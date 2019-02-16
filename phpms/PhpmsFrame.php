<?php

namespace Phpms;

use Phpms\Lib\Log;

class PhpmsFrame 
{
	public static $classMap = array();

	public $assign;

    public function __get($property_name){
        $msg = "属性 $property_name 不存在\n";
        self::reportingDog($msg);	
    }

    public function __set($property_name,$value){
        $msg = "属性 $property_name 不存在\n";
		self::reportingDog($msg);	
    }

    public function __isset($property_name){
        $msg = "属性 $property_name 不存在\n";
        self::reportingDog($msg);	
    }

    public function __unset($property_name){
        $msg = "属性 $property_name 不存在\n";
        self::reportingDog($msg);	
    }

    public function __call($methodName,$argument){
		$msg = "实例方法 $methodName 不存在\n";
        self::reportingDog($msg);	
    }
 
	public static function run()
	{			
		Log::init();		 
		Log::log("url:".$_SERVER['REQUEST_URI']);	 

		$route = new \Phpms\Lib\Route();		
		$ctrlClass = $route->ctrl;
		$action = $route->action;		
		$ctrlfile = APP.'/ctrl/'.$ctrlClass.'Ctrl.php';
		$ctrlClass = '\\'.MODULE.'\ctrl\\'.$ctrlClass.'Ctrl';				
		if(is_file($ctrlfile)){			
			include $ctrlfile;			
			$ctrl = new $ctrlClass();
			$ctrl->$action();					
		}else{	
			$msg = "控制器 $ctrlClass 不存在\n";		 
			self::reportingDog($msg);			
		}
	}

	public static function load($class)
	{						 
		if(isset($classMap[$class])){
			return true;
		}else{			
			$class  = str_replace('\\', '/', $class);			
			$file = PHPMSFRAME.'/'.$class.'.php';		
			if(is_file($file)){
				include $file;				
				self::$classMap[$class] = $class;									 
			}else{				
				return false;
			}
		}			 				
	}

	public function assign($name,$value){
		$this->assign[$name]=$value;
	}

	public function display($file){
		$file_path = APP.'/views/'.$file;
		if(is_file($file_path)){	
			extract($this->assign);
			include $file_path;
		}
	}

	private static function reportingDog($msg){
		echo $msg."\n";				
		include 'smile/havefun.php';		
		$num = str_pad(rand(00,32),2,"0",STR_PAD_LEFT);
		$num = "str_".$num;		
		$Parsedown = new \Parsedown();
		echo $Parsedown->text($$num);
		$num = "str_".rand(50,84);
		echo $Parsedown->text($$num); 
		// include 'smile/img2txt.php';
		// $Parsedown = new \Parsedown();
		// echo $Parsedown->text($str);
		exit;
	}
}
