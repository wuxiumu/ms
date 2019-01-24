<?php

namespace Core\Lib\Drive\Log;

use Core\Lib\Conf;

class Mysql
{	
	public function __construct(){
		$conf = Conf::get('OPTION','log');
		$this->path = $conf['PATH'];
	}
	public function log($msg,$file = 'log'){
	 
	 
	}
}
//数据库