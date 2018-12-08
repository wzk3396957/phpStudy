<?php

namespace app\home\controller;

use app\home\controller\Base;
use think\Db;

class Opinion extends Base{

	private $connect = 'mysql://root:suyongkeji@suyongw.cn:3306/send#utf8';

    public function showList(){
        $CONFIG = [
            'path' => [
                '首页',
                '意见列表'
            ]
        ];
        $this->assign('CONFIG', $CONFIG);
        $list = Db::connect($this->connect) ->table('opinion')
                                            ->alias('t1')
                                            ->join('knight t2','t1.k_id=t2.id')
                                            ->field('t1.*,t2.username')
                                            ->paginate(10);
        $this->assign('list',$list);
        return $this -> fetch();
    }
}