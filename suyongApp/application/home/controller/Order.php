<?php

namespace app\home\controller;

use app\home\controller\Base;
use think\Db;
use think\request;
use think\Session;

class Order extends Base{

	private $connect = 'mysql://root:suyongkeji@suyongw.cn:3306/send#utf8';

    public function showList(){
        $CONFIG = [
            'path' => [
                '首页',
                '订单列表'
            ]
        ];
        $this->assign('CONFIG', $CONFIG);
        $map = $this->auth();
        $area = Db::connect($this->connect)->table('order') ->group('area') ->field('area')->where($map)->select();
        $this->assign('area',$area);
        
        $data =  input('get.data');
        
        $map_p = $this->auth_p();
        if($data){
            Session::set('areas',$data);
            $list = Db::connect($this->connect) ->table('order')
                                            ->alias('o')
                                            ->join('user u','o.openid = u.openid')
                                            ->field('o.*,u.username')
                                            ->where($map_p)
                                            ->where('o.area',$data)
                                            ->paginate(12,false,['query' => request()->param()]);
            $this->assign('list', $list);
            return $this->fetch();
        }
        Session::delete('areas');
        $list = Db::connect($this->connect) ->table('order')
                                            ->alias('o')
                                            ->join('user u','o.openid = u.openid')
                                            ->where($map_p)
                                            ->field('o.*,u.username')
                                            ->paginate(12);
        $this->assign('list', $list);
        return $this->fetch();
    }
    //权限
    public function auth_p(){
        if(Session::get('auth')=='2'){
        $map['o.area'] = array('in',Session::get('area'));
        }else if(Session::get('auth')=='3'){
            $map['o.area'] = array('eq',Session::get('area'));
        }else{
            $map = null;
        }
        return $map;
    }
}