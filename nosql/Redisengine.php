<?php

class Redisengine
{
    /**
     * Redis是一个开源的使用ANSI C语言编写
     * 支持网络、可基于内存
     * 亦可持久化的日志型、Key-Value数据库，并提供多种语言的API。
     * 
     */

    //1-简单字符串缓存(Simple string cache)
    public  function sscache(){
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $strCacheKey  = 'Test_bihu';

        //SET 应用
        $arrCacheData = [
            'name' => 'job',
            'sex'  => '男',
            'age'  => '30'
        ];
        $redis->set($strCacheKey, json_encode($arrCacheData));
        $redis->expire($strCacheKey, 30);  # 设置30秒后过期
        $json_data = $redis->get($strCacheKey);
        $data = json_decode($json_data);
        print_r($data->age); //输出数据

        //HSET 应用
        $arrWebSite = [
            'google' => [
                'google.com',
                'google.com.hk'
            ],
        ];
        $redis->hSet($strCacheKey, 'google', json_encode($arrWebSite['google']));
        $json_data = $redis->hGet($strCacheKey, 'google');
        $data = json_decode($json_data);
        print_r($data); //输出数据 
    }

    //2-简单队列实战(Simple queue combat)
    public function sscom(){
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $strQueueName  = 'Test_bihu_queue';

        //进队列
       /* $redis->rpush($strQueueName, json_encode(['uid' => 1,'name' => 'Job']));
        $redis->rpush($strQueueName, json_encode(['uid' => 2,'name' => 'Tom']));
        $redis->rpush($strQueueName, json_encode(['uid' => 3,'name' => 'John']));*/
        echo "---- 进队列成功 ---- <br /><br />";

        //查看队列
        $strCount = $redis->lrange($strQueueName, 0, -1);
        echo "当前队列数据为： <br />";
        print_r($strCount);

        //出队列
        $redis->lpop($strQueueName);
        echo "<br /><br /> ---- 出队列成功 ---- <br /><br />";

        //查看队列
        $strCount = $redis->lrange($strQueueName, 0, -1);
        echo "当前队列数据为： <br />";
        print_r($strCount); 
    }

    //3-简单发布订阅(Simple publishing subscription)
    public function spsub(){
        //以下是 pub.php 文件的内容 cli下运行
        ini_set('default_socket_timeout', -1);
        $redis->connect('127.0.0.1', 6379);
        $strChannel = 'Test_bihu_channel';

        //发布
        $redis->publish($strChannel, "来自{$strChannel}频道的推送");
        echo "---- {$strChannel} ---- 频道消息推送成功～ <br/>";
        $redis->close(); 

        //以下是 sub.php 文件内容 cli下运行
        ini_set('default_socket_timeout', -1);
        $redis->connect('127.0.0.1', 6379);
        $strChannel = 'Test_bihu_channel';

        //订阅
        echo "---- 订阅{$strChannel}这个频道，等待消息推送...----  <br/><br/>";
        $redis->subscribe([$strChannel], 'callBackFun');
        function callBackFun($redis, $channel, $msg)
        {
            print_r([
                'redis'   => $redis,
                'channel' => $channel,
                'msg'     => $msg
            ]);
        }        
    }

    //4-简单计数器(Simple counter)
    public function simcount(){
        $redis = new Redis();        
        $redis->connect('127.0.0.1', 6379);
        $strKey = 'Test_bihu_comments';

        //设置初始值
        $redis->set($strKey, 0);

        $redis->INCR($strKey);  //+1
        $redis->INCR($strKey);  //+1
        $redis->INCR($strKey);  //+1

        $strNowCount = $redis->get($strKey);

        echo "---- 当前数量为{$strNowCount}。 ---- ";        
    }

    //5-排行榜(Leaderboard)
    public function leaderb(){
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $strKey = 'Test_bihu_score';

        //存储数据
        $redis->zadd($strKey, '50', json_encode(['name' => 'Tom']));
        $redis->zadd($strKey, '70', json_encode(['name' => 'John']));
        $redis->zadd($strKey, '90', json_encode(['name' => 'Jerry']));
        $redis->zadd($strKey, '30', json_encode(['name' => 'Job']));
        $redis->zadd($strKey, '100', json_encode(['name' => 'LiMing']));

        $dataOne = $redis->ZREVRANGE($strKey, 0, -1, true);
        echo "---- {$strKey}由大到小的排序 ---- <br /><br />";
        print_r($dataOne);

        $dataTwo = $redis->ZRANGE($strKey, 0, -1, true);
        echo "<br /><br />---- {$strKey}由小到大的排序 ---- <br /><br />";
        print_r($dataTwo);        
    }

    //6-简单字符串悲观锁(Simple string pessimistic lock) 
    public function sspl(){
        // 定义锁标识
        $key = 'Test_bihu_lock';

        // 获取锁
        $is_lock = $this->lock($key, 10);
        if ($is_lock) {
            echo 'get lock success<br>';
            echo 'do sth..<br>';
            sleep(5);
            echo 'success<br>';
            $this->unlock($key);
        } else { //获取锁失败
            echo 'request too frequently<br>';
        }
 
        
    }

    //7-简单事务的乐观锁(Optimistic lock for simple transactions)
    public function olfst(){
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $strKey = 'Test_bihu_age';

        $redis->set($strKey,10);

        $age = $redis->get($strKey);

        echo "---- Current Age:{$age} ---- <br/><br/>";

        $redis->watch($strKey);

        // 开启事务
        $redis->multi();

        //在这个时候新开了一个新会话执行
        $redis->set($strKey,30);  //新会话

        echo "---- Current Age:{$age} ---- <br/><br/>"; //30

        $redis->set($strKey,20);

        $redis->exec();

        $age = $redis->get($strKey);

        echo "---- Current Age:{$age} ---- <br/><br/>"; //30

        //当exec时候如果监视的key从调用watch后发生过变化，则整个事务会失败        
    } 

    /**
     * 获取锁
     * @param  String  $key    锁标识
     * @param  Int     $expire 锁过期时间
     * @return Boolean
     */
    public function lock($key = '', $expire = 5) {
        $is_lock = $this->_redis->setnx($key, time()+$expire);
        //不能获取锁
        if(!$is_lock){
            //判断锁是否过期
            $lock_time = $this->_redis->get($key);
            //锁已过期，删除锁，重新获取
            if (time() > $lock_time) {
                unlock($key);
                $is_lock = $this->_redis->setnx($key, time() + $expire);
            }
        }

        return $is_lock? true : false;
    }

    /**
     * 释放锁
     * @param  String  $key 锁标识
     * @return Boolean
     */
    public function unlock($key = ''){
        return $this->_redis->del($key);
    } 
}