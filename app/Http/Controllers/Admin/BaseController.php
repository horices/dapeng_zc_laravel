<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController as Controller;
use App\Models\GroupModel;
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
}