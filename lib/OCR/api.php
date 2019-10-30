<?php

require_once 'AipOcr.php';

function ocr_apispeech($content,$file,$vol=5){
    $client = new AipOcr('15463829', '5OKkG35LEqv15HxdGFKbgf1Q', 'KcGnkSFMDLaX66Bq8maVL7u6eOQM2Htd');
    $image = file_get_contents('example.jpg');

    // 调用通用文字识别, 图片参数为本地图片
    $client->basicGeneral($image);
    
    // 如果有可选参数
    $options = array();
    $options["language_type"] = "CHN_ENG";
    $options["detect_direction"] = "true";
    $options["detect_language"] = "true";
    $options["probability"] = "true";
    
    // 带参数调用通用文字识别, 图片参数为本地图片
    $client->basicGeneral($image, $options);
    $url = "http//www.x.com/sample.jpg";
    
    // 调用通用文字识别, 图片参数为远程url图片
    $client->basicGeneralUrl($url);
    
    // 如果有可选参数
    $options = array();
    $options["language_type"] = "CHN_ENG";
    $options["detect_direction"] = "true";
    $options["detect_language"] = "true";
    $options["probability"] = "true";
    
    // 带参数调用通用文字识别, 图片参数为远程url图片
    $client->basicGeneralUrl($url, $options);
    
    $data['status'] = 1;
    $data['msg'] = 'ok';
    $data['php'] = 'ms';
    //return $data;
    echo json_encode($data);
}
