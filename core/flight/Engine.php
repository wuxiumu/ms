<?php
/**
 * Flight: 可扩展的微框架
 *
 */
namespace flight;
use flight\core\Loader;
use flight\core\Dispatcher;

/**
 * Engine类包含框架的核心功能
 * 它负责加载HTTP请求，运行分配的服务
 * 并生成HTTP响应
 *
 * 核心方法
 * @method void start() 启动引擎
 * @method void stop() 停止框架并输出当前响应
 * @method void halt(int $code = 200, string $message = '') 停止处理并返回给定的响应
 *
 *
 * 路由
 * @method void route(string $pattern, callable $callback, bool $pass_route = false) 将URL路由到回调函数
 * @method \flight\net\Router router() 获取路由器
 *
 * 视图
 * @method void render(string $file, array $data = null, string $key = null) 渲染模板
 * @method \flight\template\View view() Gets current view
 *
 * 请求 - 响应
 * @method \flight\net\Request request() 获取当前请求
 * @method \flight\net\Response response() 获取当前响应
 * @method void error(\Exception $e) 针对任何错误发送HTTP 500响应
 * @method void notFound() 找不到URL时发送HTTP 404响应
 * @method void redirect(string $url, int $code = 303)  将当前请求重定向到另一个URL
 * @method void json(mixed $data, int $code = 200, bool $encode = true, string $charset = 'utf-8', int $option = 0) 发送JSON响应
 * @method void jsonp(mixed $data, string $param = 'jsonp', int $code = 200, bool $encode = true, string $charset = 'utf-8', int $option = 0) 发送JSONP响应
 *
 * HTTP缓存
 * @method void etag($id, string $type = 'strong') 处理ETag HTTP缓存
 * @method void lastModified(int $time) 处理上次修改的HTTP缓存
 */
