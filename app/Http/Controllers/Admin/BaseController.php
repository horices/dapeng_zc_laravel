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
        $type = Input::get("type");
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
        if($type){
            $query->where("type",$type);
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
    protected function export(&$data,$query = null,$exportType = 'xls'){
        if(!collect($data)->get('data') && $query){
            $data['data'] = $query->limit(10000)->get();
        }
        //导出最长为五分钟
        set_time_limit(60*2);
        ini_set("memory_limit","100M");
        //将数组全部转化为 Collect
        if(is_array($data)){
            $data = collect($data);
        }
        $data->transform(function($v,$k){
            if(is_array($v)){
                $v = collect($v);
            }
            return $v;
        });
        //需要导出的全部数据
        $exportData = [];
        //重新整理数组,取出多级数据
        $data->get("data")->transform(function($v) use ($data,&$exportData){
            $row = [];//处理单选数据
            //判断字段中是否存在点的语法,进行多级获取
            $data->get("title")->keys()->transform(function($column) use (&$v,&$row){
                $result = $v;
                collect(explode('.',$column))->each(function($key) use (&$result){
                    if(!$result){
                        $result = '';
                        return false;
                    }
                    if($result instanceof Model){
                        $result = $result->$key;
                    }elseif($result instanceof Collection){
                        $result = $result->get($key);
                    }elseif(is_array($result)){
                        $result = $result[$key] ?? '';
                    }

                });
                $row[$column] = $result;
            });
            $exportData[] = $row;
        });
        //添加标头行
        $data->put('data',collect($exportData)->prepend($data->get("title")->toArray()));
//        $data->get("data")->prepend($data->get("title"));
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
     * @param array $column 需要分组的字段
     * @param \Closure|null $rosterWhere
     * @return mixed
     */
    protected function getStatistics(array $column = null,\Closure $rosterWhere = null){
        //若没有选择时间，则默认选择当天时间
        $request = app("request");
        if(!$request->has("startdate")){
            $request->merge(['startdate'=>date('Y-m-d 00:00:00')]);
        }
        $roster = RosterModel::query();
        $user = UserModel::query();
        $searchType = Input::get("search_type");
        $keywords = Input::get("keywords");
        $rosterType = Input::get("roster_type");
        $startDate = Input::get("startdate");
        $endDate = Input::get("enddate");
        $isReg = Input::get("is_reg");
        $courseType = Input::get("course_type");
        $groupStatus = Input::get("group_status");
        $flag = Input::get("flag");
        $dateType = Input::get("dateType",'addtime');
        $field = collect($column)->merge(['is_reg','course_type','group_status'])->filter();
        $where = [];
        if($searchType && $keywords !== null){
            if($searchType == "roster_no"){
                $roster->where("qq",$keywords)->orWhere('wx',$keywords);
            }elseif($searchType == "group_name") {
                $roster->whereHas("group",function($group) use ($keywords){
                    $group->where("group_name",$keywords);
                });
            }else{
                $where[$searchType] = $keywords;
            }
        }
        if($rosterType !== null){
            $where['type'] = $rosterType;
            //$roster->where("type",$rosterType);
        }
        if($isReg !== null){
            $where['is_reg'] = $isReg;
        }
        if($courseType !== null){
            $where['course_type'] = $courseType;
        }
        if($groupStatus !== null){
            $where['group_status'] = $groupStatus;
        }
        if($flag !== null){
            $where['flag'] = $flag;
        }
        $roster->where($where);
        if($startDate){
            $roster->where($dateType,'>=',strtotime($startDate));
        }
        if($endDate){
            $roster->where($dateType,'<',strtotime($endDate));
        }
        $query = $roster->select($field->merge([DB::raw("count(*) as num")])->toArray())->groupBy($field->toArray());
        if($rosterWhere){
            $query->where($rosterWhere);
        }
        $result = $query->get();
        $statistics = [];
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

        //计算统计通用式，返回统计数组
        $caculate = function($data){
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
            $data->each(function ($roster) use (&$temp){
                $temp['user_total'] += $roster->num ;
                //注册量
                $temp['user_total_reg_num_'.$roster->is_reg]+=$roster->num;
                //进群量
                $temp['user_total_group_num_'.$roster->group_status] += $roster->num;
                //开课量
                $temp['user_total_course_num_'.$roster->course_type] += $roster->num;
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
            return $temp;
        };
        //如果没有传入字段，则表示计算所有的数据，直接计算，返回一个数组
        if(!$column){
            $statistics = $caculate($result);
        }else{
            //如果有传入字段，则表示需要单独计算推广专员或课程顾问,返回一个二维数组，键名为用户id;
            $result->groupBy($column)->each(function($collect,$user_id) use (&$statistics,$caculate){
                $statistics[$user_id] = $caculate($collect);
            });
        }
        return $statistics;
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