<?php
namespace app\index\model;

use think\Model;

class Comment extends Model{
	public function commentSon(){
		return $this->hasMany('CommentSon','pid','id');
	}

	public function getComment($id){
		$comment = self::with(['commentSon'=>function ($query){$query->order('id', 'desc');}])
						->where('art_id',$id)
						->order('id desc')
						->select();
		return $comment;
	}
}