<?php 
namespace App\Utils;
use App\Exceptions\UserValidateException;
use App\Models\UserModel;
use Faker\Provider\bn_BD\Utils;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class Util{
    const SUCCESS = 1;
    const WARNING = 2;
    const FAIL = 0;

    const MASTER = 'MASTER';    //正式站
    const DEV = "DEV";          //测试站

    const SCHOOL_NAME_MS = 'MS';    //美术学院
    const SCHOOL_NAME_SJ = 'SJ';    //设计学院
    const SCHOOL_NAME_IT = 'IT';    //IT学院
    /**
     * 跳转到老版展翅相关
     */
    const PAY_URL_KEY   = '987654321zxcv';
    const PAY_URL_HOST   = 'http://dev.bzr.dapengjiaoyu.com';
    const PAY_URL_JUMP   = '/Member/Portal/logino';

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
            $host = Request::getHost();
            $subKey = collect(config("website.HOST_ALL"))->get($host,Util::DEV);
            $key.= ".".$subKey;
        }
        return config($key,$default);
    }

    /**
     * 获取当前分支 MASTER,DEV,TEST
     * @return mixed
     */
    static function getCurrentBranch($default = Util::DEV){
        $host = Request::getHost();
        return collect(config("website.HOST_ALL"))->get($host,$default);
    }
    /**
     * 获取当前学院的名字
     * @param string $host
     * @param string $default
     * @return mixed
     */
    static function getSchoolName($host = "",$default = self::SCHOOL_NAME_SJ){
        if(!$host)
            $host = Request::getHost();
        return collect(self::getWebSiteConfig("SCHOOL_NAME",false))->get($host,$default);
    }

    /**
     * 获取学院ID号
     * @param string $host
     * @param string $default
     * @return mixed
     */
    static function getSchoolId($host = "",$default = self::SCHOOL_NAME_SJ){
        return self::getWebSiteConfig("SCHOOL_ID.".self::getSchoolName());
    }

    /**
     * 获取学院标题
     * @param string $key
     * @return \Illuminate\Config\Repository|mixed
     */
    static function getSchoolNameText($key = ''){
        if($key){
            return self::getWebSiteConfig('SCHOOL_NAME_TEXT.'.$key,false);
        }else{
            return self::getWebSiteConfig('SCHOOL_NAME_TEXT',false);
        }
    }

    /**
     * 获取当前学院名称
     * @return \Illuminate\Config\Repository|mixed
     */
    static function getCurrentSchoolNameText(){
        return self::getSchoolNameText(self::getSchoolName());
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
     * 获取支付地址
     * @param $schoolName
     */
    public static function getPayUrl($schoolName = null){
        if(!$schoolName){
            $schoolName = Util::getSchoolName();
        }
        return self::getWebSiteConfig("PAY_URL.".$schoolName);
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
     * @return UserModel
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
        $key = self::PAY_URL_KEY;
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
        $key = self::PAY_URL_KEY;
        $key    = md5(empty($key));
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


    static function jsonEncode($data){
        return json_encode($data);
    }

    static function jsonDecode($str){
        return json_decode($str,true);
    }


    /**
     * 生成签名串
     * @param array $data
     * @return string 返回签名串
     */
    static function makeSign(array $data):string{
        ksort($data);
        $str = "";
        $data = collect($data)->except("sign");
        foreach ($data as $k=>$v){
            if($k && $v !== '' && $v !== null){
                $str.=$k.$v;
            }
        }
        return Str::upper(md5($str));
    }

    /**
     * 获取签名数据
     * @param array $data
     * @return array
     */
    static function getSignData(array $data):array{
        $data['sign'] = self::makeSign(collect($data)->except("sign"));
        return $data;
    }

}

?>