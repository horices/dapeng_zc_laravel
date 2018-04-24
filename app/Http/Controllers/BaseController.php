<?php
namespace App\Http\Controllers;
 
use App\Models\GroupModel;
use App\Models\UserModel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    /***********************
     * 
     * 所有常量信息应该以方法调用方式返回
     * 
     ***********************/
    //学生量类型
    private static $_ROSTER_TYPE = [
        1   =>  'QQ',
        2 =>  '微信'
    ];
    //量类型对应的字段名
    private static $_ROSTER_TYPE_COLUMN = [
        1   =>  'qq',
        2   =>  'wx'
    ];
    //注册类型
    private static $_REGISTER_STATUS = [
        0   =>  '未注册',
        1 =>  '已注册'
    ];
    //开课状态
    private static $_COURSE_TYPE = [
        0   =>  '未开通',
        1 =>  '试学课',
        2 =>  '正式课'
    ];
    //开课状态
    private static $_COURSE_TYPE_CLOUMN_VALUE = [
        'trial' =>  1,
        'formal' =>  2
    ];
    //开课状态
    private static $_GROUP_STATUS = [
        0   =>  '无',
        1 =>  '等待进群',
        2 =>  '已进群',
        3 =>  '已退群',
        4   =>  '已拒绝',
        5   =>  '已被踢'
    ];
    //用户身份级别
    private static $_USER_GRADE = [
        4     =>  "管理员",
        5     =>  "数据员",
        9     =>  "课程顾问战队长",
        10    =>  "课程顾问",
        11    =>  "智能推广专员",
        12    =>  "推广专员",
    ];

    //私聊深度
    private static $_ROSTER_DEEP_LEVEL = [
        1   =>  '搭话',
        2   =>  '看人',
        3   =>  '挖痛',
        4   =>  '讲价值',
        5   =>  '关单'
    ];
    //用户意向
    private static $_ROSTER_INTENTION = [
        1   =>  'A',
        2   =>  'B',
        3   =>  'C',
        4   =>  'D'
    ];
    //支付方式列表
    private static $_PAY_TYPE_LIST = [
        'ALIPAY'=>'支付宝',
        'HUABEI'=>'花呗',
        'HUABEIFQ'=>'花呗分期',
        'WEIXIN'=>'微信支付',
        'MAYIFQ'=>'蚂蚁分期',
        'BANKZZ'=>'银行转账'
    ];
    //分期方式
    private static $_FQ_TYPE = [
        'NO'        =>  '无分期',
        'CASH'      =>  '现金分期',
        'HUABEI'    =>  '花呗分期',
        'MYFQ'      =>  '蚂蚁分期',
    ];
    //左侧菜单导航
    private static $_LEFT_NAV = [
        'roster_add' =>   [
            'text'=> '添加新量',   //文字描述
            'route'=> "admin.roster.add",    //链接地址
            'flag'=> 'admin.roster.add',  //默认选中标识
            'grade' =>  [4,5],          //需要展示的权限等级
        ],
        'roster_user_add' =>   [
            'text'=> '添加QQ',   //文字描述
            'route'=> "admin.roster.user.add",    //链接地址
            'flag'=> 'admin.roster.user.add',  //默认选中标识
            'grade' =>  [11,12],          //需要展示的权限等级
        ],
        'roster_user_add_wx' =>   [
            'text'=> '添加微信',   //文字描述
            'route'=> "admin.roster.user.addwx",    //链接地址
            'flag'=> 'admin.roster.user.addwx',  //默认选中标识
            'grade' =>  [11,12],          //需要展示的权限等级
        ],
        'group_list' =>   [
            'text'=> '群管理',   //文字描述
            'route'=> "admin.group.list",    //链接地址
            'flag'=> 'admin.group.list',  //默认选中标识
            'grade' =>  [4,5],          //需要展示的权限等级
        ],
        'user_list' =>   [
            'text'=> '成员管理',   //文字描述
            'route'=> "admin.user.list",    //链接地址
            'flag'=> 'admin.user.list',  //默认选中标识
            'grade' =>  [4,5],          //需要展示的权限等级
        ],
        'roster_list' =>   [
            'text'=> '所有数据',   //文字描述
            'route'=> "admin.roster.list",    //链接地址
            'flag'=> 'admin.roster.list',  //默认选中标识
            'grade' =>  [4,5],          //需要展示的权限等级
        ],
        /*'roster_list_one' =>   [
            'text'=> '指定开课',   //文字描述
            'route'=> "admin.roster.list.one",    //链接地址
            'flag'=> 'admin.roster.list.one',  //默认选中标识
            'grade' =>  [9,10],          //需要展示的权限等级
        ],*/
        'roster_list_user' =>   [
            'text'=> '我的数据',   //文字描述
            'route'=> "admin.roster.list.user",    //链接地址
            'flag'=> 'admin.roster.list.user',  //默认选中标识
            'grade' =>  [9,10,11,12],          //需要展示的权限等级
        ],
        'roster_statistics_seoer' =>   [
            'text'=> '推广统计',   //文字描述
            'route'=> "admin.roster.statistics.seoer",    //链接地址
            'flag'=> 'admin.roster.statistics.seoer',  //默认选中标识
            'grade' =>  [4,5],          //需要展示的权限等级
        ],
        'roster_statistics_adviser' =>   [
            'text'=> '顾问统计',   //文字描述
            'route'=> "admin.roster.statistics.adviser",    //链接地址
            'flag'=> 'admin.roster.statistics.adviser',  //默认选中标识
            'grade' =>  [4,5,9],          //需要展示的权限等级
        ],
        'roster_follow' =>   [
            'text'=> '销售数据',   //文字描述
            'route'=> "admin.roster.follow.index",    //链接地址
            'flag'=> 'admin.roster.follow.index',  //默认选中标识
            'grade' =>  [4,5],          //权限显示
        ],
        'roster_follow_adviser' =>   [
            'text'=> '我的销售',   //文字描述
            'route'=> "admin.roster.follow.list.user",    //链接地址
            'flag'=> 'admin.roster.follow.list.user',  //默认选中标识
            'grade' =>  [9,10],          //权限显示
        ],
        'add_pay' =>   [
            'text'=> '添加支付',   //文字描述
            'route'=> "admin.registration.add",    //链接地址
            'flag'=> 'admin.registration.add',  //默认选中标识
            'grade' =>  [4,5,9,10],          //需要展示的权限等级
        ],
        'pay_list' =>   [
            'text'=> '支付查询',   //文字描述
            'route'=> "admin.registration.list.user",    //链接地址
            'flag'=> 'admin.registration.list',  //默认选中标识
            'grade' =>  [4,5,9,10],          //需要展示的权限等级
        ],
        'pay_package' =>   [
            'text'=> '支付套餐',   //文字描述
            'route'=> "admin.pay.package.list",    //链接地址
            'flag'=> 'admin.pay.package',  //默认选中标识
            'grade' =>  [4,5],          //需要展示的权限等级
        ],
        'pay_rebate' =>   [
            'text'=> '优惠活动',   //文字描述
            'route'=> "admin.pay.rebate.list",    //链接地址
            'flag'=> 'admin.pay.rebate',  //默认选中标识
            'grade' =>  [4,5],          //需要展示的权限等级
        ],
        'accounts' =>   [
            'text'=> '个人中心',   //文字描述
            'route'=> "admin.public.account",    //链接地址
            'flag'=> 'admin.public.account',  //默认选中标识
            'grade' =>  '*',          //需要展示的权限等级
        ],
    ];

    //用户对系统的访问权限
    private static $_USER_PERMISSION = [
        '4' =>  [
            'allow'    =>  '*',
            'deny'  =>  [
                //'admin.group.list'
            ],
            'default_route'=>'admin.roster.add',
        ],
        '5' =>  [
            'allow'    =>  '*',
            'default_route'=>'admin.roster.add',
        ],
        '9' =>  [
            'allow'    =>  '*',
            'default_route'=>'admin.roster.list.user',
        ],
        '10' =>  [
            'allow'    =>  '*',
            'default_route'=>'admin.roster.list.user',
        ],
        '11' =>  [
            'allow'    =>  '*',
            'deny'  =>  [
                "admin.roster.follow.add"
            ],
            'default_route'=>'admin.roster.list.user',
        ],
        '12' =>  [
            'allow'    =>  '*',
            'deny'  =>  [
                "admin.roster.follow.add"
            ],
            'default_route'=>'admin.roster.list.user',
        ],

    ];
    /**
     * 获取用户所有权限列表
     */
    public function getUserGradeList($key = ''){
        return $key ? self::$_USER_GRADE[$key] : self::$_USER_GRADE;
    }
    /**
     * 获取左侧菜单导航
     * @return string[][]
     */
    public function getLeftNavList($key = ''){
        return $key ? self::$_LEFT_NAV[$key] : self::$_LEFT_NAV;
    }

    /**
     * 获取私聊深度
     * @param string $key
     * @return array|mixed
     */
    public function getRosterDeepLevel($key = ''){
        return $key ? self::$_ROSTER_DEEP_LEVEL[$key] : SELF::$_ROSTER_DEEP_LEVEL;
    }
    /**
     * 获取用户意向
     * @param string $key
     * @return array|mixed
     */
    public function getRosterIntention($key = ''){
        return $key ? self::$_ROSTER_INTENTION[$key] : SELF::$_ROSTER_INTENTION;
    }
    /**
     * 获取量的类型[QQ,微信]
     * @param string $key
     * @return array|mixed
     */
    public function getRosterType($key = ''){
        return $key ? self::$_ROSTER_TYPE[$key] : self::$_ROSTER_TYPE;
    }

    /**
     * 获取量类型的数据库字段名
     * @param $key
     * @return array|mixed
     */
    public function getRosterTypeColumn($key){
        return $key ? self::$_ROSTER_TYPE_COLUMN[$key]: self::$_ROSTER_TYPE_COLUMN;
    }
    /**
     * 获取注册状态描述
     * @param string $key
     * @return array|mixed
     */
    public function getRegisterStatus($key = ''){
        return $key ? self::$_REGISTER_STATUS[$key] : self::$_REGISTER_STATUS;
    }

    /**
     * 获取开课状态描述
     * @param string $key
     * @return array|mixed
     */
    public function getCourseType($key = ''){
        return $key ? self::$_COURSE_TYPE[$key] : self::$_COURSE_TYPE;
    }
    /**
     * 获取课程类型对应的值
     * @param string $key
     * @return array|mixed
     */
    public function getCourseTypeColumnValue($key = ''){
        return $key ? self::$_COURSE_TYPE_CLOUMN_VALUE[$key] : self::$_COURSE_TYPE_CLOUMN_VALUE;
    }

    /**
     * 获取群状态描述
     * @param string $key
     * @return array|mixed
     */
    public function getGroupStatus($key = ''){
        return $key ? self::$_GROUP_STATUS[$key]: self::$_GROUP_STATUS;
    }

    /**
     * 获取支付方式列表
     * @param string $key
     * @return array|mixed
     */
    public function getPayTypeList($key = ''){
        return $key ? self::$_PAY_TYPE_LIST[$key]: self::$_PAY_TYPE_LIST;
    }

    /**
     * 获取分期方式
     * @param string $key
     * @return array|mixed
     */
    public function getFqType($key = ''){
        return $key ? self::$_FQ_TYPE[$key]: self::$_FQ_TYPE;
    }

    /**
     * 获取角色权限数组
     * @param string $key
     * @return array|mixed
     */
    public function getPermission($key = ''){
        return $key ? self::$_USER_PERMISSION[$key] : self::$_USER_PERMISSION;
    }

    /**
     * 判断用户是否有权限访问当前页面,路由没有name 则全部都不允许访问
     * @param string $routeName 路由名字
     * @param integer $userGrade 用户等级
     */
    public function checkUserPermission(string $routeName,$userGrade){
        $route = explode(".",$routeName);
        $tempv = '';
        $flag = false;  //不允许列表中，默认禁止访问
        $permission = collect(self::$_USER_PERMISSION[$userGrade]);
        $allowList = collect($permission->get('allow',[]));
        $denyList = collect($permission->get('deny',[]));
        //判断当前URL是否在允许列表中
        if($allowList->contains($routeName)){
            $flag = true;
        }
        //判断当前URL是否在禁止列表中
        if($denyList->contains($routeName)){
            return false;
        }
        //判断是否属于通配符
        foreach ($route as $k=>$v){
            if(!$tempv){
                $url = '*';
            }else{
                $url = $tempv.'*';
            }
            $tempv.= $v.'.';
            //是否在允许列表中
            if($allowList->contains($url)){
                $flag = true;
            }
            if($denyList->contains($url)){
                //在禁止访问列表中时，立即停止，没有权限
                return false;
            }
        }
        return $flag;
    }

    /**
     * 获取当前登陆的用户信息
     */
    public function getUserInfo(){
        return Session::get("userInfo");
    }

    /**
     * 获取下一次应该分配的QQ组信息，成功返回数组 重新修改分配方式
     * 分配轮数分为小轮分配和大轮分配
     *  小轮分配(circle)：每个课程顾问分配数量为1,不满足条件时跳过
     *  大轮分配(round)：进行N次小轮分配后，若所有课程顾问都已达到分配数量，则表示一次大轮分配结束
     *          重新开始一次新的大轮分配(重新进行N次小轮分配)
     * @return Ambigous <mixed, boolean, NULL, string, unknown, multitype:, object>
     */
    function getNextGroupInfo($type = 1 ){
        $column  = app('status')->getRosterTypeColumn($type);

        $row = [];
        $searched = []; //已经搜索过的课程顾问
        $filename = "./logData/advisersOrderNew/advisersOrderInfo_".$column."_".date("Y_m_d").".txt";
        if(!file_exists(dirname($filename))){
            mkdir(dirname($filename),0777,true);
        }
        if(!file_exists($filename)){
            $resource = fopen($filename, "w+");
            flock($resource, LOCK_EX);
            //获取所有可用的咨询师
            $advisers = UserModel::adviser()->select('uid','grade','name','per_max_num_'.$column)->get()->toArray();
            //设置最大分配数量
            $maxCircle = 0;
            //默认当前分配数量
            foreach($advisers as $k=>$v){
                $advisers[$k]['currentNum'] = 0;    //本轮已经分配的数量
                $advisers[$k]['totalNum'] = 0;      //分配的总数量
                $advisers[$k]['perMaxNum'] = $v['per_max_num_'.$column];        //最大分配数量
                $maxCircle = max($maxCircle,$advisers[$k]['perMaxNum']);
            }
            shuffle($advisers);
            $data['orderInfo'] = $advisers;
            $data['totalAdviserNum'] = count($advisers);
            $data['totalNum'] = 0; //默认分配总量
            $data['currentKey'] =  0;//下一次应该被分配的咨询师的下标
            $data['maxCircle'] = $maxCircle;    //每次大轮中最多进行的小轮分配次数
            $data['currentRound'] = 1;    //当前总轮数
            $data['currentCircle'] = 1;  //当前分配轮数
            //file_put_contents($filename, json_encode($data,JSON_UNESCAPED_UNICODE));
            fwrite($resource, json_encode($data,JSON_UNESCAPED_UNICODE));
            flock($resource, LOCK_UN);
            fclose($resource);
        }
        unset($data,$advisers);
        $resource = fopen($filename, "a+");
        flock($resource, LOCK_EX);  //排他锁，禁止别人访问
        //获取排序规则
        $json = fread($resource, filesize($filename));
        $advisersOrderInfo = json_decode($json,true);
        $orderInfo = $advisersOrderInfo['orderInfo'];
        $currentKey = $advisersOrderInfo['currentKey'];
        //查询所有的课程顾问的群
        $groupInfo = GroupModel::opened()->where([
            'type'  =>  $type,
        ])->has("user")->get()->keyBy("leader_id");
        //最多进行指定次数的小轮循环，如果仍然没有找到合适的群，则退出，防止死循环
        for($i=0;$i<=$advisersOrderInfo['maxCircle'];$i++){
            //查询当前轮是否有合适的群
            $currentCircle = $advisersOrderInfo['currentCircle']+$i;
            //logData("当前正在进行 第 ".$currentCircle."小轮查询  当前轮:".($advisersOrderInfo['currentCircle']+$i)."===最大轮:".$advisersOrderInfo['maxCircle']);
            if(($advisersOrderInfo['currentCircle']+$i)> $advisersOrderInfo['maxCircle']){
                //说明本次大轮已经循环结束,开始下一大轮
                $advisersOrderInfo['currentRound']++;
                $currentCircle = $currentCircle%$advisersOrderInfo['maxCircle'];
            }
            //判断当前课程顾问是可以参与分量
            for($m=$advisersOrderInfo['currentKey'];$m<$advisersOrderInfo['totalAdviserNum'];$m++){
                //最后一圈时，只跑到 currentKey
                if($i == $advisersOrderInfo['maxCircle'] && $m == $currentKey){
                    $advisersOrderInfo['currentKey'] = $currentKey;
                    break 2;
                }
                if( $currentCircle <= $orderInfo[$m]['perMaxNum'] && !in_array($orderInfo[$m]['uid'],$searched)){
                    //该课程顾问参与分量,记录下次起点
                    $adviser = $orderInfo[$m]; //当前课程顾问
                    //logData("第".$currentCircle."小轮参与人:".$adviser['name']);
                    /*$tempWhere['leader_id'] = $adviser['uid'];
                    $tempWhere['status'] = 1;
                    $tempWhere['is_open'] = 1;
                    $tempWhere['type'] = $type; //判断是获取微信群还是qq群
                    $row = GroupModel::where($tempWhere)->first();*/
                    $row = $groupInfo->get($adviser['uid']);
                    if(!$row){
                        //若该课程顾问未进入到
                        $searched[] = $adviser['uid'];
                        //如果当前课程顾问没有指定类型的群，跳过
                        continue ;
                    }
                    $row = $row->toArray();
                    $row['adviser_name'] = $adviser['name'];
                    //当前课程顾问分配总数量+1
                    $advisersOrderInfo['orderInfo'][$m]['totalNum']++;
                    //如果已经匹配到课程顾问
                    $advisersOrderInfo['totalNum']++;
                    $advisersOrderInfo['currentCircle'] = $currentCircle+intval(($m+1)/$advisersOrderInfo['totalAdviserNum']);
                    $advisersOrderInfo['currentKey'] = ($m+1)%$advisersOrderInfo['totalAdviserNum'];
                    //logData("当前第".$advisersOrderInfo['currentRound']."大轮中的第". $advisersOrderInfo['currentCircle']."小轮   顾问:".$adviser['name']." 群: ".$row['qq_group'],'',false);
                    //logData("当前键:".$advisersOrderInfo['currentKey'],null,false);
                    break 2;
                }
            }
            //如果当前小轮中没有找到分配人员，重置索引,进入下一轮
            $advisersOrderInfo['currentKey'] = 0;
        }
        if($row){
            ftruncate($resource, 0);    //清空文件内容
            fwrite($resource, json_encode($advisersOrderInfo,JSON_UNESCAPED_UNICODE));
            //找到QQ组，跳出
        }
        flock($resource, LOCK_UN);
        fclose($resource);
        return $row;
    }
}

