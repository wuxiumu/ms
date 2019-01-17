<?php

namespace app\ctrl;

use core\lib\model;

class indexCtrl extends \core\phpmsframe
{	
	//首页 文章列表
	public function index(){
	    session_start(); 		
		$model = new \app\model\postModel();
		$page = 0;//数组以0起始
		if(isset($_GET['page'])){
			$page = $_GET['page'];
		}
		$limit = 10;
		$data['previous'] = $page - $limit;				
		$data['next']     = $page + $limit;
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
		echo '';die;
		if($_SERVER['REQUEST_METHOD']=="POST"){

			$model = new \app\model\postModel();
			$page  = $_POST['page'] ? $_POST['page'] : 1; 
			$limit = $_POST['limit'];
			$pid   = $_POST['pid'] ? $_POST['pid'] : 0;
			$id = $model->select("posts",
									"id", 
									[									
										"pid[=]" => $pid,
										"LIMIT" => [$page , $limit] 		
									]
								);//根据分页获取ID
			$list = $model->select("posts",
									   "*",
									   [	
										   "id" => $id
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
		 }else {
		   echo "submit is no come~";
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
 		$previous_id = $model->select("posts",
					"id", 
					[
						"id[>]" => $re['id'],
						"pid" => $re['pid'],
						"LIMIT" => [0,1],
						"ORDER" => ["id" => "DESC"]
					]
				);
 		$next_id = $model->select("posts",
					"id",					
					[	"id[<]" => $id,	
						"pid" => $re['pid'],				
						"LIMIT" => [0,1],
						"ORDER" => ["id" => "DESC"]
					]
				); 	
 		if(!empty($previous_id)){
 			$data['previous_id']=$previous_id['0'];
 		}
 	    if(!empty($next_id)){
 	    	$data['next_id']=$next_id['0'];
 		} 		
		$this->assign('data',$data);
        $this->display('postinfo.html');
	}
	//文章分类
	public function term(){
	    $pid = $_GET['id'];
	    $page = 0;
	    $limit = 10;
	    if(isset($_GET['page'])){
	    	$page = $_GET['page'];	
	    }	    
	    $data['pid'] = $pid;
		$data['previous'] = $page - $limit;				
		$data['next']     = $page + $limit;
		$model = new \app\model\postModel();		
		$id = $model->select("posts",
								"id", 
								[
									"pid"=>$pid,
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
		$re = [];
	    if(!empty($list)){
	    	$re = $list;
	    }	    	    
		foreach ($re as $key => $value) {
			$re[$key]['content'] = mb_substr($value['content'],0,300,'utf-8');  
		}
		$data['posts'] =  $re;   		
    	$this->assign('data',$data);
        $this->display('indexterm.html');
	}
	//文章搜索
	public function search(){  
    	 
	}
	/**
	 * 是否是GET提交的
	 */
	function isGet(){
	  return $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
	}
	/**
	 * 是否是POST提交
	 * @return int
	 */
	function isPost() {
	  return ($_SERVER['REQUEST_METHOD'] == 'POST' && checkurlHash($GLOBALS['verify']) && (empty($_SERVER['HTTP_REFERER']) || preg_replace("~https?:\/\/([^\:\/]+).*~i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("~([^\:]+).*~", "\\1", $_SERVER['HTTP_HOST']))) ? 1 : 0;
	}
     
}