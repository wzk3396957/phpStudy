<?php
namespace app\index\controller;

use think\Controller;
use app\admin\model\Article as ArticleModel;
use app\index\model\Comment as CommentModel;
use app\index\model\Contact as ContactModel;
use app\admin\model\Category as CategoryModel;
use app\index\validate\Contact as ContactValidate;

class Index extends Controller
{
    public function index()
    {
        $model = new ArticleModel();

        $list = $model ->where('status',1) ->select();
        $this ->assign('list',$list);

        $hot_art = $model ->where('hot',1) ->select();
        $this ->assign('hot_art',$hot_art);

        $hot_title = $model ->where('hot',1)->field('id,presentation') ->select();
        $this ->assign('hot_title',$hot_title);
        //分类
        $category = CategoryModel::field('id,category') ->select();
        $this ->assign('category',$category);
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
        //文章推荐
        $hot_art = $model ->where('hot',1) ->select();
        $this ->assign('hot',$hot_art);
        //评论部分
        $model = new CommentModel();
        $list = $model ->getComment($id);
        $this ->assign('list',$list);
        //分类
        $category = CategoryModel::field('id,category') ->select();
        $this ->assign('category',$category);

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
