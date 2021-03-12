<?php
use Workerman\Worker;
use Workerman\Lib\Timer;
use Channel\Server as ChannelServer;
use Channel\Client as ChannelClient;
use GlobalData\Server as GlobalServer;
use GlobalData\Client as GlobalClient;

require_once __DIR__.'/vendor/autoload.php';

$ws_worker = new Worker('websocket://0.0.0.0:2000');

$channel_server = new ChannelServer('0.0.0.0',2207);

$globaldata_server = new GlobalServer('0.0.0.0',2208);

$ws_worker->count = 4;

//添加心跳机制
define('HEARTBEAT',250000);

$ws_worker->onWorkerStart = function($worker)
{
    global $globaldata;
    $globaldata = new GlobalClient('0.0.0.0:2208');
    $globaldata->user_list = [];
    // echo '进程id为'.$worker->id.'启动'.PHP_EOL;
    Timer::add(10,function() use($worker){
        $now_time = time();
        foreach($worker->connections as $connection){
            if(empty($connection->lastMessageTime)){
                $connection->lastMessageTime = $now_time;
            }
            if($now_time - $connection->lastMessageTime > HEARTBEAT){
                $connection->close();
            }
        }
    });
    // Channel客户端连接到Channel服务端
    ChannelClient::connect('127.0.0.1', 2207);
    // 订阅broadcast事件，并注册事件回调
    ChannelClient::on('broadcast',function($eventdata) use($worker){
        $data = json_decode($eventdata,true);
        echo 'AAA'.PHP_EOL;
        foreach($worker->connections as $connection){
            if($data['chatuser_id'] == $connection->id){
                $connection->send(json_encode(['issuccess'=>1,'content'=>$data['content'],'send_id'=>$data['my_id']]));
            }
        }
    });
};
// $ws_worker->onConnect = function($connection){
//     //设置唯一id
//     $connection->id = (string)$connection->worker->id.':'.(string)$connection->id;
//     echo $connection->id.'连接'.PHP_EOL;
// };

$ws_worker->onMessage = function($connection,$data)
{
    global $globaldata;
    echo '进程id为'.$connection->worker->id.'下面的第'.$connection->id.'连接，接收到数据为：'.$data.PHP_EOL;
    $connection->lastMessageTime = time();
    //判断是否是第一次提交
    $data = json_decode($data,true);
    if(isset($data['userid'])){
        $connection->id = $data['userid'];
        do
        {
            $old_value = $new_value = $globaldata->user_list;
            $new_value[] = $data['userid'];
        }
        while(!$globaldata->cas('user_list', $old_value, $new_value));
        echo '该用户的连接id是'.$connection->id.PHP_EOL;
    }else{
        //判断发送数据的对象是否存在
        if(!isset($globaldata->user_list[$data['chatuser_id']])){
            //发送数据
            $connection->send(json_encode(['issuccess'=>0,'content'=>'该用户不在线']));    
        }else{
            //数据广播
            $senddata = json_encode($data);
            ChannelClient::publish('broadcast',$senddata);
        }
    }

};

$ws_worker->onClose = function($connection){
    echo '进程id为'.$connection->worker->id.'下面的第'.$connection->id.'连接断开'.PHP_EOL;
    global $globaldata;
    if(isset($globaldata->user_list[$connection->id])){
        unset($globaldata->user_list[$connection->id]);
    }
};

if(!defined('GLOBAL_START')){
    $ws_worker::runAll();
}