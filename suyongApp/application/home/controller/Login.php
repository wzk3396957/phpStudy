<?php

namespace app\home\controller;
use think\Config;
use think\Controller;
use think\Request;
use think\Db;
use think\Log;
use think\Session;

class Login extends Controller
{
    private $connect = 'mysql://root:suyongkeji@suyongw.cn:3306/send#utf8';
    //登录界面 退出登录
    public function index()
    {
        Session::clear();
        return $this->fetch('index');
    }

    //登录动作
    public function logincheck()
    {
       $username = input('post.username');
       $password = input('post.password');
       //dump($post);die;
       $data = Db::connect($this->connect) -> table('admin') -> where('username',$username) -> where('password',$password)-> find();
       if($data){
            Session::set('id',$data['id']);
            Session::set('auth',$data['auth']);
            Session::set('area',$data['area']);
            Session::set('username',$data['username']);
            return '1';
       }else{
            return '0';
       }
    }
}