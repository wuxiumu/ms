---
layout:     readme
title:      "理解Flight框架核心"
subtitle:   "Understand the core of the Flight framework"
date:       2019-01-23 20:00:00
author:     "吴庆宝"
tags:
    - phpms框架
---

Flight框架（官网）是一个微型的PHP框架，它简单，快速，可扩展。借助Flight可以快捷而轻松的创建你的RESTFul web应用。

虽然是一个微型的框架，而且代码量确实也较少，但我在阅读Flight代码的过程中，感到了它设计和构思独特而精妙的地方，觉得有学习的价值，便决定做一下整理分享出来。 接下来就来看看Flight是怎么工作的吧。
```
<?php
class Flight {
    /**
     * Framework engine.
     * @var object
     */
    private static $engine;
 
    // Don't allow object instantiation
    private function __construct() {}
    private function __destruct() {}
    private function __clone() {}
 
    /**
     * 之前已经看到了，框架内所有函数都是以Flight类的静态函数形式调用的
     * __callStatic()这个魔术方法能处理所有的静态函数
     * @param string $name Method name
     * @param array $params Method parameters
     * @return mixed Callback results
     */
    public static function __callStatic($name, $params) {
        static $initialized = false;
 
        if (!$initialized) {
            //这里定义框架的自动加载机制，实际上是依据PSR-0标准来做的
            require_once __DIR__.'/autoload.php';
 
            //Engine类是框架的引擎所在
            self::$engine = new \flight\Engine();
 
            $initialized = true;
        }
 
        //在这里，Flight对Engine包装了一层而已。对Flight类静态函数的调用，实质上是对Engine类的相应函数的调用
        return \flight\core\Dispatcher::invokeMethod(array(self::$engine, $name), $params);
    }
}
```

```
//那么就直接就来看看Dispatcher::invokeMethod函数吧
namespace flight\core;
class Dispatcher {
    /**
     * 调用一个方法
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
}
```
上面注释里提到了，自动加载和PSR-0，我之前写过一篇关于这部分内容的文章。Flight框架的自动加载就是基于namespace和psr-0标准的：
```
//只列出有关自动加载部分的主要代码
namespace flight\core;
class Loader {
    /**
     * Starts/stops autoloader.
     *
     * @param bool $enabled Enable/disable autoloading
     * @param mixed $dirs Autoload directories
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
     * Autoloads classes.
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
}
```
再继续往下看之前，我们不妨先对Flight内主要的类和函数进行一下梳理，以下是Flight框架的内置类：
```
Engine类：包含了这个框架的核心功能。它的责任是加载HTTP请求，运行已注册的服务，并生成最后的HTTP响应。
Loader类：它负责框架内对象的加载。用自定义的初始化参数来生成新的类实例，并且维护可复用的类实例的列表。它还处理刚才提到过的类的自动加载。
Dispatcher类：它负责框架内事件的分发处理。事件即是对类方法或函数的简单的称呼（别名）。它还允许你在事件上的挂钩点挂载别的函数，能够改变函数的输入或者输出。
Router类：它负责将一个HTTP讲求发送到指定的函数进行处理。它视图将请求的URL和一系列用户定义的URL范式进行匹配。
Route类：它负责路由的具体实现。Router相当于对Route的包装。
Request类：它代表了一个HTTP请求。所有来自$_GET,$_POST,$_COOKIE,$_FILES中的数据都要通过Request类获取和访问。默认的Request属性就包括url,base,method,user_agent等。
Response类：对应于Request，它代表了一个HTTP响应。这个对象包括了返回头，HTTP状态码和返回体。
View类：视图类负责将输出展示。它提供了在渲染时管理视图数据和将数据插入视图模板的函数。
Collection类：它允许你既可以以使用数组的方式，也能以使用对象的方式来访问数据。
```

Flight框架的函数分两部分，一部分是核心函数：
```
Flight::map($name, $callback) // Creates a custom framework method.
Flight::register($name, $class, [$params], [$callback]) // Registers a class to a framework method.
Flight::before($name, $callback) // Adds a filter before a framework method.
Flight::after($name, $callback) // Adds a filter after a framework method.
Flight::path($path) // Adds a path for autoloading classes.
Flight::get($key) // Gets a variable.
Flight::set($key, $value) // Sets a variable.
Flight::has($key) // Checks if a variable is set.
Flight::clear([$key]) // Clears a variable.
```

另一部分是扩展函数：
```
Flight::start() // Starts the framework.
Flight::stop() // Stops the framework and sends a response.
Flight::halt([$code], [$message]) // Stop the framework with an optional status code and message.
Flight::route($pattern, $callback) // Maps a URL pattern to a callback.
Flight::redirect($url, [$code]) // Redirects to another URL.
Flight::render($file, [$data], [$key]) // Renders a template file.
Flight::error($exception) // Sends an HTTP 500 response.
Flight::notFound() // Sends an HTTP 404 response.
Flight::etag($id, [$type]) // Performs ETag HTTP caching.
Flight::lastModified($time) // Performs last modified HTTP caching.
Flight::json($data, [$code], [$encode]) // Sends a JSON response.
Flight::jsonp($data, [$param], [$code], [$encode]) // Sends a JSONP response.
```
Flight框架的使用方式就是对Flight类静态函数调用(Flight::func())，我们在上面提到过，其实质是对Engine对象中函数的调用($engineObj->func())。

