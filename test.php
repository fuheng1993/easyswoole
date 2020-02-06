<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/31
 * Time: 15:24
 */
require 'vendor/autoload.php';
use \Swoole\Coroutine as co;
go(function (){
    $chanel = new co\Channel(12);
    $wait = new \EasySwoole\Component\WaitGroup(12); //协程等待
    for ($i=1;$i<=12;$i++){
        $wait->add(); //等待增量1
        go(function ()use ($chanel,$wait,$i){
            $client = new \EasySwoole\HttpClient\HttpClient('http://easy.rmrf.top/index.php');
            $response = $client->get();
            $chanel->push(['index'=>1,'data'=>$response->getBody()]);//获取响应主体
            $wait->done(); //等待减少1
        });
    }
    $wait->wait(); //等待为0时执行以下代码
    echo '------------------'.PHP_EOL;
    while (true){
        if($chanel->isEmpty()){
            break;
        }
        $res = $chanel->pop();
        var_dump($res);
        //error_log($res.PHP_EOL,3,'channel.log');
    }
    echo '------------------'.PHP_EOL;
});