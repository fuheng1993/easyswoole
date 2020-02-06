<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/6
 * Time: 9:54
 */
namespace App\Utility;


use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Queue\Job;

class QueueProcess extends AbstractProcess
{

    protected function run($arg)
    {
        go(function (){
            MyQueue::getInstance()->consumer()->listen(function (Job $job){
                var_dump($job->toArray());
                var_dump('consumer');
            });
        });
    }
}