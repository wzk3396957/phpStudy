<?php

namespace app\home\controller;

use app\home\controller\Base;
use think\Db;

class User extends Base{

	private $connect = 'mysql://root:suyongkeji@suyongw.cn:3306/send#utf8';

    public function showList(){
        $CONFIG = [
            'path' => [
                '首页',
                '用户列表'
            ]
        ];
        $this->assign('CONFIG', $CONFIG);
        $list = Db::connect($this->connect)->table('user')->paginate(10);
        $this->assign('list',$list);
        return $this -> fetch();
    }
}