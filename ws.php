<?php
use Workerman\Worker;
require_once __DIR__ . '/vendor/autoload.php';

// 创建一个Worker监听2345端口，使用http协议通讯
$websocket_worker = new Worker("websocket://0.0.0.0:9501");

// 启动4个进程对外提供服务
$websocket_worker->count = 4;

$websocket_worker->onConnect = function($connection){
    echo "new connection from ip " . $connection->getRemoteIp() .PHP_EOL;
};

$websocket_worker->onWorkerStart = function($worker){
        $worker->testttt = mt_rand(0,1000);//从这个测试可以看的出来，当子进程创建成功之后，worker对象会在每个进程创建一次
    // echo 'Worker starting'.PHP_EOL;
};

$websocket_worker->onWorkerReload = function($worker){
    echo $worker->testttt.PHP_EOL;
    foreach($worker->connections as $connection)
    {
        $connection->send('worker reloading');
    }
};

// 接收到浏览器发送的数据时回复hello world给浏览器
$websocket_worker->onMessage = function($connection, $data)
{
    // 向浏览器发送hello world
    $connection->send('hello world');
};

// 运行worker
Worker::runAll();