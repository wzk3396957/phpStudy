<?php
/**
 * Created by PhpStorm.
 * User: 丶晓
 * Date: 2017/11/30
 * Time: 16:45
 */

use \Workerman\Worker;
use \GatewayWorker\Register;

// 自动加载类

require_once __DIR__ . '/../vendor/autoload.php';

// register 必须是text协议
$register = new Register('text://172.17.18.5:1236');

// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}

