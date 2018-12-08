<?php
namespace app\admin\controller;

use think\Controller;

class Common extends Controller
{
    //当任何函数加载时候  会调用此函数
    public function _initialize(){//默认的方法  会自动执行 特征有点像构造方法
	     $uid = session('uid');
	     if(empty($uid)){
	        $url = url('Login/login');
            echo "<script>top.location.href='$url'</script>";exit;
	     }
 	}
 	//无限极分类
 	public function sort($data,$pid=0,$level=0){
        static $arr=array();
        foreach ($data as $k => $v) {
            if($v['pid']==$pid){
                $v['level']=$level;
                $arr[]=$v;
                $this->sort($data,$v['id'],$level+1);
            }
        }
        return $arr;
    }
    //无限极删除
    public function tree_del($data,$pid){
    	static $arr=array();
    	foreach ($data as $k => $v) {
            if($v['pid'] == $pid){
                $arr[]=$v['id'];
                $this->tree_del($data,$v['id']);
            }
        }
        return $arr;
    }
 }