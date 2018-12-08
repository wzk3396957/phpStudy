<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Admin as AdminModel;

class Login extends Controller
{   
    //登录页面
    public function login()
    {   
        session(null);
        return $this->fetch();
    }
    //登录
    public function do_login()
    {   
        $data = input('post.');
        $captcha = new \think\captcha\Captcha();
        if (!$captcha->check($data['code'])) {
            return '验证码不正确';
        }
        $model = new AdminModel();
        $admin = $model ->where(['username'=>$data['username'],'password'=>$data['password']])->find();
        if($admin){
            session('uid',$admin['id']);
            session('username',$admin['username']);
            return 1;
        }else{
            return '用户名或密码错误';
        }
    }
}