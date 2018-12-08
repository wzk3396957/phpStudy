<?php
namespace app\index\controller;

use think\Controller;
use app\admin\model\Article as ArticleModel;
use app\index\model\Comment as CommentModel;
use app\index\model\Contact as ContactModel;
use app\index\validate\Contact as ContactValidate;

class Index extends Controller
{
    public function index()
    {
        $model = new ArticleModel();
        $list = $model ->where('status',1) ->select();
        $this ->assign('list',$list);
    	return $this->fetch();
    }

    public function about()
    {
    	return $this->fetch();
    }

    public function contact()
    {
    	return $this->fetch();
    }

    public function single()
    {   
        $id = input('get.id');
        if(!$id || !is_numeric($id)){
            $this->error('id格式错误','/index.php/index/Index/index');
        }
        $model = new ArticleModel();
        $data = $model ->where('id',$id) ->find();
        $this ->assign('data',$data);
        //评论部分
        $model = new CommentModel();
        $list = $model ->getComment($id);
        $this ->assign('list',$list);

    	return $this->fetch();
    }

    public function do_contact(){
        $data = input('post.');
        $validate = new ContactValidate();
        if(!$validate->check($data)){
            return $validate->getError();
        }
        $data['time'] = time();
        $res = ContactModel::insert($data);
        return $res?'1':'留言失败';
    }

    
}
