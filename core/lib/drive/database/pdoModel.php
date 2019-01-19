<?php
/**
 * 继承pdo的，模型基类
 */
namespace core\lib\drive\database;

use core\lib\conf;

class pdoModel extends \PDO{
    //初始化，继承pdo应该是就可以直接用手册中的pdo中的方法了
    public function __construct()
    {
		$database = conf::all('database');	
		$dsn='mysql:host='.$database['msyql_default']['DSN'].';dbname='.$database['msyql_default']['DBNAME'];
		$username=$database['msyql_default']['USERNAME'];
		$password=$database['msyql_default']['PASSWORD'];
		try{
			parent::__construct($dsn,$username,$password);
		}catch(\PDOException $e){
			p($e->getMessage());
		}
    }
}