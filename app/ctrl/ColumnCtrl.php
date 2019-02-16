<?php

namespace app\ctrl;

use core\lib\Model;

class ColumnCtrl extends BaseController
{	
	//后台首页
	public function index(){ 	
		$data = [];
    	$this->assign('data',$data);
        $this->display('column/index.html');
    }

    
}