<?php
namespace App\HttpController;

use EasySwoole\EasySwoole\Logger;
use App\Model\AdminsModel;
use App\Model\WechatModel;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\ORM\DbManager;
//use EasySwoole\RedisPool\Redis;
use EasySwoole\Template\Render;
use \Swoole\Coroutine as co;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\Jwt\Jwt;
class Index extends Controller
{

    public static $list = [];
    function index(){
        $model = new WechatModel();
        $table = $model->schemaInfo();
        $this->request()->getQueryParams();
        $model->username = 'test1';
        $model->password = 'test1';
        $res = $model->login();
        $lastResult = $model->lastQueryResult();
        $this->writeJson(200,$this->request()->getQueryParams(),'成功');
        $this->writeJson(200,$lastResult->getResult());
        /*
        //获取查询结果对象
        $lastResult = $model->lastQueryResult();
        //获取查询数据总数,查询时需要调用`withTotalCount`才可以使用该方法
        var_dump($lastResult->getTotalCount());
        //获得最后插入的id
        var_dump($lastResult->getLastInsertId());
        //获取执行影响的数据条数  update,delete等方法使用
        var_dump($lastResult->getAffectedRows());
        //获取错误code
        var_dump($lastResult->getLastErrorNo());
        //获取错误消息
        var_dump($lastResult->getLastError());
        //获取执行mysql返回的结果
        var_dump($lastResult->getResult());
        */

        $this->response()->withHeader('Content-type','text/html;charset=utf-8');
//        $file = EASYSWOOLE_ROOT.'/vendor/easyswoole/easyswoole/src/Resource/Http/welcome.html';
//        if(!is_file($file)){
//            $file = EASYSWOOLE_ROOT.'/src/Resource/Http/welcome.html';
//        }
//        go(function (){
//            \co::sleep(0.5);
//            echo "hello";
//        });
//        go(function (){
//            $csp = new \EasySwoole\Component\Csp();
//            for($i=1;$i<=10;$i++){
//                $list[]=$csp->add('t'.$i,function (){
//                    \co::sleep(0.2);
//                    return 't1 result';
//                });
//            }
//
//            $csp->add('t2',function (){
//                \co::sleep(0.1);
//                return 't2 result';
//            });
//            $list = $csp->exec();
//            var_dump($list);
//
//        });
        $this->response()->write('协程--'.Index::class);//file_get_contents($file)
    }
    //
    protected function actionNotFound(?string $action)
    {
        $this->response()->withStatus(404);
        $file = EASYSWOOLE_ROOT.'/vendor/easyswoole/easyswoole/src/Resource/Http/404.html';
        if(!is_file($file)){
            $file = EASYSWOOLE_ROOT.'/src/Resource/Http/404.html';
        }
        $this->response()->write(file_get_contents($file));
    }
    //协程异步请求等待
    public function waitGroup(){
        $this->response()->withHeader('Content-type','text/html;charset=utf-8');
        $this->response()->write('1');
        $parent_wait = new \EasySwoole\Component\WaitGroup(12); //协程等待
        $parent_wait->add();
        go(function ()use ($parent_wait){
            $queryBuild = new QueryBuilder();
            // 支持参数绑定 第二个参数非必传
            $queryBuild->raw("select * from td_admins where uid = 1", "");
            // $queryBuild->raw("select * from test where name = 'siam'");
            // 第二个参数 raw  指定true，表示执行原生sql
            // 第三个参数 connectionName 指定使用的连接名，默认 default
            $chanel = new co\Channel(12);
            $wait = new \EasySwoole\Component\WaitGroup(12); //协程等待
            for ($i=1;$i<=12;$i++){
                $wait->add(); //等待增量1
                $data = DbManager::getInstance()->query($queryBuild, true, 'default');
                $this->response()->write($i.PHP_EOL);
                //$this->response()->write(json_encode($data->getResult()));
                var_dump($data->getResult());
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
                self::$list[] = 1;
                var_dump($res);
            }
            echo '------------------'.PHP_EOL;
            $parent_wait->done();
        });
        $parent_wait->wait();
        var_dump( self::$list);
        $this->response()->write('协程1--'.Index::class);//file_get_contents($file)
    }
    //
    public static function getObj($cid){
            if(!empty(self::$list[$cid])){
                return self::$list[$cid];
            }
            self::$list[$cid] = new self();
            return self::$list[$cid];
    }
    //ORM事务操作
    public function trans(){
        self::$list = [];
        $WaitGroup = new \EasySwoole\Component\WaitGroup(1); //协程等待
        // 创建协程
        go(function ()use($WaitGroup){
            $WaitGroup->add();
            $chanel = new co\Channel(4);
            $wait = new \EasySwoole\Component\WaitGroup(4); //协程等待
            for ($i=1;$i<=4;$i++){
                $wait->add(); //等待增量1
                /**
                 * 子协程ORM事务操作
                 */
                go(function ()use ($chanel,$wait,$i){
                    $wechat = WechatModel::create()->get($i);
                    // 开启事务
                    $strat = DbManager::getInstance()->startTransaction();
                    // 更新操作
                    $res = $wechat->where('id='.$i)->update(['tel'=>13662829560]);
                    if(!$wechat->lastQueryResult()->getAffectedRows()){ $rollback = DbManager::getInstance()->rollback();
                        echo $i.'更新失败'.PHP_EOL;
                    }else{
                        echo $i.'**********更新成功**********'.PHP_EOL;
                        var_dump($wechat->lastQueryResult()->getAffectedRows());
                        echo $i.'**********更新成功**********'.PHP_EOL;
                    }

                    // 不管更新成功还是失败，直接回滚
                    if($i==4){ $rollback = DbManager::getInstance()->rollback(); }

                    // 返回false 因为连接已经回滚。事务关闭。
                    $commit = DbManager::getInstance()->commit();

                    // 通过commit判断事务操作是否成功
                    if($commit===true){
                        $res = ['code'=>200,'msg'=>'事务操作成功！','commit'=>true,'id'=>$i,'res'=>$res];
                    }else{
                        $res = ['code'=>404,'msg'=>'事务操作失败，已回滚！','commit'=>true,'id'=>$i,'res'=>$res];
                    }

                    $chanel->push($res);//数据写入协程通道

                    $wait->done(); //等待减少1
                });
            }
            $wait->wait(); //等待协程执行完毕
            while (true){
                if($chanel->isEmpty()){break;}$res = $chanel->pop();self::$list[] = $res;
            }
            $WaitGroup->done();
        });
        $WaitGroup->wait();
        $this->writeJson(200,self::$list,'子协程事务操作');
    }

