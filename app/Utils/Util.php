<?php 
namespace App\Utils;
use App\Exceptions\UserValidateException;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class Util{
    const SUCCESS = 1;
    const WARNING = 2;
    const FAIL = 0;

    const MASTER = 'MASTER';
    const TEST  = "TEST";
    const DEV = "DEV";

    const SCHOOL_NAME_MS = 'MS';
    const SCHOOL_NAME_SJ = 'SJ';

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
            $subKey = collect(config("website.HOST_ALL"))->get($host,Util::DEV);
            $key.= ".".$subKey;
        }
        return config($key,$default);
    }

    /**
     * 获取当前学院的名字
     * @param string $host
     * @param string $default
     * @return mixed
     */
    static function getSchoolName($host = "",$default = self::SCHOOL_NAME_SJ){
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

    public static function getWapHost(){
        return self::getWebSiteConfig("WAP_URL");
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

    /**
     * @note 系统加密方法
     * @param string $data 要加密的字符串
     * @param string $key  加密密钥
     * @param int $expire  过期时间 单位 秒
     * @return string
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    static function think_encrypt($data, $key = '', $expire = 0) {
        //$key    = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
        $key = "987654321zxcv";
        $key    = md5(empty($key));
        $data = base64_encode($data);
        $x    = 0;
        $len  = strlen($data);
        $l    = strlen($key);
        $char = '';
        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) $x = 0;
            $char .= substr($key, $x, 1);
            $x++;
        }
        $str = sprintf('%010d', $expire ? $expire + time():0);
        for ($i = 0; $i < $len; $i++) {
            $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
        }
        return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
    }
    /**
     * 系统解密方法
     * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
     * @param  string $key  加密密钥
     * @return string
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    static function think_decrypt($data, $key = ''){
        $key    = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
        $data   = str_replace(array('-','_'),array('+','/'),$data);
        $mod4   = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        $data   = base64_decode($data);
        $expire = substr($data,0,10);
        $data   = substr($data,10);
        if($expire > 0 && $expire < time()) {
            return '';
        }
        $x      = 0;
        $len    = strlen($data);
        $l      = strlen($key);
        $char   = $str = '';
        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) $x = 0;
            $char .= substr($key, $x, 1);
            $x++;
        }
        for ($i = 0; $i < $len; $i++) {
            if (ord(substr($data, $i, 1))<ord(substr($char, $i, 1))) {
                $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
            }else{
                $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
            }
        }
        return base64_decode($str);
    }

    /**
     * 通过新浪短连接接口返回短连接
     * @param $data
     * @return mixed
     */
    static function getShorturl($data){
        $shortUrlApi = self::getWebSiteConfig("SHORT_URL_API",0);
        $regUrl = file_get_contents($shortUrlApi.urlencode($data));
        $regUrlArr = json_decode($regUrl,1);
        return $regUrlArr[0]['url_short'];
    }

}

?>