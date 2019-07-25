<?php

namespace app\ctrl;

use core\lib\Model;

class DiscussCtrl extends BaseController
{	
	public $user_set = NUll;
	public function __construct(){
		session_start(); 
		$this->user_set = $_SESSION['user']['set'];
	}

	//评论列表
	public function index(){ 			
			echo '评论列表';
  }

	//Update Discuss
	public function mod(){
		echo '修改评论';
	}
    
}