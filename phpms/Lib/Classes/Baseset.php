<?php

namespace Phpms\Lib\Classes;

//网站基本配置类
class Baseset {
	//文件位置
	private $file_path=APP;
	private $this_file_path;
    
    //构造方法
    public function __construct(){
		$this->file_path = $this->this_file_path;
    }
    //获取网站基本配置信息
    public function getsystem($this_file_path){

    	$content=file_get_contents($this_file_path);
    	$json_content=json_decode($content);

    	$array = array(
    		'web_name' =>$json_content->web_name,
    		'web_domain' =>$json_content->web_domain,
    		'company_name' =>$json_content->company_name,
    		'web_logo' =>$json_content->web_logo,
    		'company_address' =>$json_content->company_address,
    		'company_tphone' =>$json_content->company_tphone,
    		'company_mail' =>$json_content->company_mail,
    		'web_Record_number' =>$json_content->web_Record_number,
    		'appname' =>$json_content->appname,
    		'keyword' =>$json_content->keyword,
    		'description' =>$json_content->description,
    		'web_copyright' =>$json_content->web_copyright,
            'wlpay_user' =>$json_content->wlpay_user,
            'wlpay_id' =>$json_content->wlpay_id,
            'wlpay_key' =>$json_content->wlpay_key,
            'wlpay_appsecret' =>$json_content->wlpay_appsecret
    		);
    	return $array;
    }
    //修改配置信息保存到文件
    public function writesystem($post,$this_file_path){

    	$configText = file_get_contents($this_file_path);

    	$json_content=json_decode($configText);
 
    	$array = array(
    		'web_name' =>$json_content->web_name,
    		'web_domain' =>$json_content->web_domain,
    		'company_name' =>$json_content->company_name,
    		'web_logo' =>$json_content->web_logo,
    		'company_address' =>$json_content->company_address,
    		'company_tphone' =>$json_content->company_tphone,
    		'company_mail' =>$json_content->company_mail,
    		'web_Record_number' =>$json_content->web_Record_number,
    		'appname' =>$json_content->appname,
    		'keyword' =>$json_content->keyword,
    		'description' =>$json_content->description,
    		'web_copyright' =>$json_content->web_copyright,
            'wlpay_user' =>$json_content->wlpay_user,
            'wlpay_id' =>$json_content->wlpay_id,
            'wlpay_key' =>$json_content->wlpay_key,
            'wlpay_appsecret' =>$json_content->wlpay_appsecret   		
    		);
		//验证否修改过
    	if($array==$post){
    		return 0;
    	}else{
    		return file_put_contents($this_file_path,json_encode($post,JSON_UNESCAPED_UNICODE));
    	}
    	//JSON_UNESCAPED_UNICODE PHP版本5.4以上
    	// return $array==$post;
    }
}