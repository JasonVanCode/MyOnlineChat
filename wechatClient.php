<?php

$fp = stream_socket_client('tcp://0.0.0.0:2345',$errno,$errstr,30);

if(!$fp){
    echo $errstr.PHP_EOL;
    return;
}
fwrite($fp,'hello world'.PHP_EOL);
while (!feof($fp)) {
    echo fgets($fp, 1024);
}
// echo fread($fp,8192);
fclose($fp);