<?php
namespace EasySwoole\EasySwoole;


use App\Process\HotReload;
use App\Template;
use App\Utility\MyQueue;
use App\Utility\QueueProcess;
use EasySwoole\Component\Di;
use EasySwoole\Component\Timer;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\ORM\DbManager;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\Queue\Driver\Redis;
use EasySwoole\Queue\Job;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\RedisPool\RedisPool;
use EasySwoole\Template\Render;
use EasySwoole\Utility\Time;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
        Di::getInstance()->set(SysConst::LOGGER_HANDLER, new \App\Logger\Logger());
        $configData = Config::getInstance()->getConf('MYSQL');
        $config = new \EasySwoole\ORM\Db\Config($configData);
        DbManager::getInstance()->addConnection(new Connection($config));
    }

    public static function mainServerCreate(EventRegister $register)
    {

//        /**
//         * ****************   REDIS 连接池 配置    ****************
//         */
//        $redisPoolConfig=\EasySwoole\RedisPool\Redis::getInstance()->register('redis',new \EasySwoole\Redis\Config\RedisConfig());
//        //配置连接池连接数
//        $redisPoolConfig->setMinObjectNum(5);
//        $redisPoolConfig->setMaxObjectNum(20);
        /**
         * ****************   MYSQL ORM 配置    ****************
         */
        $configData = Config::getInstance()->getConf('MYSQL');
        $config = new \EasySwoole\ORM\Db\Config($configData);
        DbManager::getInstance()->addConnection(new Connection($config));
        DbManager::getInstance()->onQuery(function ($res, $builder, $start) {
            var_dump($res->toArray());
            $queryTime = 1;
            if (bcsub(time(), $start, 1) > $queryTime) {
                 var_dump('慢查询');
            }
        });
        /**
         * ****************   服务热重启    ****************
         */
        $swooleServer = ServerManager::getInstance()->getSwooleServer();
        $swooleServer->addProcess((new HotReload('HotReload', ['disableInotify' => false]))->getProcess());

        /**
         * ****************   实例化该Render,并注入你的驱动配置    ****************
         */
        Render::getInstance()->getConfig()->setRender(new Template());
        Render::getInstance()->attachServer(ServerManager::getInstance()->getSwooleServer());

//        /**
//         *    Queue 队列注册驱动
//         */
//        $config = new RedisConfig(['host'=>'127.0.0.1']);
//        $redis = new RedisPool($config);
//        $driver = new Redis($redis);
//        MyQueue::getInstance($driver);
//        //注册一个消费进程
//        ServerManager::getInstance()->addProcess(new QueueProcess());
        //模拟生产者，可以在任意位置投递
        /*
        $register->add($register::onWorkerStart,function ($ser,$id){
            if($id == 0){
                Timer::getInstance()->loop(3000,function (){
                    $job = new Job();
                    $job->setJobData(['time'=>\time()]);
                    MyQueue::getInstance()->producer()->push($job);
                });
            }
        });
        */
    }
    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        //跨域处理
        $response->withHeader('Access-Control-Allow-Origin', '*');
        $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $response->withHeader('Access-Control-Allow-Credentials', 'true');
        $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        if ($request->getMethod() === 'OPTIONS') {
            $response->withStatus(Status::CODE_OK);
            return false;
        }
        return true;
        //var_dump($request->getQueryParams());
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}