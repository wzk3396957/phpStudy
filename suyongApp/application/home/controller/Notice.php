<?php
namespace app\home\controller;

// use think\Controller;
use think\Db;

class Notice extends Base{

	private $connect = 'mysql://root:suyongkeji@suyongw.cn:3306/send#utf8';

	public function notice(){
		$CONFIG = [
                'path' => [
                    '首页',
                    '公告管理'
                ]
            ];
		$notice = Db::connect($this->connect)->table('notice')->find();
		$this->assign('CONFIG', $CONFIG);
		$this ->assign('notice',$notice);
		return view('notice');
	}
	// 编辑
    public function edit_do(){
        $data = input('post.');
        $notice = Db::connect($this->connect)->table('notice')->find();
        if($notice){
        	$data['id'] = $notice['id'];
        	$result = Db::connect($this->connect)->table('notice')->update($data);
        }else{
        	 $result = Db::connect($this->connect)->table('notice')->insert($data);
        }
        if($result!==false){
            return 'success';
        }else{
            return 'error';
        }
    }
}