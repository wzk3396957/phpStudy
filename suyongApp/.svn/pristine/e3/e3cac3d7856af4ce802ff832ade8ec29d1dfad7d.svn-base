<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think\worker;

use GatewayWorker\Gateway;
use Workerman\Worker;

/**
 * Worker控制器扩展类
 */
abstract class Server
{
    protected $worker;
    protected $socket = '';
    protected $protocol = 'http';
    protected $host = '0.0.0.0';
    protected $port = '2346';
    protected $processes = 1;

    /**
     * 架构函数
     * @access public
     */
    public function __construct($ssl = [])
    {
        if (empty($ssl)) {
            // 实例化 Websocket 服务
            $this->worker = new Worker($this->socket ?: $this->protocol . '://' . $this->host . ':' . $this->port);
        } else {
            $context = [
                'ssl' => [
                    'local_cert' => $ssl['cert'],
                    'local_pk' => $ssl['key']
                ]
            ];
            $this->worker->transport = 'ssl';
            // 实例化 Websocket 服务
            $this->worker = new Worker($this->socket ?: $this->protocol . '://' . $this->host . ':' . $this->port, $context);
        }
        // 设置进程数
        $this->worker->count = $this->processes;
        // 初始化
        $this->init();

        // 设置回调
        foreach (['onWorkerStart', 'onConnect', 'onMessage', 'onClose', 'onError', 'onBufferFull', 'onBufferDrain', 'onWorkerStop', 'onWorkerReload'] as $event) {
            if (method_exists($this, $event)) {
                $this->worker->$event = [$this, $event];
            }
        }
        $this->worker->addRoot('localhost', __DIR__ . '/web');
        // Run worker
        Worker::runAll();
    }

    protected function init()
    {

    }

}
