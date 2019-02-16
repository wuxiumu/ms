<?php

namespace core\lib\drive\Log;

use core\lib\Conf;

class File
{	
	public function __construct(){
		$conf = Conf::get('OPTION','log');
		$this->path = $conf['PATH'];
	}
	public function log($msg,$file = 'log'){
		/**
		 * 1.确定文件储存位置是否存在	
		 * 2.新建目录	
		 * 3.写入日志
		 */		
		$path = conf::get('OPTION','log');		
		if(!is_dir($this->path.date("Ymd",time()))){			
			mkdir($this->path.date("Ymd",time()),'0777',true);
		}		
		return file_put_contents($this->path.date("Ymd",time()).DIRECTORY_SEPARATOR.date("H")."_".$file.'.php', date("H:i:s").json_encode($msg).PHP_EOL,FILE_APPEND);
	}
}
//文件系统