<?php
namespace app\admin\validate;

use think\Validate;

class Article extends Validate
{
    protected $rule = [
        'title'  =>  'require|max:25',
        'art_type' =>  'require',
        'sort'  =>  'require|number|max:4',
        'author'  =>  'require|max:25',
        'content'  =>  'require',
    ];

    protected $message  =   [
        
    ];

}