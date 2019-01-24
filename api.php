<?php

define('PHPMSFRAME',__DIR__);         
define('CORE',PHPMSFRAME.'/core');
 
include CORE.'/common/function.php';

require CORE.'/flight/Flight.php';

Flight::route('POST /api.php/baidu_tts/v1', function(){
    //版本一，一次最多512个汉字
    require PHPMSFRAME.'/app/extend/yuyin/api.php';
    $id = $_POST['id']; 
    $name= $_POST['name']; 
    $content= mb_substr($_POST['content'],0,512,'utf-8'); 
    $file = PHPMSFRAME.'/public/media/baidu_tts/post_'.$id.".mp3";
    tts_apispeech($content,$file,$vol=5);
});
Flight::route('POST /api.php/baidu_tts/v2', function(){
    //版本二，一次最多20048个汉字
    require PHPMSFRAME.'/app/extend/yuyin/tts.php';
    $id = $_POST['id']; 
    $name= $_POST['name']; 
    $_num = floor(mb_strlen($_POST['content'])/2048);    
    $content= mb_substr($_POST['content'],0,2048,'utf-8'); 
    $file = PHPMSFRAME.'/public/media/baidu_tts/post_'.$id.".mp3";
    $re = baidu_tts($content,$file);    
    echo json_encode($re);
});

Flight::route('GET /api.php/OCR/@url', function($url){    
    require PHPMSFRAME.'/app/extend/OCR/api.php';
    $client = new AipOcr('15465241', 'yLMHVyCWg64RRbVVlRI8GGP5', 'PyGbkuDvmFvn1hDctxpfgfwOkBPLCPr9');
    $image = file_get_contents('phptime.jpg');
    // 调用通用文字识别（高精度版）
    $client->basicAccurate($image);    
    // 如果有可选参数
    $options = array();
    $options["detect_direction"] = "true";
    $options["probability"] = "true";    
    // 带参数调用通用文字识别（高精度版）
   $re = $client->basicAccurate($image, $options);
   switch($url){
        case 'json':            
            $arr2=object_to_array($re);//先把对象转化为数组 
            header('Content-type:text/json');              
            echo json_encode($arr2,JSON_UNESCAPED_UNICODE);
            break;
        case 'dump':            
            var_dump($re);
            break;    
        default:
            p($re);
            break;
   }
});

Flight::route('GET /api.php', function(){
    echo 'DEMO:/api.php/OCR/@type?url=http://xxx.com/phptime.jpg<br>';
    echo 'type:/josn/dump/default<br>';
    echo 'url=文字图片地址<br>';
    echo "test:<a href='/api.php/OCR/json?url=http://wqbms.com/phptime.jpg'>phptime.jpg</a><br>";
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