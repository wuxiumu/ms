<?php

namespace core\lib\exceptions;

use Exception;

class CoreHttpException extends Exception
{
    private static $hadException = false;
    /**
     * 响应异常code
     *
     * @var array
     */
    private $httpCode = [
        // 缺少参数或者必传参数为空
        400 => 'Bad Request',
        // 没有访问权限
        403 => 'Forbidden',
        // 访问的资源不存在
        404 => 'Not Found',
        // 代码错误
        500 => 'Internet Server Error',
        // Remote Service error
        503 => 'Service Unavailable'
    ];
    /**
     * 构造函数
     *
     * @param int $code excption code
     * @param string $extra 错误信息补充
     */
    public function __construct($code = 200, $extra = '')
    {
        $this->code = $code;
        if (empty($extra)) {
            $this->message = $this->httpCode[$code];
            return;
        }
        $this->message = $extra . ' ' . $this->httpCode[$code];
    }
    /**
     * rest 风格http响应
     *
     * @return json
     */
    public function reponse()
    {
        $data = [
            '__coreError' => [
                'code'    => $this->getCode(),
                'message' => $this->getMessage(),
                'infomations'  => [
                    'file'  => $this->getFile(),
                    'line'  => $this->getLine(),
                    'trace' => $this->getTrace(),
                ]
            ]
        ];
        /**
         * response
         * 
         * 错误处理handle里 fatal error是通过register_shutdown_function注册的函数获取的
         * 防止fatal error时输出两会json 所以response也注册到register_shutdown_function的队列中
         * 
         * TODO 这个地方要重构
         */
        register_shutdown_function(function () use ($data)
        {
            header('Content-Type:Application/json; Charset=utf-8');
            die(json_encode($data, JSON_UNESCAPED_UNICODE));
        });
    }    
}