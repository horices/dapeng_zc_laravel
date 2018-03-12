<?php
namespace App\Http\Controllers\Admin;


use App\Http\Requests\RosterAdd;
use App\Models\GroupModel;
use App\Models\RosterModel;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RosterController extends BaseController
{
    function getAdd(){
        return view("admin.roster.add");
    }

    public function postAdd(Request $request,array $data = []){
        //验证数据
        if(!$data){
            $data = $request->all();
        }
        if(!$data['qq_group_id']){
            //$groupInfo = $this->getNextGroupInfo($data['roster_type']);
            //$data['qq_group_id'] = $groupInfo['id'];
        }
        /**
         * @var $validator \Illuminate\Validation\Validator;
         */
        $validator = Validator::make($data,[
            'roster_no' =>  'required',
            'roster_type'   =>  'required|in:1,2',
            'qq_group_id'   =>  'required|exists:user_qqgroup,id',
            'seoer_id'   =>  'required|exists:user_headmaster,uid'
        ],[
            'roster_no.required' =>  '请输入号码',
            'roster_no.unique'  =>  '该号码已存在',
            'roster_type.required'  =>  '请选择正确的提交类型',
            'roster_type.in'    => "类型只能为1或2",
            'qq_group_id.required'   =>  '请选择QQ群',
            'qq_group_id.exists'   =>   'QQ群不存在',
            'seoer_id.required'  =>  '请选择推广专员',
            'seoer_id.exists'   => '推广专员不存在'
        ]);
        $rosterType = [];   //
        $rosterType['1']  =  'qq';
        $rosterType['2']  =  'wx';
        $createData = [];   //重置要添加的数据
            //该量已经存在，判断该量是否允许被添加,添加验证规则，返回 false 表示允许添加  返回 true 表示进需要行验证，不能进行添加
            $validator->sometimes('roster_no','unique:user_roster,'.$rosterType[$data['roster_type']],function($input) use($rosterType, &$roster , &$createData){
                $roster = RosterModel::where($rosterType[$input->roster_type],$input->roster_no)->orderBy('addtime','desc')->first();
                /**
                 * 默认需要验证(不能进行重复添加)
                 * 不需要验证的情况如下:
                 *  1.三无状态(未进群，未注册，未开通),今天之内不可提交
                 *  2.未注册，未开通,已被踢或已退群时时，允许立马被重新添加
                 *  3.其它所有情况不允许提交，但是添加活量标识
                 */
                //如果未找到该量的信息，则不需要验证,可直接添加
                if(!$roster){
                    return false;
                }
                $flag = 0 ;
                if($roster->course_type == 0 && $roster->is_reg == 0 ){
                    if($roster->group_status == 0  && date('Ymd') >  date('Ymd',$roster->addtime) ){
                        //第二天后可以重新提交
                        $flag = 1;
                    }
                    if($roster->group_status >= 3 ){
                        //已被踢或已退群，可以重新提交
                        $flag = 1;
                    }
                    if($flag == 1){
                        //需要对该QQ号取消所有的新量标识
                        if(RosterModel::where($rosterType[$input->roster_type],$input->roster_no)->update(['flag'=>0]) === false){
                            Log::error("取消活量标识失败");
                        }
                        $createData['flag'] = $flag;    //标识为活量
                        $createData['addtimes'] = $roster->addtimes + 1;
                        //允许添加该量
                        return false;
                    }
                }
                /**
                 * 已进群，已注册或已开通的课程，添加为活量
                 */
                if($roster->group_status == 2 || $roster->is_reg || $roster->course_type){
                    $roster->flag = 2;  //标记为活量
                    $roster->last_addtime = time(); //更新为活量的时间
                    if($roster->save() == false){
                        Log::error("标识活量失败",$roster->toArray());
                    }
                }
                return true;
            });
        //调用系统验证，验证失败时，抛出一个异常
        $validator->validate();
        $group = GroupModel::find($data['qq_group_id']);
        $createData[$rosterType[$data['roster_type']]] = $data['roster_no'];
        $createData['type'] = $data['roster_type'];
        $createData['qq_group_id'] = $data['qq_group_id'];
        $createData['qq_group'] = $group->qq_group;
        $createData['inviter_id'] = $data['seoer_id'];
        $createData['inviter_name'] = $data['seoer_name'];
        $createData['adviser_id'] = $createData['last_adviser_id'] = $group->leader_id;
        $createData['adviser_name'] = $createData['last_adviser_name'] = $group->user->name;
        $createData['addtime'] = time();
        $query = RosterModel::query();
        $roster = $query->create($createData);
        if($roster){
            $returnData['code'] = Util::SUCCESS;
            $returnData['msg'] = "添加成功";
        }else{
            $returnData['code'] = Util::FAIL;
            $returnData['msg'] = "添加失败";
        }
        return response()->json($returnData);
    }
    function getList(){
        $field_k = Input::get("field_k");
        $field_v = Input::get("field_v");
        //查询所有列表
        $query = RosterModel::query()->with(['group_info',"group_event_log"=>function($query){
            $query->select("roster_id","group_status",DB::raw("max(addtime) as addtime"))->where("group_status","=",2)->groupBy(["roster_id","group_status"])->orderBy("id","desc");
        }])->orderBy("id","desc");
        if($field_k == "account" && $field_v !== null){
            $query->where("qq","=",$field_v)->orWhere('wx','=',$field_v);
        }
        $list = $query->paginate(20);
        return view("admin.roster.list",[
            'list' => $list
        ]);
    }
}

