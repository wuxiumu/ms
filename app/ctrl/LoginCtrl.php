<?php

namespace app\ctrl;

use core\lib\Model;

use Gregwar\Captcha\CaptchaBuilder as CaptchaBuilder;

class LoginCtrl extends BaseController
{
	public $encryptMethod = 'aes-256-cbc';//加密算法
	//用户注册页
	public function user_register(){    	
    	$this->set_token(); 
    	$this->assign('token',$_SESSION['token']);
    	$this->display('userregister.html');
    }
    public function user_register_action(){
    	//验证token    	  
    	$arr = $_POST;     	
    	if($_SESSION['token']!=$_POST['token']){
    		js_u('/index.php/login/user_register#register');exit;
    	}
    	unset($arr['token']);
    	if($_COOKIE['phrase']!=$_POST['code']){
    		js_u('/index.php/login/user_register#register',3,'验证码有误');exit;
    	}    	
    	unset($arr['code']); 
    	$arr['password'] = $this->encrypt($_POST['password']);
    	$time = date("Y-m-d H:i:s",time());  
		$arr['created_at'] = $time;
        $arr['updated_at'] = $time;
		$model = new \app\model\userModel();
		$re = $model->adduser($arr);		
		$error_arr = $re->errorInfo();
		if($error_arr['0']=='00000'){
		   js_u('/index.php/login/user#login',3,'注册成功');
		}else{
		   dump($error_arr);         				
		}
    }

    //用户登录页
    public function user(){    	
    	$this->set_token(); 
    	$this->assign('token',$_SESSION['token']);
    	$this->display('userlogin.html');
    }
    public function user_login_action(){    	
    	if($_SESSION['token']!=$_POST['token']){
    		js_u('/index.php/login/user#login');exit;
    	}
    	unset($_POST['token']);
    	if($_COOKIE['phrase']!=$_POST['code']){
    		js_u('/index.php/login/user#login',3,'验证码有误');exit;
    	}
    	$name = $_POST['name'];
    	$model = new \app\model\UserModel();
		$re = $model->finduser(['name'=>$name]);
		$flag = FALSE;	
		//结果不为空 ，解密
		if(!empty($re['password'])){
			$password = $this->decrypt($re['password']);
			if($password == $_POST['password']){
				$flag = TRUE;
			}
		}
    	if($flag){
    		unset($re['password']);
    		$user = $re;
    		$user['login_status'] = 1;
    		$_SESSION['user'] = $user;     			
		    $_SESSION['user']['set']=array("skin"=>'skin-blur-yellow');		
    		js_u('/index.php/edit/index');    		
    	}else{
    		js_u('/index.php/login/user#login');
    	}
    }

	public function set_token() { 
	     $_SESSION['token'] = md5(microtime(true)); 
	} 
	    
	public function valid_token() { 
	     $return = $_REQUEST['token'] === $_SESSION['token'] ? true : false; 
	     $this->set_token(); 
	     return $return; 
	} 	  
    public function login()
    {
    	echo 1;
    	/*session_start([
		    'cache_limiter' => 'private', //在读取完毕会话数据之后马上关闭会话存储文件
		    'cookie_lifetime'=>3600,   //SessionID在客户端Cookie储存的时间，默认是0，代表浏览器一关闭SessionID就作废
		    'read_and_close'=>true   //在读取完会话数据之后， 立即关闭会话存储文件，不做任何修改
		]);*/
		// $name=$_POST['name'];
		// $password=$this->encrypt($_POST['password']);
		// var_dump($password);
  		//       var_dump($_POST);
    }
	//用户退出登陆
	public function user_loginout(){		
		session_destroy();
		js_u('/index.php/edit/index');   
	}

    //加密
    private function encrypt($originalData){
	    $publicKeyFilePath = APP.'/rsa/rsa_public_key.pem';
	    extension_loaded('openssl') or die('php需要openssl扩展支持');
	    file_exists($publicKeyFilePath) or die('公钥的文件路径不正确');
	    $publicKey = openssl_pkey_get_public(file_get_contents($publicKeyFilePath));
	    $publicKey or die('公钥不可用');
	    $crypto = '';
	    foreach (str_split($originalData, 117) as $chunk) {
	        $encryptData = '';
	        if(openssl_public_encrypt($chunk, $encryptData, $publicKey)){
	            $crypto .= $encryptData;
	        }else{
	            die('加密失败');
	        }
	    }
	    return base64_encode($crypto);
	}

	//解密
	private function decrypt($encryptData){
	    $privateKeyFilePath = APP.'/rsa/rsa_private_key.pem';
	    extension_loaded('openssl') or die('php需要openssl扩展支持');
	    file_exists($privateKeyFilePath) or die('密钥的文件路径不正确');
	    $privateKey = openssl_pkey_get_private(file_get_contents($privateKeyFilePath));
	    $privateKey or die('密钥不可用');
	    $decryptData = '';
	    $crypto = '';
	    foreach (str_split(base64_decode($encryptData), 128) as $chunk) {
	        if(openssl_private_decrypt($chunk, $decryptData, $privateKey)){
	            $crypto .= $decryptData;
	        }else{
	            die('解密失败');
	        }

	    }
	    return $crypto;
	}

	//验证码
	public function code(){		
		$builder = new CaptchaBuilder();
		$builder->build();		
		setcookie('phrase', $builder->getPhrase());		 
		header('Content-type: image/jpeg');
		$builder->output();
	}	
}	