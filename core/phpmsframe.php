<?php

namespace Core;

class phpmsframe 
{
	public static $classMap = [];

	public $assign;

   /* 这是一个魔术方法，当一个对象或者类获取其不存在的属性的值时，
    * 如：$obj = new BaseController ;
    * $a = $obj -> a ;
    * 该方法会被自动调用,这样做很友好，可以避免系统报错
    */
    public function __get($property_name){
        $msg = "属性 $property_name 不存在\n";
        self::reportingDog($msg);	
    }

   /* 这是一个魔术方法，当一个对象或者类给其不存在的属性赋值时，
    * 如：$obj = new BaseController ;
    * $obj -> a = 12 ;
    * 该方法(__set(属性名,属性值))会被自动调用,这样做很友好，可以避免系统报错
    */
    public function __set($property_name,$value){
        $msg = "属性 $property_name 不存在\n";
		self::reportingDog($msg);	
    }

   /* 这是一个魔术方法，当一个对象或者类的不存在属性进行isset()时，
    * 注意：isset 用于检查一个量是否被赋值 如果为NULL会返回false
    * 如：$obj = new BaseController ;
    * isset($obj -> a) ;
    * 该方法会被自动调用,这样做很友好，可以避免系统报错
    */
    public function __isset($property_name){
        $msg = "属性 $property_name 不存在\n";
        self::reportingDog($msg);	
    }

   /* 这是一个魔术方法，当一个对象或者类的不存在属性进行unset()时，
    * 注意：unset 用于释放一个变量所分配的内存空间
    * 如：$obj = new BaseController ;
    * unset($obj -> a) ;
    * 该方法会被自动调用,这样做很友好，可以避免系统报错
    */
    public function __unset($property_name){
        $msg = "属性 $property_name 不存在\n";
        self::reportingDog($msg);	
    }

    /* 当对这个类的对象的不存在的实例方法进行“调用”时，会自动调用该方法，
     * 这个方法有2个参数（必须带有的）：
     * $methodName 表示要调用的不存在的方法名;
     * $argument 是一个数组，表示要调用该不存在的方法时，所使用的实参数据，
     */
    public function __call($methodName,$argument){
		$msg = "实例方法 $methodName 不存在\n";
        self::reportingDog($msg);	
    }
 
	static public function run()
	{		
		$route = new \Core\lib\route();
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

	static public function load($class)
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
			/***********twig模板***********/
			$loader = new \Twig_Loader_Filesystem(APP.'/views');
			$twig = new \Twig_Environment($loader, array(
			    'cache' => PHPMSFRAME.'/cache',
			    'debug'=>DEBUG,
			));						
			$template = $twig->load($file);			
			$template->display($this->assign?$this->assign:'');
			/***********twig模板end***********/

			/***********原生模板***********/
			//extract($this->assign);
			//include $file_path;
			/***********原生模板end***********/
		}
	}

	static private function reportingDog($msg){
		echo $msg."\n";		
		include 'lib/smile/havefun.php';		
		$num = str_pad(rand(00,32),2,"0",STR_PAD_LEFT);
		$num = "str_".$num;		
		$Parsedown = new \Parsedown();
		echo $Parsedown->text($$num);
		$num = "str_".rand(50,84);
		echo $Parsedown->text($$num); 
		exit;
	}
}
