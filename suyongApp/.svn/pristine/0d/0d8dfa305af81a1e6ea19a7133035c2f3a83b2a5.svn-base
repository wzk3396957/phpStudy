<?php
/**
 * Created by PhpStorm.
 * User: 丶晓
 * Date: 2017/11/30
 * Time: 16:46
 */


use \Workerman\Worker;
use \Workerman\WebServer;
use \GatewayWorker\Gateway;
use \GatewayWorker\BusinessWorker;
use \Workerman\Autoloader;


// 自动加载类
require_once __DIR__ . '/../vendor/autoload.php';
// $context = array(
//     // 更多ssl选项请参考手册 http://php.net/manual/zh/context.ssl.php
//     'ssl' => array(
//         // 请使用绝对路径
//         'local_cert' => 'C:/Tools/GeoTrust DV SSL/certificate.pem', // 也可以是crt文件
//         'local_pk' => 'C:/Tools/GeoTrust DV SSL/private.key',
//         'verify_peer' => false,
//         // 'allow_self_signed' => true, //如果是自签名证书需要开启此选项
//     )
// );
// gateway 进程，这里使用Text协议，可以用telnet测试
$gateway = new Gateway("websocket://172.17.18.5:2019");
// gateway名称，status方便查看
$gateway->name = 'SocketGateways';
// gateway进程数
$gateway->count = 6;
// 本机ip，分布式部署时使用内网ip
$gateway->lanIp = '172.17.18.5';
// 内部通讯起始端口，假如$gateway->count=4，起始端口为4000
// 则一般会使用4000 4001 4002 4003 4个端口作为内部通讯端口
$gateway->startPort = 4002;
// 服务注册地址
$gateway->registerAddress = '172.17.18.5:1236';
// 开启SSL，websocket+SSL 即wss
//$gateway->transport = 'ssl';

// 心跳间隔
//$gateway->pingInterval = 30
//$gateway->pingNotResponseLimit = 3;
//// 心跳数据
//$gateway->pingData = '@heart';;

// 当客户端连接上来时，设置连接的onWebSocketConnect，即在websocket握手时的回调
//$gateway->onConnect = function($connection)
//{
//    $connection->onWebSocketConnect = function($connection , $http_header)
//    {
//        // 可以在这里判断连接来源是否合法，不合法就关掉连接
//        // $_SERVER['HTTP_ORIGIN']标识来自哪个站点的页面发起的websocket链接
//            $connection->close();
//        // onWebSocketConnect 里面$_GET $_SERVER是可用的
//        // var_dump($_GET, $_SERVER);
//    };
//};

// 如果不是在根目录启动，则运行runAll方法
if (!defined('GLOBAL_START')) {
    Worker::runAll();
}