    //模板渲染测试
    public function view(){
        DbManager::getInstance()->startTransaction();
        DbManager::getInstance()->invoke(function ($client){
            $WechatModel = WechatModel::invoke($client);
            $WechatModel->tel = '13662829560';
            echo '------START--------'.PHP_EOL;
            $WechatModel->where('tel="13662829560"')->field('id,username,tel')->select(); //执行查询操作
            $lastResult = $WechatModel->lastQueryResult();
            $WechatModel->where('id=1')->update(['tel'=>'1']);
            echo '------END--------'.PHP_EOL;
            self::$list[] = $lastResult->getResult();
            self::response()->write(Render::getInstance()->render('index/index',['row'=> time(),'server'=> self::request()->getServerParams(), 'json'=>$lastResult->getResult()]));
        });
        DbManager::getInstance()->rollback();
        DbManager::getInstance()->commit();

        //$this->response()->write(Render::getInstance()->render('index/index',['row'=> time(),'json'=>json_encode(self::$list)]));
    }

    // 日志记录
    public function log(){
        $this->response()->withHeader('Content-type','text/html;charset=utf-8');

        Logger::getInstance()->log('log level info',Logger::LOG_LEVEL_INFO,'DEBUG');//记录info级别日志//例子后面2个参数默认值
        Logger::getInstance()->log('log level notice',Logger::LOG_LEVEL_NOTICE,'DEBUG2');//记录notice级别日志//例子后面2个参数默认值
        Logger::getInstance()->console('console',Logger::LOG_LEVEL_INFO,'DEBUG');//记录info级别日志并输出到控制台
        Logger::getInstance()->info('log level info');//记录info级别日志并输出到控制台
        Logger::getInstance()->notice('log level notice');//记录notice级别日志并输出到控制台
        Logger::getInstance()->waring('log level waring');//记录waring级别日志并输出到控制台
        Logger::getInstance()->error('log level error');//记录error级别日志并输出到控制台
        Logger::getInstance()->onLog()->set('myHook',function ($msg,$logLevel,$category){
            $this->response()->write('callback</br>');
        });
        $this->response()->write('日志记录</br>');
    }

