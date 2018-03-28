<?php
include "TopSdk.php";
date_default_timezone_set('Asia/Shanghai'); 

function sendSMS($mobile, $sign, $tpl, $paramStr){
	if(!$mobile || !$sign || !$tpl || !$paramStr) return 'SmsParam Error';

	$c = new TopClient;
	$c->appkey = '23384459';
	$c->secretKey = 'e0009ed81bdf96f55bcda82c45e9d621';
	$c->format = 'json';

	$req = new AlibabaAliqinFcSmsNumSendRequest;

	$req->setSmsType("normal");
	$req->setSmsFreeSignName($sign);
	$req->setSmsParam($paramStr);
	$req->setRecNum($mobile);
	$req->setSmsTemplateCode($tpl);

	return $c->execute($req);
}