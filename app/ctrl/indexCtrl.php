<?php

namespace app\ctrl;

use core\lib\model;

class indexCtrl extends \core\phpmsframe
{	
	//首页 文章列表
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