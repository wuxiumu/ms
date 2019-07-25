<?php

// 打印数据 
function p($var){ echo "<pre>"; print_r($var); echo "</pre>";}

// 跳转 
function js_u($url,$time=0,$msg=''){	
	sleep($time);//调用了sleep()方法,效果也是x秒后执行跳转
	echo "<script language='javascript' type='text/javascript'>"; 
	if(!empty($msg)){
		echo "alert('".$msg."');";
	}
	echo "window.location.href='$url'"; 
	echo "</script>"; 
}

// 对象转数组
function object_to_array($obj){  
    if(is_array($obj)){  
        return $obj;  
    }  
    $_arr = is_object($obj)? get_object_vars($obj) :$obj;  
    foreach ($_arr as $key => $val){  
    $val=(is_array($val)) || is_object($val) ? object_to_array($val) :$val;  
    $arr[$key] = $val;  
    }  
    return $arr;     
}  
 
// 验证码
use Gregwar\Captcha\CaptchaBuilder as CaptchaBuilder;
function mycode(){	
    $builder = new CaptchaBuilder();
    $builder->build();		
    setcookie('phrase', $builder->getPhrase());		 
    header('Content-type: image/jpeg');
    $builder->output();
}	