class Engine {
    /**
     * 存储变量
     *
     * @var array
     */
    protected $vars;
    /**
     * 类加载器
     *
     * @var Loader
     */
    protected $loader;
    /**
     * 事件调度员
     *
     * @var Dispatcher
     */
    protected $dispatcher;
    /**
     * 构造函数
     */
    public function __construct() {
        $this->vars = array();
        $this->loader = new Loader();
        $this->dispatcher = new Dispatcher();
        $this->init();
    }
    /**
     * 处理对类方法的调用
     *
     * @param string $name Method name
     * @param array $params Method parameters
     * @return mixed Callback results
     * @throws \Exception
     */
    public function __call($name, $params) {
        $callback = $this->dispatcher->get($name);
        if (is_callable($callback)) {
            return $this->dispatcher->run($name, $params);
        }
        if (!$this->loader->get($name)) {
            throw new \Exception("{$name} must be a mapped method.");
        }
        $shared = (!empty($params)) ? (bool)$params[0] : true;
        return $this->loader->load($name, $shared);
    }
    /*** 核心方法 ***/
    /**
     * 初始化框架
     */
    public function init() {
        static $initialized = false;
        $self = $this;
        if ($initialized) {
            $this->vars = array();
            $this->loader->reset();
            $this->dispatcher->reset();
        }
        // 注册默认组件
        $this->loader->register('request', '\flight\net\Request');
        $this->loader->register('response', '\flight\net\Response');
        $this->loader->register('router', '\flight\net\Router');
        $this->loader->register('view', '\flight\template\View', array(), function($view) use ($self) {
            $view->path = $self->get('flight.views.path');
            $view->extension = $self->get('flight.views.extension');
        });
        // 注册框架方法
        $methods = array(
            'start','stop','route','halt','error','notFound',
            'render','redirect','etag','lastModified','json','jsonp'
        );
        foreach ($methods as $name) {
            $this->dispatcher->set($name, array($this, '_'.$name));
        }
        // 默认配置设置
        $this->set('flight.base_url', null);
        $this->set('flight.case_sensitive', false);
        $this->set('flight.handle_errors', true);
        $this->set('flight.log_errors', false);
        $this->set('flight.views.path', './views');
        $this->set('flight.views.extension', '.php');
        // 启动配置
        $this->before('start', function() use ($self) {
            // 启用错误处理
            if ($self->get('flight.handle_errors')) {
                set_error_handler(array($self, 'handleError'));
                set_exception_handler(array($self, 'handleException'));
            }
            // 设置区分大小写
            $self->router()->case_sensitive = $self->get('flight.case_sensitive');
        });
        $initialized = true;
    }
    /**
     * 自定义错误处理程序。将错误转换为异常。
     *
     * @param int $errno Error number
     * @param int $errstr Error string
     * @param int $errfile Error file name
     * @param int $errline Error file line number
     * @throws \ErrorException
     */
    public function handleError($errno, $errstr, $errfile, $errline) {
        if ($errno & error_reporting()) {
            throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
        }
    }
    /**
     * 自定义异常处理程序。记录异常。
     *
     * @param \Exception $e Thrown exception
     */
    public function handleException($e) {
        if ($this->get('flight.log_errors')) {
            error_log($e->getMessage());
        }
        $this->error($e);
    }
    /**
     * 将回调映射到框架方法
     *
     * @param string $name Method name
     * @param callback $callback Callback function
     * @throws \Exception If trying to map over a framework method
     */
    public function map($name, $callback) {
        if (method_exists($this, $name)) {
            throw new \Exception('Cannot override an existing framework method.');
        }
        $this->dispatcher->set($name, $callback);
    }
    /**
     * 将类注册到框架方法
     *
     * @param string $name Method name
     * @param string $class Class name
     * @param array $params Class initialization parameters
     * @param callback $callback Function to call after object instantiation
     * @throws \Exception If trying to map over a framework method
     */
    public function register($name, $class, array $params = array(), $callback = null) {
        if (method_exists($this, $name)) {
            throw new \Exception('Cannot override an existing framework method.');
        }
        $this->loader->register($name, $class, $params, $callback);
    }
    /**
     * Adds a pre-filter to a method.
     *
     * @param string $name Method name
     * @param callback $callback Callback function
     */
    public function before($name, $callback) {
        $this->dispatcher->hook($name, 'before', $callback);
    }
    /**
     * 向方法添加后筛选器
     *
     * @param string $name Method name
     * @param callback $callback Callback function
     */
    public function after($name, $callback) {
        $this->dispatcher->hook($name, 'after', $callback);
    }
    /**
     * 获取变量
     *
     * @param string $key Key
     * @return mixed
     */
    public function get($key = null) {
        if ($key === null) return $this->vars;
        return isset($this->vars[$key]) ? $this->vars[$key] : null;
    }
    /**
     * 设置变量
     *
     * @param mixed $key Key
     * @param string $value Value
     */
    public function set($key, $value = null) {
        if (is_array($key) || is_object($key)) {
            foreach ($key as $k => $v) {
                $this->vars[$k] = $v;
            }
        }
        else {
            $this->vars[$key] = $value;
        }
    }
    /**
     * 检查是否已设置变量
     *
     * @param string $key Key
     * @return bool Variable status
     */
    public function has($key) {
        return isset($this->vars[$key]);
    }
    /**
     * 取消设置变量。如果没有传入任何键，请清除所有变量
     *
     * @param string $key Key
     */
    public function clear($key = null) {
        if (is_null($key)) {
            $this->vars = array();
        }
        else {
            unset($this->vars[$key]);
        }
    }
    /**
     * 添加类自动加载的路径
     *
     * @param string $dir Directory path
     */
    public function path($dir) {
        $this->loader->addDirectory($dir);
    }
    /*** 可扩展方法 ***/
    /**
     * 启动框架
     * @throws \Exception
     */
    public function _start() {
        $dispatched = false;
        $self = $this;
        $request = $this->request();
        $response = $this->response();
        $router = $this->router();
        // 允许运行筛选器
        $this->after('start', function() use ($self) {
            $self->stop();
        });
        // 刷新任何现有输出
        if (ob_get_length() > 0) {
            $response->write(ob_get_clean());
        }
        // 启用输出缓冲
        ob_start();
        // 传送请求
        while ($route = $router->route($request)) {
            $params = array_values($route->params);
            // 向参数列表中添加工艺路线信息
            if ($route->pass) {
                $params[] = $route;
            }
            // 调用路由处理程序
            $continue = $this->dispatcher->execute(
                $route->callback,
                $params
            );
            $dispatched = true;
            if (!$continue) break;
            $router->next();
            $dispatched = false;
        }
        if (!$dispatched) {
            $this->notFound();
        }
    }
    /**
     * 停止框架并输出当前响应
     *
     * @param int $code HTTP status code
     * @throws \Exception
     */
    public function _stop($code = null) {
        $response = $this->response();
        if (!$response->sent()) {
            if ($code !== null) {
                $response->status($code);
            }
            $response->write(ob_get_clean());
            $response->send();
        }
    }
    /**
     * 将URL路由到回调函数
     *
     * @param string $pattern URL pattern to match
     * @param callback $callback Callback function
     * @param boolean $pass_route Pass the matching route object to the callback
     */
    public function _route($pattern, $callback, $pass_route = false) {
        $this->router()->map($pattern, $callback, $pass_route);
    }
    /**
     * 停止处理并返回给定的响应
     *
     * @param int $code HTTP status code
     * @param string $message Response message
     */
    public function _halt($code = 200, $message = '') {
        $this->response()
            ->clear()
            ->status($code)
            ->write($message)
            ->send();
        exit();
    }
    /**
     * 针对任何错误发送HTTP 500响应
     *
     * @param \Exception|\Throwable $e Thrown exception
     */
    public function _error($e) {
        $msg = sprintf('<h1>500 Internal Server Error</h1>'.
            '<h3>%s (%s)</h3>'.
            '<pre>%s</pre>',
            $e->getMessage(),
            $e->getCode(),
            $e->getTraceAsString()
        );
        try {
            $this->response()
                ->clear()
                ->status(500)
                ->write($msg)
                ->send();
        }
        catch (\Throwable $t) { // PHP 7.0+
            exit($msg);
        } catch(\Exception $e) { // PHP < 7
            exit($msg);
        }
    }
    /**
     * 当找不到URL时发送HTTP 404响应
     */
    public function _notFound() {
        $this->response()
            ->clear()
            ->status(404)
            ->write(
                '<h1>404 Not Found</h1>'.
                '<h3>The page you have requested could not be found.</h3>'.
                str_repeat(' ', 512)
            )
            ->send();
    }
    /**
     * 将当前请求重定向到其他URL
     *
     * @param string $url URL
     * @param int $code HTTP status code
     */
    public function _redirect($url, $code = 303) {
        $base = $this->get('flight.base_url');
        if ($base === null) {
            $base = $this->request()->base;
        }
        // 附加基URL以重定向URL
        if ($base != '/' && strpos($url, '://') === false) {
            $url = $base . preg_replace('#/+#', '/', '/' . $url);
        }
        $this->response()
            ->clear()
            ->status($code)
            ->header('Location', $url)
            ->send();
    }
    /**
     * 呈现模板
     *
     * @param string $file Template file
     * @param array $data Template data
     * @param string $key View variable name
     * @throws \Exception
     */
    public function _render($file, $data = null, $key = null) {
        if ($key !== null) {
            $this->view()->set($key, $this->view()->fetch($file, $data));
        }
        else {
            $this->view()->render($file, $data);
        }
    }
    /**
     * 发送JSON响应
     *
     * @param mixed $data JSON data
     * @param int $code HTTP status code
     * @param bool $encode Whether to perform JSON encoding
     * @param string $charset Charset
     * @param int $option Bitmask Json constant such as JSON_HEX_QUOT
     * @throws \Exception
     */
    public function _json(
        $data,
        $code = 200,
        $encode = true,
        $charset = 'utf-8',
        $option = 0
    ) {
        $json = ($encode) ? json_encode($data, $option) : $data;
        $this->response()
            ->status($code)
            ->header('Content-Type', 'application/json; charset='.$charset)
            ->write($json)
            ->send();
    }
	
