<?php
include "TopSdk.php";
date_default_timezone_set('Asia/Shanghai'); 

$c = new TopClient;
$c->appkey = '23343313';
$c->secretKey = '0213a127e48f3f77dde73895718665ca';

$req = new AlibabaAliqinFcSmsNumSendRequest;
//$req->setExtend("123456");	
$req->setSmsType("normal");
$req->setSmsFreeSignName("大鱼测试");
$req->setSmsParam("{'code': '1234', 'limit': '30'}");
$req->setRecNum("18010471576");
$req->setSmsTemplateCode("SMS_7490142");
var_dump($resp = $c->execute($req));

?>