<?php

require_once 'AipSpeech.php';

function tts_apispeech($content,$file,$vol=5){
    $client = new AipSpeech('15463829', '5OKkG35LEqv15HxdGFKbgf1Q', 'KcGnkSFMDLaX66Bq8maVL7u6eOQM2Htd');
    $result = $client->synthesis($content, 'zh', 1, array(
        'vol' => $vol,
    ));
    // 识别正确返回语音二进制 错误则返回json 参照下面错误码
    if(!is_array($result)){
        file_put_contents($file, $result);
    }
    $data['status'] = 1;
    $data['msg'] = 'ok';
    $data['php'] = 'ms';
    //return $data;
    echo json_encode($data);
}
