<?php 
class Flight {
    /**
     * 框架引擎
     *
     * @var \flight\Engine
     */
    private static $engine;
    // 不允许对象实例化
    private function __construct() {}
    private function __destruct() {}
    private function __clone() {}
    /**
     * 处理对静态方法的调用。
     *
     * @param string $name Method name
     * @param array $params Method parameters
     * @return mixed Callback results
     * @throws \Exception
     */
    public static function __callStatic($name, $params) {
        $app = Flight::app();
        return \flight\core\Dispatcher::invokeMethod(array($app, $name), $params);
    }
    /**
     * @return \flight\Engine Application instance
     */
    public static function app() {
        static $initialized = false;
        if (!$initialized) {
            require_once __DIR__.'/autoload.php';
            self::$engine = new \flight\Engine();
            $initialized = true;
        }
        return self::$engine;
    }
}