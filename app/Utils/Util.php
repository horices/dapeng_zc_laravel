<?php 
namespace App\Utils;
class Util{
    const SUCCESS = 1;
    const FAIL = 0;
    
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