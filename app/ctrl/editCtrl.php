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
		$data['post'] = [];
		$data['post']['created_at'] = date("Y-m-d H:i:s",time());	
		$termModel = new \app\model\termModel();
		$reterm = $termModel->lists(); 
		$reterm = $this->recursion($reterm,0);	
		$data['terms'] = $reterm;
    	$this->assign('data',$data);
        $this->display('posts/add.html');
	}
	//添加行为
	public function add_action(){ 
	    $arr = $_POST;		
    	$time = date("Y-m-d H:i:s",time());  		
        $arr['updated_at'] = $time;
		$arr['uid'] = $_SESSION['user']['id'];
		$model = new \app\model\termModel();
	    if($arr['pid'] =='0' ){
	        $arr['path'] = '0';
	    }else{
	    	$tmp = $model->getOne($arr['pid']);
	    	if($tmp['pid']=!0){
	    		$arr['path'] = $tmp['path'].'-'.$tmp['id'];
	    	}else{
	    		$arr['path'] = '0-'.$arr['pid'];	
	    	}	    	
	    }
		$model = new \app\model\postModel();
		if(empty($arr['id'])){
			unset($arr['id']);
			$re = $model->addpost($arr);					
		}else{
			$id = $arr['id'];
			unset($arr['id']);
			$re = $model->updateOne($id,$arr);
		}

		$error_arr = $re->errorInfo();
        if($error_arr['0']=='00000'){
		   js_u('/index.php/edit/index');
		}else{
		   dump($error_arr);         				
		}		
	}
	//修改页面
	public function mod(){ 
		$id = $_GET['id'];
		$model = new \app\model\postModel();
		$re = $model->getOne($id);
		$data['post'] = $re;	
        $termModel = new \app\model\termModel();
		$reterm = $termModel->lists(); 
		$reterm = $this->recursion($reterm,0);	
		$data['terms'] = $reterm;
    	$this->assign('data',$data);
        $this->display('posts/add.html');
	}
	//添加分类页面
	public function term_add(){ 
        $model = new \app\model\termModel();
		$re = $model->lists(); 
		$re = $this->recursion($re,0);	
		$data['terms'] = $re;		 
    	$this->assign('data',$data);
        $this->display('posts/termadd.html');
	}
	//修改分类页面
	public function term_mod(){ 
		$id = $_GET['id'];
        $model = new \app\model\termModel();
		$re = $model->lists(); 
		$re = $this->recursion($re,0);	
		foreach ($re as $key => $value) {
			if($value['id']==$id){
				unset($re[$key]);
			}
		}
		$re = array_values($re);
		$data['terms'] = $re;
		$data['term']  = $model->getOne($id); 
    	$this->assign('data',$data);
        $this->display('posts/termadd.html');
	}
	//分类页面列表
	public function term_list(){ 
        $model = new \app\model\termModel();
		$re = $model->lists(); 
		$re = $this->recursion($re,0);	
		$data['terms'] = $re;	
		$data['currentPage'] = 1;
		if(isset($_GET['page'])){
			$data['currentPage'] = $_GET['page'];
		}
		$data['totalPages'] = 100;	
    	$this->assign('data',$data);
        $this->display('posts/termlist.html');
	}
	//显示数组层次关系
	public function recursion($result,$pid=0,$format="|--"){
		/*记录排序后的类别数组*/
		static $list=array();
		foreach ($result as $k => $v){
	    	if($v['pid']==$pid){
	    		if($pid!=0){
	    			$v['name']=$format.$v['name'];
	    		}
	    		/*将该类别的数据放入list中*/
	    		$list[]=$v;
	    		$this->recursion($result,$v['id'],"  ".$format);
	    	}
	   	}
	   	return $list;
	} 
	//添加分类行为
	public function term_action(){ 
	    dump($_POST);
	    $arr = $_POST;
	    $model = new \app\model\termModel();
	    if($arr['pid'] =='0' ){
	        $arr['path'] = '0';
	    }else{
	    	$tmp = $model->getOne($arr['pid']);
	    	if($tmp['pid']=!0){
	    		$arr['path'] = $tmp['path'].'-'.$tmp['id'];
	    	}else{
	    		$arr['path'] = '0-'.$arr['pid'];	
	    	}	    	
	    }
		if(empty($arr['id'])){
			unset($arr['id']);
			$re = $model->addterm($arr);					
		}else{
			$id = $arr['id'];
			unset($arr['id']); 
			$re = $model->updateOne($id,$arr);
		}
	    $error_arr = $re->errorInfo();
        if($error_arr['0']=='00000'){
		   js_u('/index.php/edit/term_list');
		}else{
		   dump($error_arr);         				
		}
	}	

	//文章列表
	public function index(){ 
		$model = new \app\model\termModel();
		$re = $model->lists(); 
		//$re = $this->recursion($re,0);
		//	getTree
		$re = $this->get_tree($re);
		//$re = $this->formatTree($re,0);
		echo json_encode($re);die;
		dump($re);
		die;
		$data['terms'] = $re;
		$pid    =  0;
		$page   =  0; 
		$limit  = 10;
		$data['currentPage'] = 1;		
		if(isset($_GET['page'])){
			$data['currentPage'] = $_GET['page'];
		}
		$page = ($data['currentPage']-1)*$limit;
		$data['numberOfPages'] = $limit;	
		$model = new \app\model\postModel();
		$conf = [					
			"ORDER" => ["id" => "DESC"],
			"LIMIT" => [$page, $limit]
		];
		$data['totalPages'] = ceil($model->count("posts","id")/$limit);
		if(isset($_GET['pid'])){
			if($_GET['pid']!='0'){
				$data['pid'] = $_GET['pid'];
				$pid         = $_GET['pid'];
				$conf['pid'] = $pid;						
				$data['totalPages'] = ceil($model->count("posts",[ 
					'pid' => $pid  
				])/$limit);
			}
		}
	    $id = $model->select("posts",
								"id", 
								$conf
							);//根据分页获取ID							
		$re = $model->select("posts",
								   "*",
								   [	
								   	   "ORDER" => ["id" => "DESC"],
									   "id" => $id
								   ]
								);//请求数据库数据
		$data['posts'] = $re;								
    	$this->assign('data',$data);
        $this->display('posts/list.html');
	}

	//文章详情
	public function postinfo(){ 
    	 
	}

	//文章搜索
	public function search(){  
    	 
	}

	/********************
	*数据转为树型状的数组  
	*传统递归方法 getTreeOptions3 
	*入栈、出栈的递归
	*引用
	********************/
    
	//效率最低的递归方法：就是不停的foreach循环递归。
	function getTreeOptions3($list, $pid = 0,$level = 0)
	{    
		$options = [];    
		foreach ($list as $key => $value) {		
			if ($value['pid'] == $pid) {
				//查看是否为子元素，如果是则递归继续查询
				$value['level'] = $level;
	            $options[] = $value;            
	            unset($list[$key]);//销毁已查询的，减轻下次递归时查询数量
				$optionsTmp = $this->getTreeOptions3($list, $value['id'],$level+1);				
	            //递归
	            if (!empty($optionsTmp)) {                
	            	$options = array_merge($options, $optionsTmp);
	            }
	        }
	    }    
	    return $options;
	}

	//入栈、出栈的递归来
	function getTreeOptions2($list, $pid = 0,$level = 0)
	{    
		$tree = [];    
		if (!empty($list)) {        
		    //先将数组反转，因为后期出栈时会优先出最上面的
			$list = array_reverse($list);    			
	        //先取出顶级的来压入数组$stack中，并将在$list中的删除掉
	        $stack = [];        
	        foreach ($list as $key => $value) {            
	        	if ($value['pid'] == $pid) {  
					$value['level'] = $level;              
	        		array_push($stack,$value);                
	        		unset($list[$key]);
	            }
			}       			    
	        while (count($stack)) {            
	        	//先从栈中取出第一项
				$info = array_pop($stack); 				         
	            //查询剩余的$list中pid与其id相等的，也就是查找其子节点
	            foreach ($list as $key => $child) {                
	            	if ($child['pid'] == $info['id']) {                    
						//如果有子节点则入栈，while循环中会继续查找子节点的下级
						$child['level'] = $info['level']+1;    
	                    array_push($stack,  $child);                    
	                    unset($list[$key]);
	                }
				}    				      
	            //组装成下拉菜单格式
	            $tree[] = $info;
	        }
	    }    
	    return $tree;
	}

	//引用
	function getTreeOptions($list, $pid = 0)
	{    
		$tree = [];    
		if (!empty($list)) {        //先修改为以id为下标的列表
	        $newList = [];        
	        foreach ($list as $k => $v) {  				          
	        	$newList[$v['id']] = $v;
	        }        
	        //然后开始组装成特殊格式
	        foreach ($newList as $value) {            
	        	if ($pid == $value['pid']) {//先取出顶级
					$tree[] = &$newList[$value['id']];
					
	            } elseif (isset($newList[$value['pid']])) {
					//再判定非顶级的pid是否存在
					//如果存在，则再pid所在的数组下面加入一个字段items，来将本身存进去
	            	$newList[$value['pid']]['items'][] = &$newList[$value['id']];
				}				
	        }
	    }    
	    return $tree;
	}
	// 	（递归）耗时：8.9441471099854左右
	//  （迭代）耗时：6.7250330448151左右
	//  （引用）耗时：0.028863906860352左右

	//array树转array
    function formatTree($tree)
	{    
		$options = [];    
		if (!empty($tree)) {        
			foreach ($tree as $key => $value) {            				         
				if (isset($value['items'])) {//查询是否有子节点
					$optionsTmp = $this->formatTree($value['items']); 
					unset($value['items']);
					$options[] = $value;   
	                if (!empty($optionsTmp)) {                   
	                    $options = array_merge($options, $optionsTmp);
					}					
	            }else{
					$options[] = $value;   
				}
	        }
	    }    
	    return $options;
	}	
	//数组变成无限级分类--传引用思想
	public static function get_tree($orig) {
		//解决下标不是1开始的问题
		$items = [];
		foreach ($orig as $key => $value) {
		 	$items[$value['id']] = $value;
		}
		//开始组装
		$tree = [];
		foreach ($items as $key => $item) {
			if ($item['pid'] == 0) { //为0，则为1级分类
				$tree[] = &$items[$key];
			} else {
				if (isset($items[$item['pid']])) { //存在值则为二级分类
					$items[$item['pid']]['items'][] = &$items[$key]; //传引用直接赋值与改变
				} else { 
					//至少三级分类
					//由于是传引用思想，这里将不会有值
					$tree[] = &$items[$key];
				}
		 	}
		}
		return $tree;
	}
}