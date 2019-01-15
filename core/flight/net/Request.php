<?php
/**
 * Flight: 可扩展的微框架
 *
 */
namespace flight\net;

use flight\util\Collection;
/**
 * Request类表示HTTP请求。
 * 来自的数据所有超级全局变量$ _GET，$ _POST，$ _COOKIE和$ _FILES
 * 存储并可通过Request对象访问
 *
 * 默认的请求属性是：
 *   url       - 请求的URL
 *   base      - URL的父子目录
 *   method    - 请求方法(GET, POST, PUT, DELETE)
 *   referrer  - 引荐来源网址
 *   ip        - 客户端的IP地址
 *   ajax      - 请求是否是AJAX请求
 *   scheme    - 服务器协议(http, https)
 *   user_agent- 浏览器信息
 *   type      - 内容类型
 *   length    - 内容长度
 *   query     - 查询字符串参数
 *   data      - 发布参数
 *   cookies   - Cookie参数
 *   files     - 上传的文件
 *   secure    - 连接安全
 *   accept    - HTTP接受参数
 *   proxy_ip  - 客户端的代理IP地址
 */
class Request {
    /**
     * @var string URL being requested
     */
    public $url;
    /**
     * @var string Parent subdirectory of the URL
     */
    public $base;
    /**
     * @var string Request method (GET, POST, PUT, DELETE)
     */
    public $method;
    /**
     * @var string Referrer URL
     */
    public $referrer;
    /**
     * @var string IP address of the client
     */
    public $ip;
    /**
     * @var bool Whether the request is an AJAX request
     */
    public $ajax;
    /**
     * @var string Server protocol (http, https)
     */
    public $scheme;
    /**
     * @var string Browser information
     */
    public $user_agent;
    /**
     * @var string Content type
     */
    public $type;
    /**
     * @var int Content length
     */
    public $length;
    /**
     * @var \flight\util\Collection Query string parameters
     */
    public $query;
    /**
     * @var \flight\util\Collection Post parameters
     */
    public $data;
    /**
     * @var \flight\util\Collection Cookie parameters
     */
    public $cookies;
    /**
     * @var \flight\util\Collection Uploaded files
     */
    public $files;
    /**
     * @var bool Whether the connection is secure
     */
    public $secure;
    /**
     * @var string HTTP accept parameters
     */
    public $accept;
    /**
     * @var string Proxy IP address of the client
     */
    public $proxy_ip;
    /**
     * 构造函数
     *
     * @param array $config Request configuration
     */
    public function __construct($config = array()) {
        // 默认属性
        if (empty($config)) {
            $config = array(
                'url' => str_replace('@', '%40', self::getVar('REQUEST_URI', '/')),
                'base' => str_replace(array('\\',' '), array('/','%20'), dirname(self::getVar('SCRIPT_NAME'))),
                'method' => self::getMethod(),
                'referrer' => self::getVar('HTTP_REFERER'),
                'ip' => self::getVar('REMOTE_ADDR'),
                'ajax' => self::getVar('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest',
                'scheme' => self::getVar('SERVER_PROTOCOL', 'HTTP/1.1'),
                'user_agent' => self::getVar('HTTP_USER_AGENT'),
                'type' => self::getVar('CONTENT_TYPE'),
                'length' => self::getVar('CONTENT_LENGTH', 0),
                'query' => new Collection($_GET),
                'data' => new Collection($_POST),
                'cookies' => new Collection($_COOKIE),
                'files' => new Collection($_FILES),
                'secure' => self::getVar('HTTPS', 'off') != 'off',
                'accept' => self::getVar('HTTP_ACCEPT'),
                'proxy_ip' => self::getProxyIpAddress()
            );
        }
        $this->init($config);
    }
    /**
     * 初始化请求属性
     *
     * @param array $properties 请求属性数组
     */
    public function init($properties = array()) {
        // 设置所有已定义的属性
        foreach ($properties as $name => $value) {
            $this->$name = $value;
        }
        // 获取没有基目录的请求的URL
        if ($this->base != '/' && strlen($this->base) > 0 && strpos($this->url, $this->base) === 0) {
            $this->url = substr($this->url, strlen($this->base));
        }
        // 默认url
        if (empty($this->url)) {
            $this->url = '/';
        }
        // 使用$ _GET合并URL查询参数
        else {
            $_GET += self::parseQuery($this->url);
            $this->query->setData($_GET);
        }
        // 检查JSON输入
        if (strpos($this->type, 'application/json') === 0) {
            $body = $this->getBody();
            if ($body != '') {
                $data = json_decode($body, true);
                if ($data != null) {
                    $this->data->setData($data);
                }
            }
        }
    }
    /**
     * 获取请求的主体。
     *
     * @return string 原始HTTP请求正文
     */
    public static function getBody() {
        static $body;
        if (!is_null($body)) {
            return $body;
        }
        $method = self::getMethod();
        if ($method == 'POST' || $method == 'PUT' || $method == 'PATCH') {
            $body = file_get_contents('php://input');
        }
        return $body;
    }
    /**
     * 获取请求方法
     *
     * @return string
     */
    public static function getMethod() {
        $method = self::getVar('REQUEST_METHOD', 'GET');
        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            $method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
        }
        elseif (isset($_REQUEST['_method'])) {
            $method = $_REQUEST['_method'];
        }
        return strtoupper($method);
    }
    /**
     * 获取真正的远程IP地址
     *
     * @return string IP address
     */
    public static function getProxyIpAddress() {
        static $forwarded = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED'
        );
        $flags = \FILTER_FLAG_NO_PRIV_RANGE | \FILTER_FLAG_NO_RES_RANGE;
        foreach ($forwarded as $key) {
            if (array_key_exists($key, $_SERVER)) {
                sscanf($_SERVER[$key], '%[^,]', $ip);
                if (filter_var($ip, \FILTER_VALIDATE_IP, $flags) !== false) {
                    return $ip;
                }
            }
        }
        return '';
    }
    /**
     * 如果未提供，则使用$ default从$ _SERVER获取变量
     *
     * @param string $var Variable name
     * @param string $default Default value to substitute
     * @return string Server variable value
     */
    public static function getVar($var, $default = '') {
        return isset($_SERVER[$var]) ? $_SERVER[$var] : $default;
    }
    /**
     * 从URL解析查询参数
     *
     * @param string $url URL string
     * @return array Query parameters
     */
    public static function parseQuery($url) {
        $params = array();
        $args = parse_url($url);
        if (isset($args['query'])) {
            parse_str($args['query'], $params);
        }
        return $params;
    }
}