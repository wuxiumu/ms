<?php
/**
 * Flight: 可扩展的微框架
 *
 */
namespace flight\util;
/**
 * Collection类允许您访问一组数据
 * 同时使用数组和对象表示法
 *
 */
class Collection implements \ArrayAccess, \Iterator, \Countable {
    /**
     * 收集数据
     *
     * @var array
     */
    private $data;
    /**
     * 构造函数
     *
     * @param array $data Initial data
     */
    public function __construct(array $data = array()) {
        $this->data = $data;
    }
    /**
     * 获取项
     *
     * @param string $key Key
     * @return mixed Value
     */
    public function __get($key) {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }
    /**
     * 设置项
     *
     * @param string $key Key
     * @param mixed $value Value
     */
    public function __set($key, $value) {
        $this->data[$key] = $value;
    }
    /**
     * 检查项目是否存在
     *
     * @param string $key Key
     * @return bool Item status
     */
    public function __isset($key) {
        return isset($this->data[$key]);
    }
    /**
     * 移除项
     *
     * @param string $key Key
     */
    public function __unset($key) {
        unset($this->data[$key]);
    }
    /**
     * 在偏移量处获取项
     *
     * @param string $offset Offset
     * @return mixed Value
     */
    public function offsetGet($offset) {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }
    /**
     * 在偏移处设置项
     *
     * @param string $offset Offset
     * @param mixed $value Value
     */
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->data[] = $value;
        }
        else {
            $this->data[$offset] = $value;
        }
    }
    /**
     * 检查偏移处是否存在项
     *
     * @param string $offset Offset
     * @return bool Item status
     */
    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }
    /**
     * 删除偏移量处的项
     *
     * @param string $offset Offset
     */
    public function offsetUnset($offset) {
        unset($this->data[$offset]);
    }
    /**
     * 重置集合
     */
    public function rewind() {
        reset($this->data);
    }
 
    /**
     * 获取当前集合项
     *
     * @return mixed Value
     */ 
    public function current() {
        return current($this->data);
    }
 
    /**
     * 获取当前集合密钥
     *
     * @return mixed Value
     */ 
    public function key() {
        return key($this->data);
    }
 
    /**
     * 获取下一个集合值
     *
     * @return mixed Value
     */ 
    public function next() 
    {
        return next($this->data);
    }
 
    /**
     * 检查当前集合密钥是否有效
     *
     * @return bool Key status
     */ 
    public function valid()
    {
        $key = key($this->data);
        return ($key !== NULL && $key !== FALSE);
    }
    /**
     * 获取集合的大小
     *
     * @return int Collection size
     */
    public function count() {
        return sizeof($this->data);
    }
    /**
     * 获取项键
     *
     * @return array Collection keys
     */
    public function keys() {
        return array_keys($this->data);
    }
    /**
     * 获取收集数据
     *
     * @return array Collection data
     */
    public function getData() {
        return $this->data;
    }
    /**
     * 收集的数据集
     *
     * @param array $data New collection data
     */
    public function setData(array $data) {
        $this->data = $data;
    }
    /**
     * 从集合中删除所有项
     */
    public function clear() {
        $this->data = array();
    }
}