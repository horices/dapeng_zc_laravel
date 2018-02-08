<?php
namespace App\Http\Controllers;
 
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    /***********************
     * 
     * 所有常量信息应该以方法调用方式返回
     * 
     ***********************/
    //量类型
    private static $_ROSTER_TYPE = [
        1   =>  'QQ',
        '2' =>  '微信'
    ];
    //用户身份级别
    private static $_USER_GRADE = [
        '4'     =>  "管理员",
        '5'     =>  "数据员",
        '9'     =>  "课程顾问战队长",
        '10'    =>  "课程顾问",
        '11'    =>  "机器人推广专员",
        '12'    =>  "推广专员",
    ];
    
    //左侧菜单导航
    private static $_LEFT_NAV = [
        'roster_add' =>   [
            'text'=> '添加新量',   //文字描述
            'url'=> "/admin/group/list",    //链接地址
            'flag'=> 'group_list',  //默认选中标识
        ],
        'group_list' =>   [
            'text'=> 'QQ群管理',   //文字描述
            'url'=> "/admin/group/list",    //链接地址
            'flag'=> 'group_list',  //默认选中标识
        ],
        'user_list' =>   [
            'text'=> '成员管理',   //文字描述
            'url'=> "/admin/user/list",    //链接地址
            'flag'=> 'group_list',  //默认选中标识
        ],
        'roster_all' =>   [
            'text'=> '所有数据',   //文字描述
            'url'=> "/admin/group/list",    //链接地址
            'flag'=> 'group_list',  //默认选中标识
        ],
        'statistics' =>   [
            'text'=> '效率统计',   //文字描述
            'url'=> "/admin/group/list",    //链接地址
            'flag'=> 'group_list',  //默认选中标识
        ],
        'add_pay' =>   [
            'text'=> '添加支付',   //文字描述
            'url'=> "/admin/registration/add",    //链接地址
            'flag'=> 'group_list',  //默认选中标识
        ],
    ];
    
    /**
     * 获取用户所有权限列表
     */
    public function getUserGradeList(){
        return self::$_USER_GRADE;
    }
    /**
     * 获取左侧菜单导航
     * @return string[][]
     */
    public function getLeftNavList(){
        return self::$_LEFT_NAV;
    } 
    public function getRosterType(){
        return self::$_ROSTER_TYPE;
    }
    
    /**
     * 获取当前登陆的用户信息
     * @param Request $request
     * @return mixed|\ArrayAccess[]|array[]|\ArrayAccess|array|Closure
     */
    public function getUserInfo(Request $request){
        return $request->session()->get("userInfo");
    }
}

