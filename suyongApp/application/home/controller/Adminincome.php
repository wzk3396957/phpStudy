<?php

namespace app\home\controller;

use app\home\controller\Base;
use think\Db;
use think\Session;

class Adminincome extends Base{

	private $connect = 'mysql://root:suyongkeji@suyongw.cn:3306/send#utf8';

	public function year(){
		$CONFIG = [
            'path' => [
                '首页',
                '年费收益'
            ]
        ]; 
        $this->assign('CONFIG', $CONFIG);
        $list = Db::connect($this->connect) ->table('admin_income')
                                            ->alias('a')
                                            ->join('user u','a.name = u.openid')
                                            ->where('pid',Session::get('id'))
                                            ->where('type','1')
                                            ->field('a.*,u.username')
                                            ->paginate(12);
        $this->assign('list', $list);
        return $this->fetch();
	}

	public function order(){
		$CONFIG = [
            'path' => [
                '首页',
                '订单收益'
            ]
        ]; 
        $this->assign('CONFIG', $CONFIG);
        $list = Db::connect($this->connect)->table('admin_income')->where('pid',Session::get('id'))->where('type','2')->paginate(12);
        $this->assign('list', $list);
        return $this->fetch();
	}
}