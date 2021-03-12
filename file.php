<?php

$file = __DIR__.'/wechat.php';
if(!file_exists($file)){
    die('文件不存在');
}
$handle = fopen($file,'r');
$content = '';
while(!feof($handle)){
    $content .= fgets($handle,1024);
}
fclose($handle);
var_dump($content);