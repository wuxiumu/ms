<?php

namespace  Phpms\Lib;

class Conf
{
	public static $conf = [];

	public static function get($name,$file){		
		/**
		 * 1.判断配置文件是否存在	
		 * 2.判断配置是否存在
		 * 3.缓存配置
		 */
		$file = PHPMSFRAME.DIRECTORY_SEPARATOR.'phpms'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.$file.'.php';		
		if(isset(self::$conf[$file])){
			return self::$conf[$file][$name];
		}else{			
			if(is_file($file)){
				$conf = include $file;
				if(isset($conf[$name])){
					self::$conf[$file] = $conf;
					return $conf[$name];
				}else{
					throw new Exception("没有这个配置项目", $name);
				}
			}else{
				throw new Exception("没有这个配置文件", $file);
			}				
		}
	}

	public static function all($file){
		$file = PHPMSFRAME.DIRECTORY_SEPARATOR.'phpms'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.$file.'.php';		
		if(isset(self::$conf[$file])){
			return self::$conf[$file];
		}else{			
			if(is_file($file)){
				$conf = include $file;
				self::$conf[$file] = $conf;
				return $conf;
			}else{
				throw new Exception("没有这个配置文件", $file);
			}				
		}
	}
}