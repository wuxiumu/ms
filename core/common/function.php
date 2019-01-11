<?php

//------打印数据-----
function p($var){ echo "<pre>"; print_r($var); echo "</pre>";}

//------跳转------
function js_u($url,$time=0,$msg=''){	
	sleep($time);//调用了sleep()方法,效果也是x秒后执行跳转
	echo "<script language='javascript' type='text/javascript'>"; 
	if(!empty($msg)){
		echo "alert('".$msg."');";
	}
	echo "window.location.href='$url'"; 
	echo "</script>"; 
}