    //JTW令牌
    public function jwt(){
        $this->response()->withHeader('Content-type','text/html;charset=utf-8');
        $user_id = 2;
        $jwtObject = Jwt::getInstance()
            ->setSecretKey('easyswoole') // 秘钥
            ->publish();
        $jwtObject->setAlg('HMACSHA256'); // 加密方式
        $jwtObject->setAud('user1'); // 用户
        $jwtObject->setExp(time()+3600); // 过期时间
        $jwtObject->setIat(time()); // 发布时间
        $jwtObject->setIss('tudou'); // 发行人
        $jwtObject->setJti(md5(md5(time()))); // jwt id 用于标识该jwt
        $jwtObject->setNbf(time()+60*5); // 在此之前不可用
        $jwtObject->setSub('主题'); // 主题

// 自定义数据
        $jwtObject->setData([
            'uid'=>1,
            'username'=>'13662829560',
            'openid'=>md5(time())
        ]);

// 最终生成的token
        $token = $jwtObject->__toString();
        $this->response()->write('token长度'.strlen($token).'</br>');
        try {
            $jwtObject = Jwt::getInstance()->setSecretKey('easyswoole')->decode($token);
            $status = $jwtObject->getStatus();

            // 如果encode设置了秘钥,decode 的时候要指定
            // $status = $jwt->setSecretKey('easyswoole')->decode($token)
            switch ($status)
            {
                case  1:
                    $this->response()->write($jwtObject->getIss().'验证通过</br>');
                    $data['uid'] = $jwtObject->getIss();
                    $data['user'] = $jwtObject->getAud();
                    $data['openid'] = $jwtObject->getData();
                    echo '验证通过';
                    $this->response()->write($jwtObject->getAud().'-'.$jwtObject->getJti());
                    $jwtObject->getAlg();
                    $jwtObject->getAud();
                    $jwtObject->getData();
                    $jwtObject->getExp();
                    $jwtObject->getIat();
                    $jwtObject->getIss();
                    $jwtObject->getNbf();
                    $jwtObject->getJti();
                    $jwtObject->getSub();
                    $jwtObject->getSignature();
                    $jwtObject->getProperty('alg');
                    break;
                case  -1:
                    echo '无效';
                    break;
                case  -2:
                    echo 'token过期';
                    break;
            }
        } catch (\EasySwoole\Jwt\Exception $e) {
            $this->response()->write('解析异常</br>');
        }
    }
    //redis pool
    public function redis(){
        //redis连接池注册(config默认为127.0.0.1,端口6379)
        $user_id =1;
        $token = md5(time());
        go(function ()use ($user_id,$token) {

            //defer方式获取连接
            $redis = \EasySwoole\RedisPool\Redis::defer('redis');
            $redis->set('defer_'.$user_id, $token);
            var_dump($redis->get('defer_'.$user_id));

            //invoke方式获取连接
            \EasySwoole\RedisPool\Redis::invoke('redis',function (\EasySwoole\Redis\Redis $redis)use ($user_id,$token){
                var_dump($redis->set('invoke_'.$user_id, $token));
                //$redis->del(['invoke_'.$user_id,'defer_'.$user_id]);
            });

        });
    }
}