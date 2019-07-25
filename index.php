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
 * 今天是2019年1月11号，单身狗就这样默默地看着你，告诉你这是入口文件。
 */
header("content-type:text/html;charset=utf-8");  //设置编码
define('PHPMSFRAME',__DIR__);                    //当前内容写在哪个文件就显示这个文件目录
define('CORE',PHPMSFRAME.'/core');
define('APP',PHPMSFRAME.'/app');
define('MODULE','app');

define('DEBUG',true);

include "vendor/autoload.php";

// 价值配置文件env
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

// 开发模式，提供更多的错误信息
if(DEBUG){
	$whoops = new \Whoops\Run;
	$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
	$whoops->register();
	ini_set('display_error', 'On');
}else{
	ini_set('display_error', 'Off');
}

include CORE.'/common/function.php';

include CORE.'/phpmsframe.php';

spl_autoload_register('\core\phpmsframe::load');

\core\phpmsframe::run();