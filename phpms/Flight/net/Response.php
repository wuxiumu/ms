<?php
/**
 * Flight: 可扩展的微框架
 *
 */
namespace flight\net;
/**
 * Response类表示HTTP响应 
 * 该对象包含响应标头，HTTP状态代码和响应身体。
 *
 */
class Response {
    /**
     * @var int HTTP status
     */
    protected $status = 200;
    /**
     * @var array HTTP headers
     */
    protected $headers = array();
    /**
     * @var string HTTP response body
     */
    protected $body;
    /**
     * @var bool HTTP response sent
     */
    protected $sent = false;
    /**
     * @var array HTTP status codes
     */
    public static $codes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required'
    );
    /**
     * 设置响应的HTTP状态
     *
     * @param int $code HTTP status code.
     * @return object|int Self reference
     * @throws \Exception If invalid status code
     */
    public function status($code = null) {
        if ($code === null) {
            return $this->status;
        }
        if (array_key_exists($code, self::$codes)) {
            $this->status = $code;
        }
        else {
            throw new \Exception('Invalid status code.');
        }
        return $this;
    }
    /**
     * 为响应添加标头
     *
     * @param string|array $name Header name or array of names and values
     * @param string $value Header value
     * @return object Self reference
     */
    public function header($name, $value = null) {
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                $this->headers[$k] = $v;
            }
        }
        else {
            $this->headers[$name] = $value;
        }
        return $this;
    }
    /**
     * 返回响应中的标头
     * @return array
     */
    public function headers() {
        return $this->headers;
    }
    /**
     * 将内容写入响应正文
     *
     * @param string $str Response content
     * @return object Self reference
     */
    public function write($str) {
        $this->body .= $str;
        return $this;
    }
    /**
     * 清除响应
     *
     * @return object Self reference
     */
    public function clear() {
        $this->status = 200;
        $this->headers = array();
        $this->body = '';
        return $this;
    }
    /**
     * 设置响应的缓存标头
     *
     * @param int|string $expires Expiration time
     * @return object Self reference
     */
    public function cache($expires) {
        if ($expires === false) {
            $this->headers['Expires'] = 'Mon, 26 Jul 1997 05:00:00 GMT';
            $this->headers['Cache-Control'] = array(
                'no-store, no-cache, must-revalidate',
                'post-check=0, pre-check=0',
                'max-age=0'
            );
            $this->headers['Pragma'] = 'no-cache';
        }
        else {
            $expires = is_int($expires) ? $expires : strtotime($expires);
            $this->headers['Expires'] = gmdate('D, d M Y H:i:s', $expires) . ' GMT';
            $this->headers['Cache-Control'] = 'max-age='.($expires - time());
            if (isset($this->headers['Pragma']) && $this->headers['Pragma'] == 'no-cache'){
                unset($this->headers['Pragma']);
            }
        }
        return $this;
    }
    /**
     * 发送HTTP标头
     *
     * @return object Self reference
     */
    public function sendHeaders() {
        // 发送状态代码标头
        if (strpos(php_sapi_name(), 'cgi') !== false) {
            header(
                sprintf(
                    'Status: %d %s',
                    $this->status,
                    self::$codes[$this->status]
                ),
                true
            );
        }
        else {
            header(
                sprintf(
                    '%s %d %s',
                    (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1'),
                    $this->status,
                    self::$codes[$this->status]),
                true,
                $this->status
            );
        }
        // 发送其他标题
        foreach ($this->headers as $field => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    header($field.': '.$v, false);
                }
            }
            else {
                header($field.': '.$value);
            }
        }
        // 发送内容长度
        $length = $this->getContentLength();
        if ($length > 0) {
            header('Content-Length: '.$length);
        }
        return $this;
    }
    /**
     * 获取内容长度
     *
     * @return string Content length
     */
    public function getContentLength() {
        return extension_loaded('mbstring') ?
            mb_strlen($this->body, 'latin1') :
            strlen($this->body);
    }
    /**
     * 获取是否发送了响应
     */
    public function sent() {
        return $this->sent;
    }
    /**
     * 发送HTTP响应
     */
    public function send() {
        if (ob_get_length() > 0) {
            ob_end_clean();
        }
        if (!headers_sent()) {
            $this->sendHeaders();
        }
        echo $this->body;
        $this->sent = true;
    }
}