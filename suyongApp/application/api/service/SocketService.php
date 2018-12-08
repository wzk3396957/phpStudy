<?php
namespace app\api\service;

use GatewayWorker\Lib\Gateway;
use think\Config;
use think\Request;
use think\Session;

class SocketService
{

    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     *
     * @param int $client_id 连接id
     */
    public static function onConnect($client_id)
    {
        // 向当前client_id发送数据
        Gateway::sendToClient($client_id, "@connect");


    }

    /**
     * 当客户端发来消息时触发
     * @param int $client_id 连接id
     * @param mixed $message 具体消息
     */
    public static function onMessage($client_id,$message)
    {
        $data = json_decode($message,true);
        $login_id = $data['login_id'];
        if($data['type'] == 'login'){
            $RES = Self::_requestPost('https://sd.suyongw.com/api/api/sendToClient',['id'=>$login_id,'client_id' => $client_id, 'client_ip' => Request::instance()->ip()]);
        }
        Gateway::sendToClient($client_id,'链接成功');
    }

    public static function sendMessage($client_id,$message)
    {
        if (empty($client_id)) {
            return false;
        }
        $time = 0;
        while (!Gateway::sendToClient($client_id, $message)) {
            $time++;
            if ($time > 3) {
                return false;
            }
            sleep(1);
        }
        return true;
    }

    /**
     * 当用户断开连接时触发
     * @param int $client_id 连接id
     */
    public static function onClose($client_id)
    {
        $RES = Self::_requestPost('https://sd.suyongw.com/api/api/sendToClose',['client_id' => $client_id]);
        Gateway::sendToClient($client_id,'断开连接');
    }

    public static function _requestPost($url, $data, $ssl = true)
    {
        // curl完成
        $curl = curl_init();

        //设置curl选项
        curl_setopt($curl, CURLOPT_URL, $url);//URL
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36';
        curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);//user_agent，请求代理信息
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);//referer头，请求来源
        //SSL相关
        if ($ssl) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//禁用后cURL将终止从服务端进行验证
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);//检查服务器SSL证书中是否存在一个公用名(common name)。
        }
        // 处理post相关选项
        curl_setopt($curl, CURLOPT_POST, true);// 是否为POST请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);// 处理请求数据
        // 处理响应结果
        curl_setopt($curl, CURLOPT_HEADER, false);//是否处理响应头
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);//curl_exec()是否返回响应结果

        // 发出请求
        $response = curl_exec($curl);
        if (false === $response) {
            return false;
        }
        return $response;
    }


}