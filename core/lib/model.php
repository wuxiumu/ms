<?php

namespace core\lib;

use \core\lib\conf;

use Medoo\Medoo;

class model extends Medoo
{	
	public function __construct()
	{
		$option = conf::all('database');		
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