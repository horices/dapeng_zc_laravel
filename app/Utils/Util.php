<?php 
namespace App\Utils;
use App\Exceptions\UserValidateException;
use Illuminate\Support\Facades\Session;

class Util{
    const SUCCESS = 1;
    const WARNING = 2;
    const FAIL = 0;

    const MASTER = 'MASTER';
    const TEST  = "TEST";
    const DEV = "DEV";


    /**
     * @note json返回数据
     * @param $status
     * @param string $msg
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function ajaxReturn($status,$msg="",$data=[]){
        if(is_array($status)){
            $return  = $status;
        }else{
            $return['code'] = $status;
            $return['msg'] = $msg;
            $return['data'] = $data;
        }
        return response()->json($return);
    }

    static function setDefault(&$key, $val) {
        if ($key === null || $key === "") {
            $key = $val;
        }
    }

    /**
     * 获取website 配置文件中的配置
     * @param $key
     * @param int $getSub 是否根据获取相应的正式或测试数据
     */
    static function getWebSiteConfig($key,$getSub = 1,$default = ""){
        $key = "website.".$key;
        if($getSub){
            $host = $_SERVER['HTTP_HOST'];
            $subKey = collect(config("website.HOST_ALL"))->get($host,Util::TEST);
            $key.= ".".$subKey;
        }
        return config($key,$default);
    }

    /**
     * 获取当前学院的名字
     * @param string $host
     * @return mixed
     */
    static function getSchoolName($host = "",$default = "SJ"){
        if(!$host)
            $host = $_SERVER['HTTP_HOST'];
        return collect(self::getWebSiteConfig("SCHOOL_NAME",false))->get($host,$default);
    }
    /**
     *  获取大鹏主站的接口host地址
     * @return mixed
     */
    public static function getDapengHost(){
        return self::getWebSiteConfig("PC_URL");
    }

    /**
     * 获取展翅系统接口host地址
     * @param string $schoolId
     * @return mixed
     */
    public static function getSystemHost($schoolId = "sj"){
        return self::getWebSiteConfig("ZC_URL.".$schoolId);
    }

    /**
     * 添加图片域名
     * @param unknown $url
     * @param string $host
     * @return string
     */
    static function getImgUrl($url, $host = null) {
        if ($url && strpos($url, "http") !== 0) {
            $url = $host . $url;
        }
        return $url;
    }
    
    /**
     * 获取图片二维码解析后的内容
     * @param unknown $image
     */
    static function getQrContent(String $image) : string {
        $result = "";
        if(file_exists($image)){
            $QrReader = new \QrReader($image);
            $result = $QrReader->text();
        }
        return $result;
    }


    /**
     * @note 验证手机号格式
     * @param $mobile
     * @return bool
     */
    static function checkMobileFormat($mobile){
        if(preg_match("/^(1[0-9][0-9])\\d{8}$/",$mobile)){
            return true;
        }
        return false;
    }

    /**
     * 获取当前登录用户的信息
     * @return mixed
     */
    static function getUserInfo(){
        return session()->get("userInfo");
    }

    /**
     * 引入 vendor 目录下的插件
     * @param $name
     */
    static function vendor($name,$ext = ".php"){
       $arr = explode(".",$name);
       $url = __DIR__."/vendor";
       foreach ($arr as $v){
           $url.= "/".$v;
       }
       $url.=$ext;
       include $url;
    }

    /**
     * 发送短信接口
     * @param $mobile
     * @param string $type
     */
    static function sendSms($mobile , $type="REGISTER"){
        self::vendor("alidayu.sendSMS");
        $data = [
            'code' => strval(mt_rand(1000, 9999)),
            'limit_time' => '30'
        ];
        $ret = (array) sendSMS(strval($mobile), '大鹏教育', 'SMS_11540677', json_encode($data));
        //$ret = json_decode($ret,true);
        if(!$ret || !isset($ret['result'])){
            throw new UserValidateException("短信发送失败:".$ret['sub_msg']);
        }
        Session::put("sms_code",$data['code']);
        Session::put("sms_code_expire_time",time()+60*30);
    }
}

?>