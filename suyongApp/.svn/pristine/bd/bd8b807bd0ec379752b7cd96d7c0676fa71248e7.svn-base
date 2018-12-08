<?php

namespace app\home\controller;
use app\home\controller\Base;
use think\Controller;
use think\Request;
use think\Db;

class Index extends Base
{
    private $connect = 'mysql://root:root@localhost:3306/app#utf8';

    public function index()
    {
    	$CONFIG = [
            'css' => [
                'css/plugins/cropper/cropper.min.css',
                'css/plugins/webuploader/webuploader.css',
                'js/plugins/webuploader/webuploader_imgs.css',
            ],
            'js' => [
                'js/plugins/cropper/cropper.min.js',
                'js/plugins/webuploader/webuploader.js',
                'js/plugins/clockpicker/clockpicker.js',
            ],
            'path' => [
                '首页',
                '例子'
            ]

        ];
        $this->assign('CONFIG', $CONFIG);
        return $this->fetch('index');
    }
  
    //多图
    public function get_images(){
    	        
       $data[0]['src'] = 'https://res.suyongw.com/' . 'FsF51uo6axWJ1RdSrb5TIqhGV-0C';
       $data[0]['key'] = 'FsF51uo6axWJ1RdSrb5TIqhGV-0C';
       $data[1]['src'] = 'https://res.suyongw.com/' . 'FrK-gygfoEvEyB5nnvoj4dE8RFPR';
       $data[1]['key'] = 'FrK-gygfoEvEyB5nnvoj4dE8RFPR';
       return $data;
    }
  
    //单图
    public function get_bcakground_image(){
    	return false;
    }

}