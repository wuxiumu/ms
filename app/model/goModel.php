<?php

namespace app\model;
 
use core\lib\conf;

class goModel
{
	protected $host;//主机名
    protected $user;//用户名
    protected $password;//密码
    protected $dbname;//数据库名
    protected $charset;//字符集
    protected $prefix;//数据表前缀
 
    //数据库连接资源
    protected $link;
    //表名 
    protected $tableName;
    //操作数组
	protected $options;
	
	//构造方法
	public function __construct($config=array())
	{

		$option = conf::all('database'); 
		$this->host      = $option['mysql_default']['DSN'];
		$this->user      = $option['mysql_default']['USERNAME'];
		$this->password  = $option['mysql_default']['PASSWORD'];
		$this->dbname    = 'phpms';
		$this->charset   =  'utf-8';
		$this->prefix    = '';
		$this->link      = $this->connect();
		$this->tableName = $this->getTableName();
		//初始化操作数组
		$this->initOptions();
	}
	//连接方法
	protected function connect()
    {
        //连接数据库
        $link = mysqli_connect($this->host, $this->user, $this->password, $this->dbname);
        if (!$link) {
            die('数据库连接失败');
        }
        //设置字符集
        mysqli_set_charset($link, $this->charset);
        return $link;
	}
	//
	protected function getTableName()
    {
        if (!empty($this->tableName)) {
            $tableName = $this->prefix . $this->tableName;
        } else {
            $className = get_class($this);
            $table = strtolower(substr($className, 0, -5));
            $tableName = $this->prefix . $table;
        }
        return $tableName;
	}
	protected function initOptions()
    {
        $arr = ['where', 'table', 'field', 'group', 'order', 'having', 'limit'];
        foreach ($arr as $value) {
            $this->options[$value] = '';
        }
        if ($value == 'table') {
            $this->options[$value] = $this->tableName;
        }
    }




}

class MySQLException extends \Exception { }