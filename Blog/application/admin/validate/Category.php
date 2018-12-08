<?php
namespace app\admin\validate;

use think\Validate;

class Category extends Validate
{
    protected $rule = [
        'pid'  =>  'require|number|max:4',
        'category' =>  'require|unique:category',
        'sort'  =>  'require|number|max:4',
    ];

    protected $message  =   [
        'category.unique' => '分类名已存在'
    ];

}