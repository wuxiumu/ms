<?php
/**
 * Flight: 可扩展的微框架
 *
 */
namespace flight\net;
/**
 * 路由器类负责将HTTP请求路由到分配的回调函数
 * 路由器尝试匹配根据一系列URL模式请求URL
 *
 */
class Router {
    /**
     * 映射路由
     *
     * @var array
     */
    protected $routes = array();
    /**
     * 指向当前路由的指针
     *
     * @var int
     */
    protected $index = 0;
    /**
     * 区分大小写匹配
     *
     * @var boolean
     */
    public $case_sensitive = false;
    /**
     * 获取映射的路由
     *
     * @return array Array of routes
     */
    public function getRoutes() {
        return $this->routes;
    }
    /**
     * 清除路由器中的所有路由
     */
    public function clear() {
        $this->routes = array();
    }
    /**
     * 将URL模式映射到回调函数
     *
     * @param string $pattern URL pattern to match
     * @param callback $callback Callback function
     * @param boolean $pass_route Pass the matching route object to the callback
     */
    public function map($pattern, $callback, $pass_route = false) {
        $url = $pattern;
        $methods = array('*');
        if (strpos($pattern, ' ') !== false) {
            list($method, $url) = explode(' ', trim($pattern), 2);
            $methods = explode('|', $method);
        }
        $this->routes[] = new Route($url, $callback, $methods, $pass_route);
    }
    /**
     * 路由当前请求
     *
     * @param Request $request Request object
     * @return Route|bool Matching route or false if no match
     */
    public function route(Request $request) {
        $url_decoded = urldecode( $request->url );
        while ($route = $this->current()) {
            if ($route !== false && $route->matchMethod($request->method) && $route->matchUrl($url_decoded, $this->case_sensitive)) {
                return $route;
            }
            $this->next();
        }
        return false;
    }
    /**
     * 获取当前路由
     *
     * @return Route
     */
    public function current() {
        return isset($this->routes[$this->index]) ? $this->routes[$this->index] : false;
    }
    /**
     * 获取下一条路线
     *
     * @return Route
     */
    public function next() {
        $this->index++;
    }
    /**
     * 重置为第一条路线
     */
    public  function reset() {
        $this->index = 0;
    }
}