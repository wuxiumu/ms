<?php

namespace Core\Lib;

use Core\Lib\Conf;

class Log
{
	static $class;
	/**
	 * 1.确定日志储存方式
	 * 2.写日志	 
	 */
	public static function init(){
		//确定储存方式
		$drive = Conf::get('DRIVE','log');
		$class = '\core\lib\drive\log\\'.$drive;		
		self::$class = new $class;
	}

	public static function log($name,$file='log'){
		self::$class->log($name,$file);	
	}
}