而Engine类的函数有两类，一类是核心函数，是直接进行调用(相对于动态调用)的，另外的扩展函数，则是进行动态调用的。

此外，在Flight中加载类和资源，获得某个类的实例，直接调用Flight::className()即可，等同于$engineObj->className()。这个也是采用动态调用的形式。也就是说，除了Engine类的核心函数，其他函数（类）都是动态调用的。这样，框架就可以为此提供一个统一的入口了。
```
namespace flight;
class Engine {
    //....
    public function __construct() {
        $this->vars = array();
 
        //上面提到过，Flight中,Dispatcher负责处理函数，Loader负责对象的加载
        $this->loader = new Loader();
        $this->dispatcher = new Dispatcher();
 
        $this->init();
    }
 
    /**
     * __call是一个魔术方法，当调用一个不存在的函数时，会调用到该函数
     * 刚才讲的动态调用就是通过这个函数进行的
     * @param string $name Method name
     * @param array $params Method parameters
     * @return mixed Callback results
     */
    public function __call($name, $params) {
        //先判断是类还是可直接调用的函数
        $callback = $this->dispatcher->get($name);
 
        //如果是函数，通过dispatcher处理
        if (is_callable($callback)) {
            return $this->dispatcher->run($name, $params);
        }
 
        //是否是共享实例
        $shared = (!empty($params)) ? (bool)$params[0] : true;
 
        //通过loader加载该类的对象
        return $this->loader->load($name, $shared);
    }
 
    /**
     * 框架初始化
     */
    public function init() {
        static $initialized = false;
        $self = $this;
 
        if ($initialized) {
            $this->vars = array();
            $this->loader->reset();
            $this->dispatcher->reset();
        }
 
        // Flight中，类会通过loader的register函数进行注册
        // 注册默认组件
        $this->loader->register('request', '\flight\net\Request');
        $this->loader->register('response', '\flight\net\Response');
        $this->loader->register('router', '\flight\net\Router');
        $this->loader->register('view', '\flight\template\View', array(), function($view) use ($self) {
            $view->path = $self->get('flight.views.path');
        });
 
        // 注册框架方法
        $methods = array(
            'start','stop','route','halt','error','notFound',
            'render','redirect','etag','lastModified','json','jsonp'
        );
        // Flight中，method会通过dispatcher的set函数将对应的回调函数绑定到一个事件中
        // 为了可以进行动态调用，Enginge的扩展函数全部是通过 _method 的名字定义的
        foreach ($methods as $name) {
            $this->dispatcher->set($name, array($this, '_'.$name));
        }
 
        // 默认的配置
        $this->set('flight.base_url', null);
        $this->set('flight.handle_errors', true);
        $this->set('flight.log_errors', false);
        $this->set('flight.views.path', './views');
 
        $initialized = true;
    }
 
    /**
     * 将一个类注册到框架方法中，我们就是通过这个函数注册我们自定义的类的
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
 
        //通过loader的register函数进行注册
        $this->loader->register($name, $class, $params, $callback);
    }
 
    /**
     * 将一个回调函数映射到框架方式中，我们就是通过这个函数映射我们自定义函数的
     *
     * @param string $name Method name
     * @param callback $callback Callback function
     * @throws \Exception If trying to map over a framework method
     */
    public function map($name, $callback) {
        if (method_exists($this, $name)) {
            throw new \Exception('Cannot override an existing framework method.');
        }
 
        //会通过dispatcher的set函数将对应的回调函数绑定到一个事件中
        $this->dispatcher->set($name, $callback);
    }
 
    //...
}
```
Flight中的两个核心函数，map是映射自定义的函数，最后是通过dispathcer的set函数实现的，register是注册自定义的类，最后是通过loader的register函数实现的。而框架自己的核心组件和扩展函数，Engine在初始化过程帮我们完成了这两个过程。接着Flight提供了一个统一的入口，可以动态调用所有非核心的函数，类。这就是Flight的核心加载机制了。

可能你还有疑问，为什么Flight要使用动态调用的形式去访问这些函数或对象？尤其是对于Engine的扩展函数，为什么不直接进行调用呢？因为Flight可以对它们进行过滤或重写。过滤和重写是Flight框架进行扩展的重要功能。框架实现了统一的资源操作方式后，就可以方便的进行重写或者过滤的处理了。需要注意的是，核心函数诸如map和register是不能够进行过滤或重写的，相信你已经清楚为什么了。


