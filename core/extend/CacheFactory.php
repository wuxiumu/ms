<?php

namespace core\extend;

class CacheFactory
{
    const FILE = 1;
    const MEMCACHE = 2;
    const REDIS = 3;
 
    static $instance;//定义静态属性，用于存储对象
 
    /**
     * 工厂类创建缓存对象
     * @param $type 指定缓存类型
     * @param array $options 传入缓存参数
     * @return FileCache|Memcache|RedisCache
     */
    static function getCacheObj($type, array $options)
    {
        switch ($type) {
            case 'file':
            case self::FILE:
                self::$instance = new FileCache($options);
                break;
 
            case 'memcache':
            case self::MEMCACHE:
                self::$instance = new Memcache($options);
                break;
 
            case 'redis':
            case self::REDIS:
                self::$instance = new RedisCache($options);
                break;
 
            default:
                self::$instance = new FileCache($options);
                break;
 
        }
        return self::$instance;
    }
}