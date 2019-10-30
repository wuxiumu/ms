<?php

namespace Core\Lib;

use \Core\Lib\conf;

use Medoo\Medoo;

class model extends Medoo
{	
	public function __construct()
	{
		$option = conf::all('database');	// 默认配置文件加载
		//env 文件加载配置数据
		// $option['mysql_medoo_conf'] = [
		// 	"database_type" =>  getenv('DB_CONNECTION'),
		// 	"database_name" => getenv('DB_DATABASE'),
		// 	"server" => getenv('DB_HOST'),
		// 	"username" => getenv('DB_USERNAME'),
		// 	"password" => getenv('DB_PASSWORD')
		// ];	
		parent::__construct($option['mysql_medoo_conf']);
	}
}
/**************************Medoo拓展**************************/

/**************************pdo拓展**************************/
// class model extends \PDO
// {	
// 	public function __construct()
// 	{
// 		$database = conf::all('database');	
// 		$dsn='mysql:host='.$database['msyql_default']['DSN'].';dbname='.$database['msyql_default']['DBNAME'];
// 		$username=$database['msyql_default']['USERNAME'];
// 		$password=$database['msyql_default']['PASSWORD'];
// 		try{
// 			parent::__construct($dsn,$username,$password);
// 		}catch(\PDOException $e){
// 			p($e->getMessage());
// 		}
// 	}
// }
/**************************pdo拓展end**************************/