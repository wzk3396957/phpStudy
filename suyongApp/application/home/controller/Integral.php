<?php

namespace app\home\controller;

use app\home\controller\Base;
use think\Db;
use think\Session;

class Integral extends Base{

	private $connect = 'mysql://root:suyongkeji@suyongw.cn:3306/send#utf8';

	public function showList(){
		$CONFIG = [
            'path' => [
                '首页',
                '积分规则列表'
            ]
        ];
        $this->assign('CONFIG', $CONFIG);
        $list = Db::connect($this->connect)->table('integral')->select();
        $this->assign('list', $list);
		return $this->fetch();
	}

	public function setintegral(){
		$CONFIG = [
            'path' => [
                '首页',
                '设置积分规则'
            ]
        ];
        $this->assign('CONFIG', $CONFIG);
		return $this->fetch();
	}

	// 编辑页面
    public function edit(){
        $CONFIG = [
                'path' => [
                    '首页',
                    '设置积分规则'
                ]
            ];
        $id = input('get.id');
        $data = Db::connect($this->connect)->table('integral') -> where('id',$id) ->find();
      	$this->assign('CONFIG', $CONFIG);
       	$this->assign('data',$data);
        return view('edit');
    }

    // 编辑
    public function do_edit(){
        $data = input('post.');
        // dump($data);die;
        $result = Db::connect($this->connect)->table('integral')->update($data);
        if($result!==false){
            return '1';
        }else{
            return '0';
        }
    }

	public function del(){
		$id = input('id');
        if (empty($id)) {
            return 'request lost!';
        }
        $res = Db::connect($this->connect)->table('integral')->where('id',$id)->delete();
        return $res?'1' : '0';
	}

	public function do_set(){
        $data = input('post.');
        $re = Db::connect($this->connect)->table('integral')->insert($data);
        return $re?'1':'0';
    }
}