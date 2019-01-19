<?php

namespace core\lib\drive\template;

class phpView{   
	
	public function view($file,$assign)
    {
		$file_path = APP.'/views/'.$file;
		extract($assign);
		include $file_path;
    }
}