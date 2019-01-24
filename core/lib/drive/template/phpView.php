<?php

namespace Core\Lib\Drive\Template;

class PhpView{   
	
	public function view($file,$assign)
    {
			$file_path = APP.'/views/'.$file;
			extract($assign);
			include $file_path;
    }
}