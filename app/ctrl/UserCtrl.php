<?php

namespace App\Ctrl;

use Core\Lib\Model;

class UserCtrl extends BaseController
{	
	//用户 权限
	public function index(){ 
    	$this->assign('title','404页面');
        $this->display('index.html');
	}

	//文章详情
	public function postinfo(){ 
    	 
	}

	//文章搜索
	public function search(){  
    	 
	}

     
}