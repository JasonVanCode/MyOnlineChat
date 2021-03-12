<?php
use Workerman\Worker;
use Workerman\Lib\Timer;
require_once __DIR__ . '/vendor/autoload.php';

define('HEARTBEAT',55);

$tcp = new Worker('tcp://0.0.0.0:2345');
//开启多进程
$tcp->count = 4;

$tcp->onWorkerStart = function($worker){
    echo '进程启动，进程id是'.$worker->id.PHP_EOL;
    Timer::add(10,function() use ($worker){
        $time_now = time();
        foreach($worker->connections  as $connection){
            if(empty($connection->lastMessageTime)){
                $connection->lastMessageTime = $time_now ;
            }
            if($time_now - $connection->lastMessageTime > HEARTBEAT){
                $connection->close();
            }
        }
    });
    // if($worker->id == 0){
        // Timer::add(2,function(){
        //     echo '进程id为0在执行定时器任务'.PHP_EOL;
        // });
    // }
};
$tcp->onConnect  = function($connection){
    echo '客户端连接的ip地址是'.$connection->getRemoteIp().PHP_EOL;
};

$tcp->onMessage = function($connection, $data){
    $connection->lastMessageTime = time();
    echo '进程id为'.$connection->id.'接收到数据为'.$data.PHP_EOL;
    echo '向客户端ip为：'.$connection->getRemoteIp().'端口为：'.$connection->getRemotePort().'发送数据'.PHP_EOL;
    $connection->send('copy that');
};

$tcp->onClose = function($connection){
    echo '进程id为'.$connection->worker->id.'下面的连接id为'.$connection->id.'断开连接'.PHP_EOL;
};

$tcp::runAll();


