<?php
 
//spl（php标准库）数据结构
 
/**
 * 栈（先进后出）
 */
function zan(){
    $stack = new SplStack();
    $stack->push('data1');//入栈（先进后出）
    $stack->push('data2');//入栈
    $stack->push('data3');//入栈
     
    echo $stack->pop().PHP_EOL;//出栈
    echo $stack->pop().PHP_EOL;//出栈
    echo $stack->pop().PHP_EOL;//出栈
}

 
 
 /**
 *队列(先进先出）
 */

function duilie(){
    $queue = new SplQueue();
    $queue->enqueue('data4');//入队列
    $queue->enqueue('data5');//入队列
    $queue->enqueue('data6');//入队列
    
    echo $queue->dequeue().PHP_EOL;//出队列
    echo $queue->dequeue().PHP_EOL;//出队列
    echo $queue->dequeue().PHP_EOL;//出队列
}
 
 
/**
 * 堆
 */
function dui(){
    $heap = new SplMinHeap();
    $heap->insert('data8');//入堆
    $heap->insert('data9');//入堆
    $heap->insert('data10');//入堆
    
    echo $heap->extract().PHP_EOL;//从堆中提取数据
    echo $heap->extract().PHP_EOL;//从堆中提取数据
    echo $heap->extract().PHP_EOL;//从堆中提取数据
}
  
/**
 * 固定数组（不论使不使用，都会分配相应的内存空间）
 */
$array = new SplFixedArray(15);
$array['0'] = 54;
$array['6'] = 69;
$array['10'] = 32;
var_dump($array);
