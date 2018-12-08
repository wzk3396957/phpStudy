<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Comment as CommentModel;
use app\admin\model\Contact as ContactModel;

class Comment extends Controller
{
	public function feedback_list(){
		//下面是获取GET传来的页数，如果没有页数时，页数为1.
        $nowpage=input('get.page',1);
        //$totalpage就是计算你要获取的最大页数，ceil 是向前取整，这里是设置为10条数据为1页（注意括号）。
        $model = new ContactModel();
        $count = 5; //限制个数
        $totalpage = ceil(($model->count())/$count);//这里尽量简写了。
        //下面注意加上 这句 limit(($nowpage-1)*10,10)，就是数据控制每页显示数据的条数，获取页数后乘以设置的条数，获取该页的10条（自己设置）数据
        $list =$model->limit(($nowpage-1)*$count,$count)->select();
        //最后， 就是把数据和最大页数传到前端接受了完成了。（搜索条件的也要的话也要传。）
        $this->assign("totalpage",$totalpage);
		$this ->assign('list',$list);
		return $this ->fetch();
	}
}