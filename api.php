<?php
/***
 * ░░░░░░░░░░░░░░░░░░░░░░░░▄░░
 * ░░░░░░░░░▐█░░░░░░░░░░░▄▀▒▌░
 * ░░░░░░░░▐▀▒█░░░░░░░░▄▀▒▒▒▐
 * ░░░░░░░▐▄▀▒▒▀▀▀▀▄▄▄▀▒▒▒▒▒▐
 * ░░░░░▄▄▀▒░▒▒▒▒▒▒▒▒▒█▒▒▄█▒▐
 * ░░░▄▀▒▒▒░░░▒▒▒░░░▒▒▒▀██▀▒▌
 * ░░▐▒▒▒▄▄▒▒▒▒░░░▒▒▒▒▒▒▒▀▄▒▒
 * ░░▌░░▌█▀▒▒▒▒▒▄▀█▄▒▒▒▒▒▒▒█▒▐
 * ░▐░░░▒▒▒▒▒▒▒▒▌██▀▒▒░░░▒▒▒▀▄
 * ░▌░▒▄██▄▒▒▒▒▒▒▒▒▒░░░░░░▒▒▒▒
 * ▀▒▀▐▄█▄█▌▄░▀▒▒░░░░░░░░░░▒▒▒
 * 今天是2019年1月15号，单身狗就这样默默地看着你，告诉你这是API入口文件。
 */
header("content-type:text/html;charset=utf-8");  //设置编码
define('PHPMSFRAME',__DIR__);                    //当前内容写在哪个文件就显示这个文件目录
define('CORE',PHPMSFRAME.'/core');

require CORE.'/flight/Flight.php';
require PHPMSFRAME.'/nosql/Redis.php';

Flight::route('/api.php', function(){
	 // $new = new Redisengine;
	 // //$new->sscache();
	 // $new->olfst();
	 test2();
});
Flight::start();

function test2(){
	echo 1;
}
function test1(){
	$redis = new RedisClusteri;

	$redis->connect(array('host'=>'127.0.0.1','port'=>6379));


	//*
	$cron_id = 10001;
	$CRON_KEY = 'CRON_LIST'; //
	$PHONE_KEY = 'PHONE_LIST:'.$cron_id;//

	//cron info
	$cron = $redis->hget($CRON_KEY,$cron_id);
	if(empty($cron)){

	  $cron = array('id'=>10,'name'=>'jackluo');//mysql data
	  $redis->hset($CRON_KEY,$cron_id,$cron); // set redis
	}
	//phone list
	$phone_list = $redis->lrange($PHONE_KEY,0,-1);
	print_r($phone_list);
	if(empty($phone_list)){
	  $phone_list =explode(',','13228191831,18608041585');    //mysql data
	  //join  list
	  if($phone_list){
	    $redis->multi();
	    foreach ($phone_list as $phone) {
	      $redis->lpush($PHONE_KEY,$phone);
	    }
	    $redis->exec();
	  }
	}

	print_r($phone_list);
}