<?php
/**
 * Flight: 可扩展的微框架
 *
 */
namespace flight\net;
/**
 * 路由类负责将HTTP请求路由到分配的回调函数。
 * 路由器尝试根据一系列的URL模式匹配请求的URL。
 *
 */
class Route {
    /**
     * @var string URL pattern
     */
    public $pattern;
    /**
     * @var mixed Callback function
     */
    public $callback;
    /**
     * @var array HTTP methods
     */
    public $methods = array();
    /**
     * @var array Route parameters
     */
    public $params = array();
    /**
     * @var string Matching regular expression
     */
    public $regex;
    /**
     * @var string URL splat content
     */
    public $splat = '';
    /**
     * @var boolean Pass self in callback parameters
     */
    public $pass = false;
    /**
     * 构造函数
     *
     * @param string $pattern URL pattern
     * @param mixed $callback Callback function
     * @param array $methods HTTP methods
     * @param boolean $pass Pass self in callback parameters
     */
    public function __construct($pattern, $callback, $methods, $pass) {
        $this->pattern = $pattern;
        $this->callback = $callback;
        $this->methods = $methods;
        $this->pass = $pass;
    }
    /**
     * 检查URL是否与路由模式匹配。还解析URL中的命名参数
     *
     * @param string $url Requested URL
     * @param boolean $case_sensitive Case sensitive matching
     * @return boolean Match status
     */
    public function matchUrl($url, $case_sensitive = false) {
        // 通配符或完全匹配
        if ($this->pattern === '*' || $this->pattern === $url) {
            return true;
        }
        $ids = array();
        $last_char = substr($this->pattern, -1);
        // 得到 splat
        if ($last_char === '*') {
            $n = 0;
            $len = strlen($url);
            $count = substr_count($this->pattern, '/');
            for ($i = 0; $i < $len; $i++) {
                if ($url[$i] == '/') $n++;
                if ($n == $count) break;
            }
            $this->splat = (string)substr($url, $i+1);
        }
        // 生成用于匹配的 regex
        $regex = str_replace(array(')','/*'), array(')?','(/?|/.*?)'), $this->pattern);
        $regex = preg_replace_callback(
            '#@([\w]+)(:([^/\(\)]*))?#',
            function($matches) use (&$ids) {
                $ids[$matches[1]] = null;
                if (isset($matches[3])) {
                    return '(?P<'.$matches[1].'>'.$matches[3].')';
                }
                return '(?P<'.$matches[1].'>[^/\?]+)';
            },
            $regex
        );
        // 固定尾随斜线
        if ($last_char === '/') {
            $regex .= '?';
        }
        // 允许尾随斜杠
        else {
            $regex .= '/?';
        }
        // 尝试匹配路由和命名参数
        if (preg_match('#^'.$regex.'(?:\?.*)?$#'.(($case_sensitive) ? '' : 'i'), $url, $matches)) {
            foreach ($ids as $k => $v) {
                $this->params[$k] = (array_key_exists($k, $matches)) ? urldecode($matches[$k]) : null;
            }
            $this->regex = $regex;
            return true;
        }
        return false;
    }
    /**
     * 检查HTTP方法是否与路由方法匹配
     *
     * @param string $method HTTP method
     * @return bool Match status
     */
    public function matchMethod($method) {
        return count(array_intersect(array($method, '*'), $this->methods)) > 0;
    }
}