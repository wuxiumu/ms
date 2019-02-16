<?php

namespace core\lib\router;

class Ms {
  public static $halts = false;
  public static $routes = array();
  public static $methods = array();
  public static $callbacks = array();
  public static $maps = array();
  public static $patterns = array(
      ':any' => '[^/]+',
      ':num' => '[0-9]+',
      ':all' => '.*'
  );
  public static $error_callback;
  /**
   * 定义具有回调和方法的路由
   */
  public static function __callstatic($method, $params) {
    if ($method == 'map') {
        $maps = array_map('strtoupper', $params[0]);
        $uri = strpos($params[1], '/') === 0 ? $params[1] : '/' . $params[1];
        $callback = $params[2];
    } else {
        $maps = null;
        $uri = strpos($params[0], '/') === 0 ? $params[0] : '/' . $params[0];
        $callback = $params[1];
    }
    array_push(self::$maps, $maps);
    array_push(self::$routes, $uri);
    array_push(self::$methods, strtoupper($method));
    array_push(self::$callbacks, $callback);
  }
  /**
   * 如果找不到路由，则定义回调
   */
  public static function error($callback) {
    self::$error_callback = $callback;
  }
  public static function haltOnMatch($flag = true) {
    self::$halts = $flag;
  }
  /**
   * 运行给定请求的回调
   */
  public static function dispatch(){
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];
    $searches = array_keys(static::$patterns);
    $replaces = array_values(static::$patterns);
    $found_route = false;
    self::$routes = preg_replace('/\/+/', '/', self::$routes);
    //检查路由是否定义没有正则表达式
    if (in_array($uri, self::$routes)) {
      $route_pos = array_keys(self::$routes, $uri);
      foreach ($route_pos as $route) {
        // 使用ANY选项匹配GET和POST请求
        if (self::$methods[$route] == $method || self::$methods[$route] == 'ANY' || (!empty(self::$maps[$route]) && in_array($method, self::$maps[$route]))) {
          $found_route = true;
          // 如果route不是对象
          if (!is_object(self::$callbacks[$route])) {
            // 基于/分隔符抓取所有部件
            $parts = explode('/',self::$callbacks[$route]);
            // 收集数组的最后一个索引
            $last = end($parts);
            // 抓取控制器名称和方法调用
            $segments = explode('@',$last);
            // 实例化控制器
            $controller = new $segments[0]();
            // 会话方式
            $controller->{$segments[1]}();
            if (self::$halts) return;
          } else {
            // 会话关闭
            call_user_func(self::$callbacks[$route]);
            if (self::$halts) return;
          }
        }
      }
    } else {
      // 检查是否使用正则表达式定义
      $pos = 0;
      foreach (self::$routes as $route) {
        if (strpos($route, ':') !== false) {
          $route = str_replace($searches, $replaces, $route);
        }
        if (preg_match('#^' . $route . '$#', $uri, $matched)) {
          if (self::$methods[$pos] == $method || self::$methods[$pos] == 'ANY' || (!empty(self::$maps[$pos]) && in_array($method, self::$maps[$pos]))) {
            $found_route = true;
            // 删除$matched [0]，因为[1]是第一个参数。
            array_shift($matched);
            if (!is_object(self::$callbacks[$pos])) {
              // 基于/分隔符抓取所有部件
              $parts = explode('/',self::$callbacks[$pos]);
              // 收集数组的最后一个索引
              $last = end($parts);
              // 抓取控制器名称和方法调用
              $segments = explode('@',$last);
              // 实例化控制器
              $controller = new $segments[0]();
              // 修复多个参数
              if (!method_exists($controller, $segments[1])) {
                echo "controller and action not found";
              } else {
                call_user_func_array(array($controller, $segments[1]), $matched);
              }
              if (self::$halts) return;
            } else {
              call_user_func_array(self::$callbacks[$pos], $matched);
              if (self::$halts) return;
            }
          }
        }
        $pos++;
      }
    }
    // 如果找不到路由，请运行错误回调
    if ($found_route == false) {
      if (!self::$error_callback) {
        self::$error_callback = function() {
          header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
          echo '404';
        };
      } else {
        if (is_string(self::$error_callback)) {
          self::get($_SERVER['REQUEST_URI'], self::$error_callback);
          self::$error_callback = null;
          self::dispatch();
          return ;
        }
      }
      call_user_func(self::$error_callback);
    }
  }
}