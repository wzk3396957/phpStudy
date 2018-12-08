<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function ini_time($time){
	$Y = date("Y",$time);
	$m = date("m",$time);
	$d = date("d",$time);
	switch ($m)
	{
	case 1:
	  $m = 'January';
	  break;
	case 2:
	  $m = 'February';
	  break;
	case 3:
	  $m = 'March';
	  break;
	case 4:
	  $m = 'April';
	  break;
	case 5:
	  $m = 'May';
	  break;
	case 6:
	  $m = 'June';
	  break;
	case 7:
	  $m = 'July';
	  break;
	case 8:
	  $m = 'August';
	  break;
	case 9:
	  $m = 'September';
	  break;
	case 10:
	  $m = 'October';
	  break;
	case 11:
	  $m = 'November';
	  break;
	default:
	  $m = 'December';
	}
	return $d.' '.$m.' '.$Y;	
}