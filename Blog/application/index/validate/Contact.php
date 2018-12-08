<?php
namespace app\index\validate;

use think\Validate;

class Contact extends Validate
{
    protected $rule = [
        'name'  =>  'require|max:25',
        'email' =>  'require|email',
        'message'  =>  'require',
    ];

    protected $message  =   [
        'category.email' => '请填写正确的邮箱'
    ];

}