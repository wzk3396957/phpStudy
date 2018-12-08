<?php
namespace app\admin\controller;

use app\admin\model\Article as ArticleModel;
use app\admin\validate\Article as ArticleValidate;
use app\admin\model\Category as CategoryModel;
use app\admin\controller\Upload;

class Article extends Common
{   
    public function article_list()
    {   
        //下面是获取GET传来的页数，如果没有页数时，页数为1.
        $nowpage=input('get.page',1);
        //$totalpage就是计算你要获取的最大页数，ceil 是向前取整，这里是设置为10条数据为1页（注意括号）。
        $model = new ArticleModel();
        $count = 5; //限制个数
        $totalpage=ceil(($model->count())/$count);//这里尽量简写了。
        //下面注意加上 这句 limit(($nowpage-1)*10,10)，就是数据控制每页显示数据的条数，获取页数后乘以设置的条数，获取该页的10条（自己设置）数据
        $res=$model->limit(($nowpage-1)*$count,$count)->order('id desc')->select();
        //最后，就是把数据和最大页数传到前端接受了完成了。（搜索条件的也要的话也要传。）
        $this->assign("totalpage",$totalpage);
        $this->assign("res",$res);
        return $this->fetch();
    }

    public function article_add()
    {
        $category = CategoryModel::select();
        $this ->assign('category',$category);
    	return $this->fetch();
    }

    public function do_article_add()
    {
        $file = $_FILES['art_image'];
        
        $upload = new Upload();
        $str = $upload->upload($file);
        if(!$str){
            return $this->error("上传异常","article_add",2);
        }
    	$data = [
            'title'=>input('articletitle'),
            'art_type'=>input('art_type'),
            'image'=>$str,
            'sort'=>input('sort'),
            'author'=>input('author'),
            'content'=>input('editorValue'),
            'time'=>time()
        ];
        $validate = new ArticleValidate();
        if(!$validate->check($data)){
            return $validate->getError();
        }
        $model = new ArticleModel();
        $res = $model->save($data);
        if($res){
            return $this->success("添加成功","article_list",2);
        }else{
            return $this->error("添加失败","article_add",2);
        }
    }

    public function showContent($id)
    {
        if(!$id || !is_numeric($id)){
            return 'id非法';
        }
        $model = new ArticleModel();
        return $model::where('id',$id)->value('content');
    }

    public function article_status(){
        $data = input('post.');
        $model = new ArticleModel();
        $re = $model::where('id',$data['id'])->update(['status'=>$data['status']]);
        if($re !== false){
            return $data['id'];
        }else{
            return;
        }
    }

    public function del_art(){
        $id = input('post.id');
        if(!$id || !is_numeric($id)){
            return 'id非法';
        }
        $model = new ArticleModel();
        $res = $model::where('id',$id)->delete();
        return $res?1:'删除失败';
    }

    public function article_edit(){
        $id = input('id');
        if(!$id || !is_numeric($id)){
            return;
        }
        $model = new ArticleModel();
        $data = $model ->where('id',$id) ->find();
        $this ->assign('data',$data);
        $category = CategoryModel::select();
        $this ->assign('category',$category);
        return $this ->fetch();
    }

    public function do_article_edit(){
        $id = input('id');
        $file = $_FILES['art_image'];

        if(!$file['name']){
            $str = ArticleModel::where('id',$id) ->value('image');
        }else{
            $upload = new Upload();
            $str = $upload->upload($file);
            if(!$str){
                return $this->error("上传异常","article_add",2);
            }
        }
        $data = [
            'title'=>input('articletitle'),
            'art_type'=>input('art_type'),
            'image'=>$str,
            'sort'=>input('sort'),
            'author'=>input('author'),
            'content'=>input('editorValue'),
            'time'=>time()
        ];
        $validate = new ArticleValidate();
        if(!$validate->check($data)){
            return $validate->getError();
        }
        $model = new ArticleModel();
        $res = $model ->where('id',$id) ->update($data);
        if($res){
            return $this->success("编辑成功","article_list",2);
        }else{
            return $this->error("编辑失败","article_add",2);
        }
    }
 }