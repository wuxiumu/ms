<?php

namespace app\ctrl;

use core\lib\model;

class indexCtrl extends \core\phpmsframe
{	
	//首页 文章列表
	public function index(){ 
		$model = new \app\model\postModel();
		$re = $model->lists();
		$data['posts'] = $re;
    	$this->assign('data',$data);
        $this->display('index.html');
	}

	//文章详情
	public function postinfo(){
	    $id =  $_GET['id'];
		$model = new \app\model\postModel();
		$re = $model->getOne($id);
		$Parsedown = new \Parsedown();
	    $re['content'] = $Parsedown->text($re['content']);
		$data['post'] = $re;       	 
		$this->assign('data',$data);
        $this->display('postinfo.html');
	}
	//文章分类
	public function term(){
	    echo 1;
	}
	//文章搜索
	public function search(){  
    	 
	}

     
}