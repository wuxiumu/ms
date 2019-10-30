<?php

namespace App\Ctrl;
use Core\phpmsframe as phpms;

class userCtrl extends phpms
{	

	public function index($data=[]){ 
		$this->assign('data',$data);
		$this->display('user.html');
	} 
}