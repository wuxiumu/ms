<?php
/**
 * Flight: 可扩展的微框架
 *
 */
namespace flight\template;
/**
 * View类表示要显示的输出。
 * 它提供了管理视图数据的方法，并将数据插入渲染时查看模板。
 *
 */
class View {
    /**
     * 视图模板的位置
     *
     * @var string
     */
    public $path;
    /**
     * 文件扩展名
     *
     * @var string
     */
    public $extension = '.php';
    /**
     * 视图变量
     *
     * @var array
     */
    protected $vars = array();
    /**
     * 模板文件
     *
     * @var string
     */
    private $template;
    /**
     * 构造函数
     *
     * @param string $path Path to templates directory
     */
    public function __construct($path = '.') {
        $this->path = $path;
    }
    /**
     * 获取模板变量
     *
     * @param string $key Key
     * @return mixed Value
     */
    public function get($key) {
        return isset($this->vars[$key]) ? $this->vars[$key] : null;
    }
    /**
     * 设置模板变量
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
     * 检查是否设置了模板变量
     *
     * @param string $key Key
     * @return boolean If key exists
     */
    public function has($key) {
        return isset($this->vars[$key]);
    }
    /**
     * 取消设置模板变量。如果没有传入任何键，则清除所有变量
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
     * 呈现模板
     *
     * @param string $file Template file
     * @param array $data Template data
     * @throws \Exception If template not found
     */
    public function render($file, $data = null) {
        $this->template = $this->getTemplate($file);
        if (!file_exists($this->template)) {
            throw new \Exception("Template file not found: {$this->template}.");
        }
        if (is_array($data)) {
            $this->vars = array_merge($this->vars, $data);
        }
        extract($this->vars);
        include $this->template;
    }
    /**
     * 获取模板的输出
     *
     * @param string $file Template file
     * @param array $data Template data
     * @return string Output of template
     */
    public function fetch($file, $data = null) {
        ob_start();
        $this->render($file, $data);
        $output = ob_get_clean();
        return $output;
    }
    /**
     * 检查模板文件是否存在
     *
     * @param string $file Template file
     * @return bool Template file exists
     */
    public function exists($file) {
        return file_exists($this->getTemplate($file));
    }
    /**
     * 获取模板文件的完整路径
     *
     * @param string $file Template file
     * @return string Template file location
     */
    public function getTemplate($file) {
        $ext = $this->extension;
        if (!empty($ext) && (substr($file, -1 * strlen($ext)) != $ext)) {
            $file .= $ext;
        }
        if ((substr($file, 0, 1) == '/')) {
            return $file;
        }
        
        return $this->path.'/'.$file;
    }
    /**
     * 显示转义输出
     *
     * @param string $str String to escape
     * @return string Escaped string
     */
    public function e($str) {
        echo htmlentities($str);
    }
}