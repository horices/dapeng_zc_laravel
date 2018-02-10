<?php 
namespace App\Utils;
class Util{
    const SUCCESS = 1;
    const WARNING = 2;
    const FAIL = 0;


    /**
     * 输出各种类型的数据，调试程序时打印数据使用。
     * @param    mixed    参数：可以是一个或多个任意变量或值
     */
    static function p() {
        $args = func_get_args();  //获取多个参数
        /*	if(count($args)<1){
                Debug::addmsg("<font color='red'>必须为p()函数提供参数!");
                return;
            }*/
        echo '<div style="width:100%;text-align:left"><pre>';
        //多个参数循环输出
        foreach ($args as $arg) {
            if (is_array($arg)) {
                print_r($arg);
                echo '<br>';
            } else if (is_string($arg)) {
                echo $arg . '<br>';
            } else {
                var_dump($arg);
                echo '<br>';
            }
        }
        echo '</pre></div>';
    }
    /**
     * @note json返回数据
     * @param $status
     * @param string $msg
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function ajaxReturn($status,$msg="",$data=[]){
        return response()->json(['code'=>$status,'msg'=>$msg,'data'=>$data]);
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
}

?>