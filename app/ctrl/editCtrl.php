<?php

namespace app\ctrl;

use core\lib\model;

class editCtrl extends \core\phpmsframe
{	
	public function __construct(){
		session_start();
		if(!empty($_SESSION['user']['login_status']) && $_SESSION['user']['login_status']=='1'){
			//登陆成功			
		}else{
			//未记录登陆
			js_u('/index.php/login/user#login');exit;
		}		
	}
	//文章添加
	public function add(){ 
    	$this->assign('title','文章添加');
        $this->display('posts/add.html');
	}
	//添加行为
	public function add_action(){ 
	    $arr = $_POST;		
    	$time = date("Y-m-d H:i:s",time());  
		$arr['created_at'] = $time;
        $arr['updated_at'] = $time;
        $arr['uid'] = $_SESSION['user']['id'];
		$model = new \app\model\postModel();
		$re = $model->addpost($arr);		
		$error_arr = $re->errorInfo();
        if($error_arr['0']=='00000'){
		   js_u('/index.php/edit/index');
		}else{
		   dump($error_arr);         				
		}
	}
	public function mod(){ 
		$id = $_GET['id'];
		$model = new \app\model\postModel();
		$re = $model->getOne($id);
		$data['post'] = $re;
		dump($data);	
    	$this->assign('data',$data);
        $this->display('posts/add.html');
	}
	//添加行为
	public function mod_action(){ 
	    $arr = $_POST;		
    	$time = date("Y-m-d H:i:s",time());  
		$arr['created_at'] = $time;
        $arr['updated_at'] = $time;
        $arr['uid'] = $_SESSION['user']['id'];
		$model = new \app\model\postModel();
		$re = $model->addpost($arr);		
		$error_arr = $re->errorInfo();
        if($error_arr['0']=='00000'){
		   js_u('/index.php/edit/index');
		}else{
		   dump($error_arr);         				
		}
	}	

	//文章列表
	public function index(){ 
		$model = new \app\model\postModel();
		$re = $model->lists();		
    	$this->assign('posts',$re);
        $this->display('posts/list.html');
	}

	//文章详情
	public function postinfo(){ 
    	 
	}

	//文章搜索
	public function search(){  
    	 
	}

     
}