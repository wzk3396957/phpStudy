<?php

namespace app\home\controller;

use app\home\controller\Base;
use think\Db;
use think\Session;

class Km extends Base{

	private $connect = 'mysql://root:suyongkeji@suyongw.cn:3306/send#utf8';

	public function setKm(){
        $CONFIG = [
            'path' => [
                '首页',
                '收费标准'
            ]
        ]; 
        $this->assign('CONFIG', $CONFIG);
        $id = Session::get('id');
        $str = Db::connect($this->connect)->table('admin')->where('id',$id)->value('area');
        $area = explode(",",$str);
        $this->assign('area', $area);
        return $this->fetch();
    }

    public function do_set(){
        $data = input('post.');
        $area = Db::connect($this->connect)->table('how_much_km')->where('area',$data['area'])->find();
        if($area){
            return '2';
        }
        $re = Db::connect($this->connect)->table('how_much_km_edit')->insert($data);
        return $re?'1':'0';
    }

    public function showList(){
        $CONFIG = [
            'path' => [
                '首页',
                '收费标准列表'
            ]
        ]; 
        $this->assign('CONFIG', $CONFIG);
        $map = $this->auth();
        $list = Db::connect($this->connect)->table('how_much_km')->where($map)->select();
        $this->assign('list', $list);
        return $this->fetch();
    }

    public function checkKm(){
         $CONFIG = [
            'path' => [
                '首页',
                '审核收费标准'
            ]
        ]; 
        $this->assign('CONFIG', $CONFIG);
        $list = Db::connect($this->connect)->table('how_much_km_edit')->select();
        $this->assign('list', $list);
        return $this->fetch();
    }

   
    public function check(){
        $id = input("post.id");
        $state = input("post.state");
        if($state=='1'){
            $data = Db::connect($this->connect)->table('how_much_km_edit')->where('id',$id)->find();
            $is_update = Db::connect($this->connect)->table('how_much_km')->where('id',$id)->find();
            if($is_update){
                $re = Db::connect($this->connect)->table('how_much_km')->update($data);
            }else{
                $re = Db::connect($this->connect)->table('how_much_km')->insert($data);
            }
            Db::connect($this->connect)->table('how_much_km_edit')->where('id',$id)->delete();
            return $re?'1':'0';
        }else{
            Db::connect($this->connect)->table('how_much_km_edit')->where('id',$id)->delete();
            return '1';
        }
    }
    public function edit(){
        $CONFIG = [
            'path' => [
                '首页',
                '修改收费标准'
            ]
        ]; 
        $this->assign('CONFIG', $CONFIG);
        $id = input('get.id');
        $data = Db::connect($this->connect)->table('how_much_km')->where('id',$id)->find();
        $data['area'] = input('get.area');
        $this->assign('data', $data);
        return $this->fetch();
    }

    public function do_edit(){
        $data = input('post.');
        $res = Db::connect($this->connect)->table('how_much_km_edit')->insert($data);
        return $res?'1':'0';
    }
}