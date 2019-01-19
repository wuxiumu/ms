<?php

namespace core\extend;

class RegisterTree
{
    static protected $objects;//静态类属性，用于储存注册到注册树上的对象
 
    /**
     * 将对象注册到注册树上
     * @param $alias 对象的别名
     * @param $object 对象
     */
    static function setObject($alias,$object)
    {
        self::$objects[$alias] = $object;
    }
 
    /**
     * 从注册树上取出给定别名相应的对象
     * @param $alias 将对象插入到注册树上时写的别名
     * @return mixed 对象
     */
    static protected function getObject($alias)
    {
        return self::$objects[$alias];
    }
 
    /**
     * 将对象从注册树上删除
     * @param $alias 将对象插入到注册树上时写的别名
     */
    public function unsetObject($alias)
    {
        unset(self::$objects[$alias]);
    }
}