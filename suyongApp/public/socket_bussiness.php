<?php
/**
 * Created by PhpStorm.
 * User: 丶晓
 * Date: 2017/11/21
 * Time: 8:13
 */

use \Workerman\Worker;
use \Workerman\WebServer;
use \GatewayWorker\Gateway;
use \GatewayWorker\BusinessWorker;
use \Workerman\Autoloader;

// 自动加载类
define('IS_CLI', true);
define('DS', '/');
define('LOG_PATH', dirname(realpath($_SERVER['SCRIPT_FILENAME'])) . '/extend/log/');
define('RUNTIME_PATH', dirname(realpath($_SERVER['SCRIPT_FILENAME'])) . '/runtime/');

require_once __DIR__ . '/../vendor/autoload.php';

// bussinessWorker 进程
$worker = new BusinessWorker();
// worker名称
$worker->name = 'SocketWorkers';
// bussinessWorker进程数量
$worker->count = 6;
// 服务注册地址
$worker->registerAddress = '172.17.18.5:1236';


// 加载框架引导文件
require_once __DIR__ . '/../application/api/service/SocketService.php';

$worker->eventHandler = '\app\api\service\SocketService';

// 如果不是在根目录启动，则运行runAll方法
if (!defined('GLOBAL_START')) {
    Worker::runAll();
}