<?php

use Workerman\Worker;
use Workerman\Protocols\Http\Request;
use Workerman\Protocols\Http\Response;

require_once __DIR__.'/vendor/autoload.php';

$http_server = new Worker('http://0.0.0.0:8866');

$http_server->count = 4;


$http_server->onMessage = function($connection,$request){
    
    $path = $request->path();
    if( $path === '/favicon.ico'){
        return $connection->send('');
    }
    if( $path == '/'){
        $file = __DIR__.'/web/index.php';
        return $connection->send(exec_php_file($file));
    }
    $other_file = __DIR__.'/web'. $path;
    $response = (new Response())->withFile($other_file);
    $connection->send($response);
};

function exec_php_file($file) {
    \ob_start();
    // Try to include php file.
    try {
        include $file;
    } catch (\Exception $e) {
        echo $e;
    }
    return \ob_get_clean();
}

Worker::runAll();