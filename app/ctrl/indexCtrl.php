<?php

namespace app\ctrl;

use core\lib\model;

class indexCtrl extends \core\phpmsframe
{	
	//首页 文章列表
	public function index(){
	    session_start(); 		
		$model = new \app\model\postModel();
		$page = 0; // 数组以0起始
		if(isset($_GET['page'])){
			$page = $_GET['page'];
		}
		$limit = 10;
		$data['previous'] = $page - $limit;				
		$data['next']     = $page + $limit;
		$id = $model->select("posts",
								"id", 
								[
									"display" => 1,
									"LIMIT" => [$page , $limit],
									"ORDER" => ["id" => "DESC"]
								]
							); // 根据分页获取ID
		$list = $model->select("posts",
								   "*",
								   [	
									   "id" => $id,
									   "ORDER" => ["id" => "DESC"]
								   ]
								); // 请求数据库数据
		$re = $list;
		foreach ($re as $key => $value) {
			$re[$key]['content'] = mb_substr($value['content'],0,300,'utf-8');  
		}
		$data['posts'] = $re;
    	$this->assign('data',$data);
        $this->display('index.html');
	}
	public function ajaxposts(){
    
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
						"ORDER" => ["id" => "ASC"]
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
		$data['comments']  = [];
		$comments = $model->select("comment",
								"*", 
								["post_id"=>$id]
							);
		foreach ($comments as $key => $value) {
			$comments[$key]['content'] = $Parsedown->text($value['content']);
		}	
		$data['comments'] = $comments;					
								
		$filename = PHPMSFRAME.'/public/media/baidu_tts/post_'.$id.'.mp3';
		if(file_exists($filename)){
			$data['mp3']['status'] = 1;
			$data['mp3']['file'] = '/public/media/baidu_tts/post_'.$id.'.mp3';
			$data['mp3']['num']=1;
			for($i=1;$i<10;$i++){
				$filename = PHPMSFRAME.'/public/media/baidu_tts/post_'.$id."_".$i.'.mp3';
				if(file_exists($filename)){
					$data['mp3']['files'][] = '/public/media/baidu_tts/post_'.$id."_".$i.'.mp3';
					$data['mp3']['num']= $i+1;
				}else{
					break;
				}
			}
		}else{
			$data['mp3']['status'] = 2;
		}		
		$this->assign('data',$data);
        $this->display('postinfo.html');
	}

	// 文章分类
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
							); // 根据分页获取ID
		$list = $model->select("posts",
								   "*",
								   [	
									   "id" => $id,
									   "ORDER" => ["id" => "DESC"]
								   ]
								); // 请求数据库数据		
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

	// 文章搜索
	public function search(){  
    	 
	}

	// 文章评论
	public function comment(){		
		if($this->isPost()){
			session_start();
			if(!empty($_SESSION['user']['login_status']) && $_SESSION['user']['login_status']=='1'){
				// 登陆成功			
			}else{
				// 未记录登陆
				js_u('/index.php/login/user#login');exit;
			}				
			$arr = $_POST;
			$arr['from_uid'] = $_SESSION['user']['id'];
			$arr['nickname'] = $_SESSION['user']['name'];
			$arr['thumb_img'] = "/public/img/header-img-comment_03.png";
			$arr['create_time'] = date('Y-m-d H:i:s',time());
			$model = new \app\model\commentModel();
			$re = $model->addcomment($arr);										
			$error_arr = $re->errorInfo();
			if($error_arr['0']=='00000'){
				$url = "/index.php/index/postinfo/id/".$_POST['post_id'];
				js_u($url);
			}else{
				dump($error_arr);         				
			}	
		}		
	}

	// 文章评论数
	public function commentlist($where){		
		$model = new \app\model\commentModel();
		$re = $model->findcomment($where);	
		if($re){
			return $re;
		}else{
			return [];
		}		
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
	public function isPost() {
		return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'],'POST');
	}   
}