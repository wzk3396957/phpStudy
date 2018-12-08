<?php

namespace app\home\controller;

use app\home\controller\Base;
use think\Db;

class Knight extends Base{

	private $connect = 'mysql://root:suyongkeji@suyongw.cn:3306/send#utf8';

    public function showList(){
        $CONFIG = [
            'path' => [
                '首页',
                '骑士列表'
            ]
        ];
        $this->assign('CONFIG', $CONFIG);
        $map = $this->auth();
        $list = Db::connect($this->connect)->table('knight')->where($map)->where(['state'=>1])->paginate(8);
        $this->assign('list',$list);
        return $this -> fetch();
    }

	public function checkList(){
		$CONFIG = [
            'path' => [
                '首页',
                '骑士审核'
            ]
        ];
        $this->assign('CONFIG', $CONFIG);

        $map = $this->auth();
        $list = Db::connect($this->connect)->table('knight')->where($map)->where(['state'=>2])->paginate(8);
        $this->assign('list',$list);
		return $this -> fetch();
	}
	//审核
    public function check(){
        $id = input("post.id");
        $state = input("post.state");
        //dump($p);die;
        $re = Db::connect($this->connect)->table('knight')->where('id',$id)->update(['state'=>$state]);
        return $re?'1':'0';
    }
    // 删除
    public function del(){
        $id = input('id');
        if (empty($id)) {
            return 'request lost!';
        }
        $res = Db::connect($this->connect)->table('knight')->where('id',$id)->delete();
        Db::connect($this->connect)->table('u_and_k')->where('knight',$id)->delete();
        return $res? '1' : '0';
    }
    //财务中心
    public function money(){
        $CONFIG = [
            'path' => [
                '首页',
                '财务中心'
            ]
        ];
        $map = $this->auth();
        $knight = Db::connect($this->connect)->table('knight')->where($map)->field('id,username')->paginate(8);
        $list = [];
        //本月
        $start = strtotime(date('Y-m-01'));
        $end = strtotime(date('Y-m-01'))+(60*60*24*30);
        $month['start'] =array('between',[$start,$end]);
        foreach($knight as $key=>$value){
            $list[$key]['username'] = $value['username'];
            $list[$key]['m_income'] = Db::connect($this->connect)->table('order')->where('knight',$value['id'])->where($month)->sum('price')*0.75;
            $list[$key]['m_num'] = Db::connect($this->connect)->table('order')->where('knight',$value['id'])->where($month)->count();
            $list[$key]['d_income'] = Db::connect($this->connect)->table('order')->where('knight',$value['id'])->whereTime('start', 'today')->sum('price')*0.75;
            $list[$key]['d_price'] = Db::connect($this->connect)->table('order')->where('knight',$value['id'])->whereTime('start', 'today')->sum('price');
            $list[$key]['avg'] = Db::connect($this->connect)->table('order')->where('knight',$value['id'])->avg('price');
        } 
        $this->assign('CONFIG',$CONFIG);
        $this->assign('knight',$knight);
        $this->assign('list',$list);
        return $this->fetch();
    }
}