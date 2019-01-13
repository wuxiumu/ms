<?php

namespace app\ctrl;

use core\lib\model;

class indexCtrl extends \core\phpmsframe
{	
	//首页 文章列表
	public function index(){ 
		$model = new \app\model\postModel();
		$page = 0;//数组以0起始
		$limit = 2;
		$id = $model->select("posts",
								"id", 
								[
									"LIMIT" => [$page , $limit],
									"ORDER" => ["id" => "DESC"]
								]
							);//根据分页获取ID
		$list = $model->select("posts",
								   "*",
								   [	
									   "id" => $id,
									   "ORDER" => ["id" => "DESC"]
								   ]
								);//请求数据库数据
		$re = $list;
		foreach ($re as $key => $value) {
			$re[$key]['content'] = mb_substr($value['content'],0,300,'utf-8');  
		}
		$data['posts'] = $re;
    	$this->assign('data',$data);
        $this->display('index.html');
	}
	public function ajaxposts(){
	    $model = new \app\model\postModel();
		$page  = $_POST['page']; 
		$limit = $_POST['limit'];
		$id = $model->select("posts",
								"id", 
								[
									"ORDER" => ["id" => "DESC"],
									"LIMIT" => [$page , $limit],				
								]
							);//根据分页获取ID
		$list = $model->select("posts",
								   "*",
								   [	
									   "id" => $id,
									   "ORDER" => ["id" => "DESC"]
								   ]
								);//请求数据库数据
	    if(empty($list)){
	    	echo '';
		}else{			
			$re = $list;
			foreach ($re as $key => $value) {
				$re[$key]['content'] = mb_substr($value['content'],0,300,'utf-8');  
			}		
			$data['posts'] = $re;
	    	$this->assign('data',$data);
	        $this->display('ajax/index.html');
		}
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