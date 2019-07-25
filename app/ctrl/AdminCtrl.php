<?php

namespace app\ctrl;

use core\lib\Model;

class AdminCtrl extends BaseController
{	
	public function __construct(){
		session_start(); 
	    if(isset($_SESSION['user']['login_status'])){
			// 登陆成功			
		}else{
			// 未记录登陆
			js_u('/index.php/login/user#login');exit;
		}
	}
	
	public function index(){ 		    
		$data['set'] = $_SESSION['user']['set'];		
    	$this->assign('data',$data);
        $this->display('admin/index.html');
    }

    public function header(){ 	
		$data = [];
    	$this->assign('data',$data);
        $this->display('admin/header/index');
	}
	public function ajaxSet(){
		$skin = $_POST['skin']?$_POST['skin']:"skin-blur-yellow";
		$a = $_SESSION['user']['set']=array("skin"=>$skin);		
		echo 1;		
	}
    
}