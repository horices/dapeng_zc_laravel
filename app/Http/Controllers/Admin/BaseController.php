<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController as Controller;
use App\Models\GroupModel;
use App\Models\RosterModel;
use App\Models\UserModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;

class BaseController extends Controller{

    /**
     * 检索所有的推广专员
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function getSelectSeoer(){
        //查询所有的推广专员
        $query = UserModel::seoer();
        $name = Input::get("name");
        if($name){
            $query->where("name","like","%".$name."%");
        }
        $list = $query->paginate(5);
        return view("admin.public.select_seoer",[
            'list' => $list
        ]);
    }
    /**
     * 检索所有的课程顾问
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function getSelectAdviser(){
        //查询所有的推广专员
        $query = UserModel::adviser();
        $name = Input::get("name");
        if($name){
            $query->where("name","like","%".$name."%");
        }
        $list = $query->paginate(5);
        return view("admin.public.select_seoer",[
            'list' => $list
        ]);
    }

    /**
     * 选择QQ群
     */
    function getSelectGroup(){
        $group = Input::get("group");
        $groupName = Input::get("group_name");
        $adviserName = Input::get("adviser_name");
        $query = GroupModel::with("user")->whereHas('user' ,function($query) use ($adviserName){
            if($adviserName){
                $query->where("name","like","%".$adviserName."%");
            }
        });
        if($group){
            $query->where("qq_group",$group);
        }
        if($groupName){
            $query->where("group_name",$groupName);
        }
        $list = $query->paginate(5);
        return view("admin.public.select_group",[
            'list'  => $list
        ]);
    }

    /**
     * 导出数据，到指定的文件
     * @param array $data
     *      filename :  导出的文件名
     *      title :     字段名,支持点 多级选择
     *      data    : 数据
     */
    protected function export(array &$data,$exportType = 'xls'){
        //导出最长为五分钟
        set_time_limit(60*2);
        ini_set("memory_limit","100M");
        //将数组全部转化为 Collect
        $data = collect($data);
        $data->transform(function($v,$k){
            if(is_array($v)){
                $v = collect($v);
            }
            return $v;
        });
        //重新整理数组,取出多级数据
        $data->get("data")->transform(function($v) use ($data){
            //判断字段中是否存在点的语法,进行多级获取
            $data->get("title")->keys()->each(function($column) use (&$v){
                if(strpos($column,'.') !== false){
                    collect(explode('.',$column))->each(function($key) use (&$v,$column){
                        if($v instanceof Model){
                            if(!$v->$column) $v->$column = $v;
                            $v->$column = $v->$column->$key;
                        }elseif($v instanceof Collection){
                            $v->put($column,$v->get($key));
                        }else{
                            $v[$column] = $v[$key];
                        }
                    });
                }
            });
            //重新排序
            $temp = [];
            $data->get("title")->keys()->each(function($key) use (&$temp, $v){
                $temp[] =  collect($v)->get($key);
            });
            return $temp;
            //return collect($v->toArray())->only($data->get("title")->keys());
        });

        //添加标头
        $data->get("data")->prepend($data->get("title"));
        Excel::create($data->get("filename"), function($excel) use ($data) {
            /**
             * @var $excel LaravelExcelWriter
             */
            $excel->sheet('Sheetname', function($sheet) use ($data) {
                /**
                 * @var $sheet  LaravelExcelWorksheet
                 */
                $sheet->fromArray($data->get("data")->toArray(),'','','',false);
            });
        })->export($exportType);
    }


