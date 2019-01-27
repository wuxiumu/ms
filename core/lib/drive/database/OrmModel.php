<?php
/**
 * 自定义模型基类
 */
namespace Core\Lib\Drive\Database;

use Core\Lib\Conf;

use PDO;

abstract class OrmModel{
   protected $pk = 'id';
   protected $_ID = null; 
   protected $_tableName;
   protected $_arRelationMap;
   protected $_modifyMap;
   protected $is_load = false;
   protected $_blForDeletion;
   protected $_DB;

   public function __consturct($id = null){
        $database = Conf::all('database');	
        $dsn='mysql:host='.$database['msyql_default']['DSN'].';dbname='.$database['msyql_default']['DBNAME'];
        $username=$database['msyql_default']['USERNAME'];
        $password=$database['msyql_default']['PASSWORD'];
        try{
            parent::__construct($dsn,$username,$password);
        }catch(\PDOException $e){
            p($e->getMessage());
        }
        if(isset($id))$this->_ID = $id;
   }
   abstract protected function getTableName();
   abstract protected function getRelationMap();

   public function Load(){
      
   }

   public function __call($method,$param){
     
   }

   public function setMember($key){
       
   }

   public function getMember($key,$val){
       
   }

   public function save(){
       
   }

   public function __destory(){
       
   }
}