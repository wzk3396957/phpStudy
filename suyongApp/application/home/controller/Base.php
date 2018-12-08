<?php
namespace app\home\controller;
header("Content-type: text/html; charset=utf-8");
use think\Config;
use think\Controller;
use think\Cookie;
use think\Request;
use think\Session;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Qiniu\Storage\BucketManager;

class Base extends Controller
{
    // 用于签名的公钥和私钥
    private $accessKey;
    private $secretKey;
    private $bucket;

    public function __construct(Request $request = null)
    {
        $id = Session::get('id');
        //判断用户是否登录
        if(empty($id)){
            //没有登录，跳转到登录页面
            //$this -> error('请先登录...',U('Public/login'),3);exit;
            //编写javascript代码
            $url = url('Login/index');
            echo "<script>top.location.href='$url'</script>";exit;  
        }
        
        $this->accessKey = Config::get('qiniu.appid');
        $this->secretKey = Config::get('qiniu.appsecret');
        $this->bucket = Config::get('qiniu.bucket');
        parent::__construct($request);
    }

    //获取七牛云token
    public function qiniu_get_token()
    {
        $accessKey = $this->accessKey;
        $secretKey = $this->secretKey;
        $bucket = $this->bucket;
        $auth = new Auth($accessKey, $secretKey);
        $token = $auth->uploadToken($bucket);
        Cookie::set('token', $token);
        return $auth->uploadToken($bucket);
    }

    //上传文件  key上传到七牛后保存的文件名  filePath 要上传文件的本地路径
    public function upload_img($key, $filePath)
    {
        $token = $this->qiniu_get_token();
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        if ($err !== null) {
            return $err;
        } else {
            return $ret;
        }
    }

    //删除文件
    public function del_qiniu_img($key)
    {
        $auth = new Auth($this->accessKey, $this->secretKey);
        $delMgr = new BucketManager($auth);
        $err = $delMgr->delete($this->bucket, $key);
        if($err !== null) {
            return false;
        }
        return true;
    }
    // 登出
    public function loginout()
    {
        Session::clear();
        $url = url('index');
            echo "<script>top.location.href='$url'</script>";exit;
    }
    //权限
    public function auth(){
        if(Session::get('auth')=='2'){
        $map['area'] = array('in',Session::get('area'));
        }else if(Session::get('auth')=='3'){
            $map['area'] = array('eq',Session::get('area'));
        }else{
            $map = null;
        }
        return $map;
    }
}