<?php
/**
 * 继承medoo第3方类库，模型基类
 */
namespace Core\Lib\Drive\Database;

use Core\Lib\Conf;

use Medoo\Medoo;

class MedooModel extends  Medoo{
    //初始化，继承pdo应该是就可以直接用手册中的pdo中的方法了
    public function __construct()
    {
        $option = conf::all('database');		
        parent::__construct($option['mysql_medoo_conf']);
    }
}