<?php 
namespace App\Utils;
class Util{
    const SUCCESS = 1;
    const WARNING = 2;
    const FAIL = 0;


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
     * 判断是否在主站站点
     * @return boolean
     */
    public static function isMasterWebSite(){
        return in_array($_SERVER['HTTP_HOST'], config("app.master_host"));
    }

    /**
     *  获取大鹏主站的接口host地址
     * @return mixed
     */
    public static function getDapengHost(){
        if(self::isMasterWebSite())
            return config("app.dapeng_host")['real'];
        else
            return config("app.dapeng_host")['test'];
    }

    /**
     * 获取展翅系统接口host地址
     * @param string $schoolId
     * @return mixed
     */
    public static function getSystemHost($schoolId = "sj"){
        if(self::isMasterWebSite())
            return config('app.system_host')[$schoolId];
        else
            return config('app.system_host_test')[$schoolId];
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

}

?>