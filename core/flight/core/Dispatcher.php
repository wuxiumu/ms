<?php
namespace flight\core;
    /**
	 * Dispatcher类负责调度事件
	 * 事件只是类方法或函数的别名
	 * Dispatcher允许您将其他函数挂钩到可以修改输入参数 and/or 输出的事件	 
	 */
class Dispatcher {
    /**
     * 映射事件
     *
     * @var array
     */
    protected $events = array();
    /**
     * 方法过滤器
     *
     * @var array
     */
    protected $filters = array();
    /**
     * 派遣活动。
     *
     * @param string $name Event name
     * @param array $params Callback parameters
     * @return string Output of callback
     * @throws \Exception
     */
    public function run($name, array $params = array()) {
        $output = '';
        // 运行预过滤器
        if (!empty($this->filters[$name]['before'])) {
            $this->filter($this->filters[$name]['before'], $params, $output);
        }
        // 运行请求的方法
        $output = $this->execute($this->get($name), $params);
        // 运行后过滤器
        if (!empty($this->filters[$name]['after'])) {
            $this->filter($this->filters[$name]['after'], $params, $output);
        }
        return $output;
    }
    /**
     * 为事件分配回调
     *
     * @param string $name Event name
     * @param callback $callback Callback function
     */
    public function set($name, $callback) {
        $this->events[$name] = $callback;
    }
    /**
     * 获取指定的回调
     *
     * @param string $name Event name
     * @return callback $callback Callback function
     */
    public function get($name) {
        return isset($this->events[$name]) ? $this->events[$name] : null;
    }
    /**
     * 检查是否已设置事件
     *
     * @param string $name Event name
     * @return bool Event status
     */
    public function has($name) {
        return isset($this->events[$name]);
    }
    /**
     * 清除活动。如果没有给出名字 
     * 所有事件都被删除。
     *
     * @param string $name Event name
     */
    public function clear($name = null) {
        if ($name !== null) {
            unset($this->events[$name]);
            unset($this->filters[$name]);
        }
        else {
            $this->events = array();
            $this->filters = array();
        }
    }
    /**
     * 钩住对事件的回调
     *
     * @param string $name Event name
     * @param string $type Filter type
     * @param callback $callback Callback function
     */
    public function hook($name, $type, $callback) {
        $this->filters[$name][$type][] = $callback;
    }
    /**
     * 执行一系列方法过滤器
     *
     * @param array $filters Chain of filters
     * @param array $params Method parameters
     * @param mixed $output Method output
     * @throws \Exception
     */
    public function filter($filters, &$params, &$output) {
        $args = array(&$params, &$output);
        foreach ($filters as $callback) {
            $continue = $this->execute($callback, $args);
            if ($continue === false) break;
        }
    }
    /**
     * 执行回调函数
     *
     * @param callback $callback Callback function
     * @param array $params Function parameters
     * @return mixed Function results
     * @throws \Exception
     */
    public static function execute($callback, array &$params = array()) {
        if (is_callable($callback)) {
            return is_array($callback) ?
                self::invokeMethod($callback, $params) :
                self::callFunction($callback, $params);
        }
        else {
            throw new \Exception('Invalid callback specified.');
        }
    }
    /**
     * 调用一个函数
     *
     * @param string $func Name of function to call
     * @param array $params Function parameters
     * @return mixed Function results
     */
    public static function callFunction($func, array &$params = array()) {
        // 调用静态方法
        if (is_string($func) && strpos($func, '::') !== false) {
            return call_user_func_array($func, $params);
        }
        switch (count($params)) {
            case 0:
                return $func();
            case 1:
                return $func($params[0]);
            case 2:
                return $func($params[0], $params[1]);
            case 3:
                return $func($params[0], $params[1], $params[2]);
            case 4:
                return $func($params[0], $params[1], $params[2], $params[3]);
            case 5:
                return $func($params[0], $params[1], $params[2], $params[3], $params[4]);
            default:
                return call_user_func_array($func, $params);
        }
    }
    /**
     * 调用方法
     *
     * @param mixed $func Class method
     * @param array $params Class method parameters
     * @return mixed Function results
     */
    public static function invokeMethod($func, array &$params = array()) {
        list($class, $method) = $func;
        $instance = is_object($class);
		
        switch (count($params)) {
            case 0:
                return ($instance) ?
                    $class->$method() :
                    $class::$method();
            case 1:
                return ($instance) ?
                    $class->$method($params[0]) :
                    $class::$method($params[0]);
            case 2:
                return ($instance) ?
                    $class->$method($params[0], $params[1]) :
                    $class::$method($params[0], $params[1]);
            case 3:
                return ($instance) ?
                    $class->$method($params[0], $params[1], $params[2]) :
                    $class::$method($params[0], $params[1], $params[2]);
            case 4:
                return ($instance) ?
                    $class->$method($params[0], $params[1], $params[2], $params[3]) :
                    $class::$method($params[0], $params[1], $params[2], $params[3]);
            case 5:
                return ($instance) ?
                    $class->$method($params[0], $params[1], $params[2], $params[3], $params[4]) :
                    $class::$method($params[0], $params[1], $params[2], $params[3], $params[4]);
            default:
                return call_user_func_array($func, $params);
        }
    }
    /**
     * 将对象重置为初始状态
     */
    public function reset() {
        $this->events = array();
        $this->filters = array();
    }
}