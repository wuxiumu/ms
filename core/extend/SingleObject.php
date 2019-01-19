<?php

namespace core\extend;

class SingleObject
{
    //私有的静态属性，用于存储类对象
    private static $instance = null;
    
    //私有的构造方法,保证不允许在类外 new
    private function __construct(){

    }

    //私有的克隆方法, 确保不允许通过在类外 clone 来创建新对象
    private function __clone(){
        
    }

    //公有的静态方法，用来实例化唯一当前类对象
    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self;
        }
        return self::$instance;
    }
}