<?php

namespace app\ctrl;

use core\lib\Model;

class FilemanagerCtrl extends BaseController
{	
	public $user_set = NUll;
	public function __construct(){
		session_start(); 
	    if(isset($_SESSION['user']['login_status'])){
			$this->user_set = $_SESSION['user']['set'];		
		}else{
			//未记录登陆
			js_u('/index.php/login/user#login');exit;
		}		
	}
	
	public function index(){ 		
		$data['set'] = $this->user_set;	
    	$this->assign('data',$data);
        $this->display('admin/file-manager.html');
    }
    
    public function connector(){ 			
		echo 1;
    }

}	