框架的重写功能还是使用的map和register这两个函数。这个功能因为框架的设计方式，很轻易的完成了。在Dispatcher和Loader中都动态维护了一个映射表，Dispatcher里是回调到事件的映射，Loader中是class到实例和构造函数等的映射。这样，注册自定义函数或类时，遇到一样名字就会覆盖掉之前的，而使用时只返回最新的。下面是Loader类的部分代码：
```
namespace flight\core;
class Loader {
    //....
    /**
     * 注册一个类
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
     * 加载一个已注册的类
     *
     * @param string $name Method name
     * @param bool $shared Shared instance
     * @return object Class instance
     */
    public function load($name, $shared = true) {
        $obj = null;
 
        //$this->classes是注册过的类
        //$this->instances是加载过的实例
        if (isset($this->classes[$name])) {
            list($class, $params, $callback) = $this->classes[$name];
 
            $exists = isset($this->instances[$name]);
 
            //是不是共享实例
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
     * 得到一个类的单一实例
     *
     * @param string $name Instance name
     * @return object Class instance
     */
    public function getInstance($name) {
        return isset($this->instances[$name]) ? $this->instances[$name] : null;
    }
 
    /**
     * 得到一个类的新的实例
     *
     * @param string|callable $class Class name or callback function to instantiate class
     * @param array $params Class initialization parameters
     * @return object Class instance
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
                $refClass = new \ReflectionClass($class);
                return $refClass->newInstanceArgs($params);
        }
    }
    //....
}
```
跟过滤器功能有关的函数是before和after，分别是在被过滤函数处理之前或之后进行操作。最终是在Dispatcher类中实现的。
```
namespace flight;
class Engine {
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
     * Adds a post-filter to a method.
     *
     * @param string $name Method name
     * @param callback $callback Callback function
     */
    public function after($name, $callback) {
        $this->dispatcher->hook($name, 'after', $callback);
    }
}
 
namespace flight\core;
class Dispatcher {
 
    /**
     * 将回调注册到一个事件之中
     *
     * @param string $name Event name
     * @param callback $callback Callback function
     */
    public function set($name, $callback) {
        $this->events[$name] = $callback;
    }
 
    /**
     * 得到事件关联的回调
     *
     * @param string $name Event name
     * @return callback $callback Callback function
     */
    public function get($name) {
        return isset($this->events[$name]) ? $this->events[$name] : null;
    }
 
    /**
     * 在事件上挂一个回调函数
     *
     * @param string $name Event name
     * @param string $type Filter type
     * @param callback $callback Callback function
     */
    public function hook($name, $type, $callback) {
        $this->filters[$name][$type][] = $callback;
    }
 
    /**
     * 对事件进行分发处理
     *
     * @param string $name Event name
     * @param array $params Callback parameters
     * @return string Output of callback
     */
    public function run($name, array $params = array()) {
        $output = '';
 
        // 运行前置过滤器
        if (!empty($this->filters[$name]['before'])) {
            $this->filter($this->filters[$name]['before'], $params, $output);
        }
 
        // 运行所请求的方法
        $output = $this->execute($this->get($name), $params);
 
        // 运行后置过滤器
        if (!empty($this->filters[$name]['after'])) {
            $this->filter($this->filters[$name]['after'], $params, $output);
        }
 
        return $output;
    }
}
```
下面，还差最后一步，运行这个框架时处理流程是怎样的呢？
```
namespace flight;
class Engine {
    /**
     * 启动这个框架
     */
    public function _start() {
        $dispatched = false;
        $self = $this;
        $request = $this->request();
        $response = $this->response();
        $router = $this->router();
 
        // 冲刷掉已经存在的输出
        if (ob_get_length() > 0) {
            $response->write(ob_get_clean());
        }
 
        // 启动输出缓冲
        ob_start();
 
        // 开启错误处理
        $this->handleErrors($this->get('flight.handle_errors'));
 
        // 对AJAX请求关闭缓存
        if ($request->ajax) {
            $response->cache(false);
        }
 
        // 允许后置过滤器的运行
        $this->after('start', function() use ($self) {
            //start完成之后会调用stop()函数
            $self->stop();
        });
 
        // 对该请求进行路由
        while ($route = $router->route($request)) {
            $params = array_values($route->params);
 
            //是否让路由链继续下去
            $continue = $this->dispatcher->execute(
                $route->callback,
                $params
            );
 
            $dispatched = true;
 
            if (!$continue) break;
 
            $router->next();
        }
 
        //路由没找匹配到
        if (!$dispatched) {
            $this->notFound();
        }
    }
 
    /**
     * 停止这个框架并且输出当前的响应内容
     *
     * @param int $code HTTP status code
     */
    public function _stop($code = 200) {
        $this->response()
            ->status($code)
            ->write(ob_get_clean())
            ->send();
    }
}
```
至此，应该对Flight核心的设计，功能以及处理流程有所认识了吧。

什么是路由，如何请求已经响应。