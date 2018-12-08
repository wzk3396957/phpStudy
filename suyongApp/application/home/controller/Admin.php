<?php

namespace app\home\controller;

use app\home\controller\Base;
use think\Db;
use think\Session;

class Admin extends Base{

	private $connect = 'mysql://root:suyongkeji@suyongw.cn:3306/send#utf8';

	public function showList(){
        $CONFIG = [
            'path' => [
                '首页',
                '合伙人列表'
            ]
        ]; 
        $this->assign('CONFIG', $CONFIG);
        $up_id = Session::get('id');
        $list = Db::connect($this->connect)->table('admin')->where('id','neq',1)->where('up',$up_id)->paginate(10);
        $this->assign('list',$list);
        return $this->fetch();
    }
     // 删除
    public function del(){
        $id = input('id');
        if (empty($id)) {
            return 'request lost!';
        }
        $res = Db::connect($this->connect)->table('admin')->where('id',$id)->delete();
        
        return $res? '1' : '0';
    }
    // 添加区域
    public function addArea(){
        $CONFIG = [
            'js' => [
                'js/area.js'
            ],
            'path' => [
                '首页',
                '添加区域'
            ]

        ];
        $id = input('get.id');
        $data = Db::connect($this->connect)->table('admin') -> where('id',$id) ->find();
        $this->assign('CONFIG', $CONFIG);
        $this->assign('data',$data);
        return $this->fetch();
    }
    //执行添加
    public function do_addArea(){
        $id = input('post.id');
        $area = input('post.area');
        $is_exist = Db::connect($this->connect)->table('admin')->where('area','like','%'.$area.'%')->value('area');
        if($is_exist){
            return '2';
        }
        $str =  Db::connect($this->connect)->table('admin') -> where('id',$id) ->value('area');
        $str = $str.','.$area;
        $res = Db::connect($this->connect)->table('admin') -> where('id',$id) ->update(['area'=>$str]);
        return $res?'1':'0';
    }

    public function setArea(){
        $CONFIG = [
            'js' => [
                'js/area.js'
            ],
            'path' => [
                '首页',
                '创建合伙人'
            ]

        ];
        $this->assign('CONFIG', $CONFIG);
        if(Session::get('auth')=='2'){
            $id = Session::get('id');
            $str = Db::connect($this->connect)->table('admin')->where('id',$id)->value('area');
            $area = explode(",",$str);
            $this->assign('area', $area);
        }
        return $this -> fetch();
    }

    //创建合伙人
    public function do_set(){
        $data = input('post.');

        $area = ['area'=>$data['area']];
        $arr = Db::connect($this->connect)->table('admin')->where('auth',3)->field('area')->select();
        $username = Db::connect($this->connect)->table('admin')->where('username',$data['username'])->find();
        
        if(in_array($area,$arr)){
            return '2';
        }

        $is_exist = Db::connect($this->connect)->table('admin')->where('area','like','%'.$data['area'].'%')->where('auth','neq','2')->value('area');
        if($is_exist){
            return '2';
        }

        if($username){
            return '3';
        }

        $data['up'] = Session::get('id');
        $up_auth = Session::get('auth');
        $data['auth'] = $up_auth + 1;
        $res = Db::connect($this->connect)->table('admin')->insert($data);
        return $res?'1':'0';
    }
}