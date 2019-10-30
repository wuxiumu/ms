<?php

namespace App\Ctrl;
use App\Model\PostModel;
use Core\phpmsframe as phpms;

class indexCtrl extends phpms
{		
	//首页 文章列表
	public function index($data=[]){ 			 
		$this->assign('data',$data);
		$this->display('index.html');
	}

	public function postsList(){
		$model = new PostModel();
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
		echo json($data);
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
		echo json($data);
	}

}