<?php
/**
 * 继承pdo的，模型基类
 */
namespace Phpms\Lib\Drive\Database;

use Phpms\Lib\Conf;

class PdoModel extends \PDO{
    //初始化，继承pdo应该是就可以直接用手册中的pdo中的方法了
    public function __construct()
    {
		$database = Conf::all('database');	
		$dsn='mysql:host='.$database['mysql_default']['DSN'].';dbname='.$database['mysql_default']['DBNAME'];
		$username=$database['mysql_default']['USERNAME'];
		$password=$database['mysql_default']['PASSWORD'];
		try{
			parent::__construct($dsn,$username,$password);
		}catch(\PDOException $e){
			p($e->getMessage());
		}
    }
}