    /**
     * 发送JSONP响应
     *
     * @param mixed $data JSON data
     * @param string $param Query parameter that specifies the callback name.
     * @param int $code HTTP status code
     * @param bool $encode Whether to perform JSON encoding
     * @param string $charset Charset
     * @param int $option Bitmask Json constant such as JSON_HEX_QUOT
     * @throws \Exception
     */
    public function _jsonp(
        $data,
        $param = 'jsonp',
        $code = 200,
        $encode = true,
        $charset = 'utf-8',
        $option = 0
    ) {
        $json = ($encode) ? json_encode($data, $option) : $data;
        $callback = $this->request()->query[$param];
        $this->response()
            ->status($code)
            ->header('Content-Type', 'application/javascript; charset='.$charset)
            ->write($callback.'('.$json.');')
            ->send();
    }
    /**
     * 处理ETag HTTP缓存
     *
     * @param string $id ETag identifier
     * @param string $type ETag type
     */
    public function _etag($id, $type = 'strong') {
        $id = (($type === 'weak') ? 'W/' : '').$id;
        $this->response()->header('ETag', $id);
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) &&
            $_SERVER['HTTP_IF_NONE_MATCH'] === $id) {
            $this->halt(304);
        }
    }
    /**
     * 处理上次修改的HTTP缓存
     *
     * @param int $time Unix timestamp
     */
    public function _lastModified($time) {
        $this->response()->header('Last-Modified', gmdate('D, d M Y H:i:s \G\M\T', $time));
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&
            strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) === $time) {
            $this->halt(304);
        }
    }
}