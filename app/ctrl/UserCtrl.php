<?php

namespace app\ctrl;

use core\lib\Model;

class UserCtrl extends BaseController
{	

	public function index(){ 
    	$this->assign('title','404页面');
        $this->display('admin/404.html');
	}
	   
}