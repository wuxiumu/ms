<?php
header("content-type:text/html;charset=utf-8");  //设置编码
define('PHPMSFRAME',dirname(__DIR__));           //当前内容写在哪个文件就显示这个文件目录

define('CORE',PHPMSFRAME.'/phpms');
define('APP',PHPMSFRAME.'/app/backstage');
define('MODULE','App\backstage');

define('ADMIN_RES_PATH','public/admin_res_path/'); //后台样式

define('DEBUG',true);                	     	   //是否打开报错信息
define('STRICT',false);                  		   //是否开启大小写严格模式
 
include dirname(__DIR__)."/vendor/autoload.php";

if(DEBUG){
	$whoops = new \Whoops\Run;
	$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
	$whoops->register();
	ini_set('display_error', 'On');
}else{
	ini_set('display_error', 'Off');
}

include CORE.'/Common/function.php';

include CORE.'/PhpmsFrame.php';

spl_autoload_register('Phpms\PhpmsFrame::load');

Phpms\PhpmsFrame::run();