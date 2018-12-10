<?php
namespace app\admin\validate;

use think\Validate;

class Article extends Validate
{
    protected $rule = [
        'title'  =>  'require|max:15',
        'art_type' =>  'require',
        'sort'  =>  'require|number|max:4',
        'author'  =>  'require|max:25',
        'content'  =>  'require',
        'presentation' => 'require|max:30'
    ];

    protected $message  =   [
        'title.max' => '标题过长',
        'presentation.max' => '描述过长',
    ];

}