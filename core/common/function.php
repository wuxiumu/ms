<?php
/* ========================================================================
 * 全局函数
 * ======================================================================== */
// 打印数据 
function p($var){ echo "<pre>"; print_r($var); echo "</pre>";}

function debug(...$var)
{
    if (function_exists('dump')) {
        array_walk($var, function ($v) {
            dump($v);
        });
    } else {
        array_walk($var, function ($v) {
            print_r($v);
        });
    }
    exit();
}

function json($array)
{
    header('Content-Type:application/json; charset=utf-8');
    echo json_encode($array);
}

function show404()
{
    header('HTTP/1.1 404 Not Found');
    header("status: 404 Not Found");
    exit();
}

// 跳转 
function redirect($str){ header('Location:' . $str); }

function js_redirect_time_msg($url,$time=0,$msg=''){	
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