<?php

namespace app\admin\model;

use think\Model;

class Article extends Model
{
    protected $autoWriteTimestamp = true;
	
	public function category()
    {
        return $this->hasMany('Category', 'id', 'art_type');
    }

    public function get_art_list($nowpage,$count){
    	$article = self::with('category') ->limit(($nowpage-1)*$count,$count) ->order('id desc') ->select();
        return $article;
    }

    public function get_single_art($id){
        $article = self::with('category') ->find($id);
        return $article;
    }
}
