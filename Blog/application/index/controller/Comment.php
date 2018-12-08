<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\Comment as CommentModel;
use app\index\model\CommentSon as CommentSonModel;

class Comment extends Controller
{

    public function comment(){
    	$data = input('post.');
        $data['time'] = time();
    	$model = new CommentModel();
    	$model ->name = $data['name'];
        $model ->content = $data['content'];
        $model ->time = $data['time'];
        $res = $model ->save();
        $data['id'] = $model ->id;
    	return $res?json_encode($data,JSON_UNESCAPED_UNICODE):0;
    }

    public function comment_son(){
    	$data = input('post.');
    	$data['time'] = time();
    	$model = new CommentSonModel();
    	$res = $model ->insert($data);
    	return $res?json_encode($data,JSON_UNESCAPED_UNICODE):0;
    }

    public function like(){
        
    }
}