    /**
     * 通用处理统计
     * @param array $column
     * @param \Closure|null $rosterWhere
     * @return mixed
     */
    protected function getStatistics(array $column,\Closure $rosterWhere = null){
        $roster = RosterModel::query();
        $user = UserModel::query();
        $searchType = Input::get("searchType");
        $keywords = Input::get("keywords");
        $rosterType = Input::get("roster_type");
        $startDate = Input::get("startdate");
        $endDate = Input::get("enddate");
        $field = collect($column)->merge(['is_reg','course_type','group_status'])->filter();
        if($searchType && $keywords !== null){
            $user->where($searchType,$keywords);
        }
        if($rosterType){
            $roster->where("type",$rosterType);
        }
        if($startDate){
            $roster->where('addtime','>=',strtotime($startDate));
        }
        if($endDate){
            $roster->where('addtime','<',strtotime($endDate));
        }
        $query = $roster->select($field->merge([DB::raw("count(*) as num")])->toArray())->groupBy($field->toArray());
        if($rosterWhere){
            $query->where($rosterWhere);
        }
        $result = $query->get();
        $statistics = $user_statistics = [];
        $statistics['user_total'] = 0; //所有数据总量
        $statistics['user_total_reg_num_0'] = 0;   //未注册总人数
        $statistics['user_total_reg_num_1'] = 0;   //已注册总人数
        $statistics['user_total_group_num_0'] = 0; //未申请总人数
        $statistics['user_total_group_num_1'] = 0; //等待通过总人数
        $statistics['user_total_group_num_2'] = 0; //已经进群总人数
        $statistics['user_total_group_num_3'] = 0; //已经退群总人数
        $statistics['user_total_group_num_4'] = 0; //已拒绝总人数
        $statistics['user_total_group_num_5'] = 0; //已被踢总人数
        $statistics['user_total_course_num_0'] = 0;    //未开通课程总人数
        $statistics['user_total_course_num_1'] = 0;    //开通试学课总人数
        $statistics['user_total_course_num_2'] = 0;    //开通正式课总人数
        $result->groupBy($column)->each(function($collect,$user_id) use (&$statistics,&$user_statistics){
            $temp['user_total'] = 0; //所有数据总量
            $temp['user_total_reg_num_0'] = 0;   //未注册总人数
            $temp['user_total_reg_num_1'] = 0;   //已注册总人数
            $temp['user_total_group_num_0'] = 0; //未申请总人数
            $temp['user_total_group_num_1'] = 0; //等待通过总人数
            $temp['user_total_group_num_2'] = 0; //已经进群总人数
            $temp['user_total_group_num_3'] = 0; //已经退群总人数
            $temp['user_total_group_num_4'] = 0; //已拒绝总人数
            $temp['user_total_group_num_5'] = 0; //已被踢总人数
            $temp['user_total_course_num_0'] = 0;    //未开通课程总人数
            $temp['user_total_course_num_1'] = 0;    //开通试学课总人数
            $temp['user_total_course_num_2'] = 0;    //开通正式课总人数
            //提交总人数
            $collect->each(function ($user) use (&$statistics,&$temp){
                $temp['user_total'] += $user->num ;
                $statistics['user_total'] += $user->num;
                //注册量
                $temp['user_total_reg_num_'.$user->is_reg]+=$user->num;
                $statistics['user_total_reg_num_'.$user->is_reg] += $user->num;
                //进群量
                $temp['user_total_group_num_'.$user->group_status] += $user->num;
                $statistics['user_total_group_num_'.$user->group_status] += $user->num;
                //开课量
                $temp['user_total_course_num_'.$user->course_type] += $user->num;
                $statistics['user_total_course_num_'.$user->course_type] += $user->num;
            });
            //进群量的在群量+退群量+被踢量
            $temp['user_total_join_group'] = $temp['user_total_group_num_2'] + $temp['user_total_group_num_3'] +$temp['user_total_group_num_5'];
            //进群比例
            if($temp['user_total'])
                $temp['user_total_join_group_percent'] = (round($temp['user_total_join_group']/$temp['user_total'],4)*100)."%";
            if($temp['user_total_join_group']){
                //退群比例
                $temp['user_total_quit_group_percent'] = (round($temp['user_total_group_num_3']/$temp['user_total_join_group'],4)*100)."%";
                //被踢比例
                $temp['user_total_kick_group_percent'] = (round($temp['user_total_group_num_5']/$temp['user_total_join_group'],4)*100)."%";
                //注册比例
                $temp['user_total_reg_percent'] = (round($temp['user_total_reg_num_1']/$temp['user_total_join_group'],4)*100)."%";
                //试学比例
                $temp['user_total_trial_course_percent'] = (round($temp['user_total_course_num_1']/$temp['user_total_join_group'],4)*100)."%";
                //正课比例
                $temp['user_total_formal_course_percent'] = (round($temp['user_total_course_num_2']/$temp['user_total_join_group'],4)*100)."%";
            }
            $user_statistics[$user_id] = $temp;
        });
        //进群量的在群量+退群量+被踢量
        $statistics['user_total_join_group'] = $statistics['user_total_group_num_2'] + $statistics['user_total_group_num_3'] +$statistics['user_total_group_num_5'];
        //进群比例
        if($statistics['user_total'])
            $statistics['user_total_join_group_percent'] = (round($statistics['user_total_join_group']/$statistics['user_total'],4)*100)."%";
        if($statistics['user_total_join_group']){
            //退群比例
            $statistics['user_total_quit_group_percent'] = (round($statistics['user_total_group_num_3']/$statistics['user_total_join_group'],4)*100)."%";
            //被踢比例
            $statistics['user_total_kick_group_percent'] = (round($statistics['user_total_group_num_5']/$statistics['user_total_join_group'],4)*100)."%";
            //注册比例
            $statistics['user_total_reg_percent'] = (round($statistics['user_total_reg_num_1']/$statistics['user_total_join_group'],4)*100)."%";
            //试学比例
            $statistics['user_total_trial_course_percent'] = (round($statistics['user_total_course_num_1']/$statistics['user_total_join_group'],4)*100)."%";
            //正课比例
            $statistics['user_total_formal_course_percent'] = (round($statistics['user_total_course_num_2']/$statistics['user_total_join_group'],4)*100)."%";
        }
        $data['user_statistics'] = $user_statistics;
        $data['statistics'] = $statistics;
        return $data;
    }

    function exportStatisticsList(&$list){
        $data['filename'] = '效率统计';
        $data['title'] = [
            'name'  =>  '姓名',
            'user_total'    =>  '数据量',
            'user_total_join_group' =>  '进群量',
            'user_total_join_group_percent' =>  '进群比例',
            'user_total_group_num_3'    =>  '退群量',
            'user_total_quit_group_percent' =>  '退群比例',
            'user_total_group_num_5'    =>  '被踢量',
            'user_total_kick_group_percent' =>  '被踢比例',
            'user_total_reg_num_1'  =>  '注册量',
            'user_total_reg_percent'    =>  '注册比例',
            'user_total_course_num_1'   =>  '试学量',
            'user_total_trial_course_percent'   =>  '试学比例',
            'user_total_course_num_2'   =>  '正课量',
            'user_total_formal_course_percent'  =>  '正课比例'
        ];
        $data['data'] = $list;
        return $this->export($data);
    }
}