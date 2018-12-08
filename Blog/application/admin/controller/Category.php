<?php
namespace app\admin\controller;

use app\admin\model\Category as CategoryModel;
use app\admin\validate\Category as CategoryValidate;

class Category extends Common
{ 
	public function category(){
		$nowpage = input('get.page',1);
       
        $count = 10; 
        $totalpage = ceil((CategoryModel::count())/$count);
        
        $category = CategoryModel::limit(($nowpage-1)*$count,$count)->order('sort desc')->select();

        $this->assign("totalpage",$totalpage);
        $this->assign("category",$category);
        return $this->fetch();
	}

	public function category_edit($id){
		if(!$id || !is_numeric($id)){
            return 'id非法';
        }
        $data = CategoryModel::find($id);
        $this ->assign('data',$data);
        $model = new CategoryModel();
		$data = $model ->order('sort desc') ->select();
		$category = $this ->sort($data);
        $this ->assign('category',$category);
        return $this ->fetch();
	}

	public function category_add(){
		$model = new CategoryModel();
		$data = $model ->order('sort desc') ->select();
		$category = $this ->sort($data);
		$this ->assign('category',$category);
		return $this ->fetch();
	}

	public function do_category_add(){
		$data = input('post.');
		$validate = new CategoryValidate();
        if(!$validate->check($data)){
            return $validate->getError();
        }
		$model = new CategoryModel();
		$res = $model ->insert($data);
		return $res?1:'添加失败';
	}
	public function del_category(){
		$id = input('post.id');
        if(!$id || !is_numeric($id)){
            return 'id非法';
        }
        if(!CategoryModel::get($id)){
        	return '此ID没有数据';
        }
        $data = CategoryModel::select();
        $array = $this ->tree_del($data,$id);
        $ids = implode(",", $array);
        if($ids){
        	if(!CategoryModel::destroy($ids)){
        		return '子分类删除失败';
        	}
        }
        $res = CategoryModel::where('id',$id)->delete();
        return $res?1:'删除失败';
	}
	public function do_category_edit(){
		$id = input('id');
		$data =input('post.');
		$validate = new CategoryValidate();
        if(!$validate->check($data)){
            return $validate->getError();
        }
		$res = CategoryModel::where('id',$id) ->update($data);
		if($res){
			return '1';
		}else{
			return '修改失败';
		}
	}
}