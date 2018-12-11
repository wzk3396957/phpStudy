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
        $nowpage=input('get.page',1);
        $count = config('config.size'); //限制个数
        $totalpage = ceil(($model->where('status',1)->count())/$count);
        if($nowpage <= 0){
            $nowpage = 1;
        }
        if($nowpage > $totalpage){
            $nowpage = $totalpage;
        }
        $list = $model ->limit(($nowpage-1)*$count,$count) ->where('status',1) ->order('update_time desc') ->select();

        $this->assign("totalpage",$totalpage);
        
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
        $data = $model ->get_single_art($id);
        $this ->assign('data',$data);
        //上下篇
        $left_art = $model ->where('id','<',$id) ->limit(0,1) ->order('id desc') ->find();
        if(!$left_art){
            $left_art = 0;
        }
        $this ->assign('left_art',$left_art);
        $right_art = $model ->where('id','>',$id) ->limit(0,1) ->find();
        if(!$right_art){
            $right_art = 0;
        }
        $this ->assign('right_art',$right_art);
        //热门专题
        $hot_title = $model ->where('hot',1)->field('id,presentation') ->select();
        $this ->assign('hot_title',$hot_title);
        //分类
        $category = CategoryModel::field('id,category') ->select();
        $this ->assign('category',$category);
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
    //得到某个分类文章
    public function getArtByCate(){
        $id = input('get.id');
        if(!$id || !is_numeric($id)){
            $this->error('id格式错误','/index.php/index/Index/index');
        }
        $nowpage=input('get.page',1);
        $count = config('config.size'); //限制个数
        $totalpage = ceil((ArticleModel::where('status',1) ->where('art_type = 0 or art_type ='.$id) ->count())/$count);
        if($nowpage <= 0){
            $nowpage = 1;
        }
        if($nowpage > $totalpage){
            $nowpage = $totalpage;
        }
        $list = ArticleModel::limit(($nowpage-1)*$count,$count) ->where('status',1) ->where('art_type = 0 or art_type ='.$id) ->order('update_time desc') ->select();

        $this->assign("totalpage",$totalpage);
        $this ->assign('list',$list);
        $this ->assign('cate',$id);
        return $this ->fetch();
    }
}
