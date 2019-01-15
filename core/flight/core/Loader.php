<?php
/**
 * Flight: 可扩展的微框架
 *
 */
namespace flight\core;
/**
 * Loader类负责加载对象 
 * 它坚持可重用类实例的列表，可以生成新类
 * 具有自定义初始化参数的实例它也表现
 * 类自动加载
 */
class Loader {
    /**
     * 注册 classes
     *
     * @var array
     */
    protected $classes = array();
    /**
     * 类实例
     *
     * @var array
     */
    protected $instances = array();
    /**
     * 自动加载目录
     *
     * @var array
     */
    protected static $dirs = array();
    /**
     * 注册一个 class
     *
     * @param string $name Registry name
     * @param string|callable $class Class name or function to instantiate class
     * @param array $params Class initialization parameters
     * @param callback $callback Function to call after object instantiation
     */
    public function register($name, $class, array $params = array(), $callback = null) {
        unset($this->instances[$name]);
        $this->classes[$name] = array($class, $params, $callback);
    }
    /**
     * 取消注册一个 class
     *
     * @param string $name Registry name
     */
    public function unregister($name) {
        unset($this->classes[$name]);
    }
    /**
     * 加载注册的 class 
     *
     * @param string $name Method name
     * @param bool $shared Shared instance
     * @return object Class instance
     * @throws \Exception
     */
    public function load($name, $shared = true) {
        $obj = null;
        if (isset($this->classes[$name])) {
            list($class, $params, $callback) = $this->classes[$name];
            $exists = isset($this->instances[$name]);
            if ($shared) {
                $obj = ($exists) ?
                    $this->getInstance($name) :
                    $this->newInstance($class, $params);
                
                if (!$exists) {
                    $this->instances[$name] = $obj;
                }
            }
            else {
                $obj = $this->newInstance($class, $params);
            }
            if ($callback && (!$shared || !$exists)) {
                $ref = array(&$obj);
                call_user_func_array($callback, $ref);
            }
        }
        return $obj;
    }
    /**
     * 获取 class 的单个实例
     *
     * @param string $name Instance name
     * @return object Class instance
     */
    public function getInstance($name) {
        return isset($this->instances[$name]) ? $this->instances[$name] : null;
    }
    /**
     * Gets a new instance of a class.
     *
     * @param string|callable $class Class name or callback function to instantiate class
     * @param array $params Class initialization parameters
     * @return object Class instance
     * @throws \Exception
     */
    public function newInstance($class, array $params = array()) {
        if (is_callable($class)) {
            return call_user_func_array($class, $params);
        }
        switch (count($params)) {
            case 0:
                return new $class();
            case 1:
                return new $class($params[0]);
            case 2:
                return new $class($params[0], $params[1]);
            case 3:
                return new $class($params[0], $params[1], $params[2]);
            case 4:
                return new $class($params[0], $params[1], $params[2], $params[3]);
            case 5:
                return new $class($params[0], $params[1], $params[2], $params[3], $params[4]);
            default:
                try {
                    $refClass = new \ReflectionClass($class);
                    return $refClass->newInstanceArgs($params);
                } catch (\ReflectionException $e) {
                    throw new \Exception("Cannot instantiate {$class}", 0, $e);
                }
        }
    }
    /**
     * @param string $name Registry name
     * @return mixed Class information or null if not registered
     */
    public function get($name) {
        return isset($this->classes[$name]) ? $this->classes[$name] : null;
    }
    /**
     * 将对象重置为初始状态
     */
    public function reset() {
        $this->classes = array();
        $this->instances = array();
    }
    /*** 自动加载功能 ***/
    /**
     * 启动/停止自动加载器
     *
     * @param bool $enabled Enable/disable autoloading
     * @param array $dirs Autoload directories
     */
    public static function autoload($enabled = true, $dirs = array()) {
        if ($enabled) {
            spl_autoload_register(array(__CLASS__, 'loadClass'));
        }
        else {
            spl_autoload_unregister(array(__CLASS__, 'loadClass'));
        }
        if (!empty($dirs)) {
            self::addDirectory($dirs);
        }
    }
    /**
     * 自动加载 classes.
     *
     * @param string $class Class name
     */
    public static function loadClass($class) {
        $class_file = str_replace(array('\\', '_'), '/', $class).'.php';
        foreach (self::$dirs as $dir) {
            $file = $dir.'/'.$class_file;
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
    /**
     * 添加自动加载 classess 的目录
     *
     * @param mixed $dir Directory path
     */
    public static function addDirectory($dir) {
        if (is_array($dir) || is_object($dir)) {
            foreach ($dir as $value) {
                self::addDirectory($value);
            }
        }
        else if (is_string($dir)) {
            if (!in_array($dir, self::$dirs)) self::$dirs[] = $dir;
        }
    }
}