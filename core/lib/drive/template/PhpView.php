<?php

namespace core\lib\drive\template;

class PhpView{   
	
	public function view($file,$assign)
    {
		$file_path = APP.'/views/'.$file;
		extract($assign);
		include $file_path;
    }
}