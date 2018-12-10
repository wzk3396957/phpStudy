<?php
namespace app\admin\controller;

class Upload extends Common
{
	public function upload($file){
		$name = $file['name'];
        if($file){
        	$upload = DS."upload".DS.strtotime(date('Y-m-d',time()));
            $dir = $_SERVER['DOCUMENT_ROOT'].$upload; 
            if(!is_dir($dir))
            {
                mkdir($dir);
            }
            $type = strtolower(substr($name,strrpos($name,'.')+1));//得到文件类型，并且都转化成小写
            $image = DS.time().mt_rand(1,100).'.'.$type;
            $str = $dir.$image;
            if (move_uploaded_file($file['tmp_name'],$str)){
            	return $upload.$image;
            }else{
            	return false;
            }
        }else{
            return $this->error("添加失败,图片未上传","/index.php/admin/Articel/article_add",2);
        }
	}
}