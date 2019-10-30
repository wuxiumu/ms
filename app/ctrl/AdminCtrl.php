<?php

namespace App\Ctrl;
use Core\phpmsframe as phpms;

class AdminCtrl extends phpms
{	 
	public function index($data=[]){ 
		$this->assign('data',$data);
		$this->display('admin.html');
	} 
}