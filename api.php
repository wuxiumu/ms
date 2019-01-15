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

define('PHPMSFRAME',__DIR__);         
define('CORE',PHPMSFRAME.'/core');

include CORE.'/common/function.php';

require CORE.'/flight/Flight.php';

Flight::route('GET /api.php', function(){
    echo '会飞的php微型框架';
});

Flight::route('POST /api.php', function(){
    p($_POST);
});

Flight::route('PUT /api.php', function(){
    echo 'put请求专注于update操作';
});

Flight::route('DELETE /api.php', function(){
    echo 'DELETE';
});

Flight::route('HEAD /api.php', function(){
    echo 'HEAD';
});

Flight::route('OPTIONS /api.php', function(){
    echo 'OPTIONS';
});

Flight::route('PATCH /api.php', function(){
    echo 'PATCH';
});

Flight::route('/api.php/@name/@id:[0-9]+', function($name, $id){
	p([$name,$id]);
});
Flight::start();