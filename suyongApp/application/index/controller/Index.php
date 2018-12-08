<?php
namespace app\index\Controller;
use think\Controller;
use think\Session;
use think\Config;
use app\api\controller\Api;
use think\Db;
use app\api\controller\WeixinPay;
use app\api\controller\PayService;
use app\api\service\SocketService;
use app\api\service\WXBizDataCrypt;
use app\api\controller\WeixinRefund;

class Index extends Controller
{
    private $connect = 'mysql://root:suyongkeji@suyongw.cn:3306/send#utf8';
    private $connect2 = 'mysql://root:suyongkeji@www.suyongw.com:3306/restaurant#utf8';
    private $APPID = 'wx99a8cdf12be465c4';
    private $APPSECRET = 'c300eed90f38fe8b2f6e3fd3c52370bb';
    private $MCH_ID = 1488710252;    //商户号
    private $KEY = '4FDFAA5085309EDF50A52B051B92FAEA'; //支付秘钥
    private $string = 'WK1BCMX647TDJH39SNA2YFVRGPZE85RQU';// 商家邀请码解码字符串
    //前置操作
    protected $beforeActionList = [
        'order_due'
        // 'order_due' => ['only' => 'index']
    ];
    protected function order_due(){
        $due_time = time()-(60*60*2);//两个钟
        $order_due = Db::connect($this->connect)->table('order')->where('state','1')->where('start','<',$due_time)->where('pay','1')->select();
        foreach ($order_due as $value) {
            $this ->balance_refund($value['order_num'],$value['openid']);
        }
        $commission_due_time = time()-(60*60*24*7);//二十分钟
        $order_commission_due = Db::connect($this->connect)->table('order')->where('state','4')->where('start','<',$commission_due_time)->where('pay','1')->select();
        //dump($order_commission_due);
        foreach($order_commission_due as $value){
           return $this ->order_commission($value['order_num']);
        }
    }

    //余额退款
    private function balance_refund($order_num,$openid){
        $price = Db::connect($this->connect)->table('order')->where('order_num',$order_num) ->value('price');
        $re1 = Db::connect($this->connect)->table('order')->where('order_num',$order_num)->update(['state'=>'5','pay'=>'0']);
        $re2 = Db::connect($this->connect)->table('user')->where('openid',$openid)->setInc('money',$price);
        if($re1 !== false && $re2 !== false){
            return json_encode(true);
        }else{
            return json_encode(false);
        }
    }
    //登陆
    
    public function login($code){
       $appid = $this->APPID;
       $appsecret = $this->APPSECRET;
       // $code = $_POST['code'];
       $url="https://api.weixin.qq.com/sns/jscode2session?appid=".$appid."&secret=".$appsecret."&js_code=".$code."&grant_type=authorization_code";
       $arr = $this->curl_get($url);  
       $res = json_decode($arr,true);
       return json_encode($res);
    }

      //curl
    public function curl_get($url){
       $info=curl_init();
       curl_setopt($info,CURLOPT_RETURNTRANSFER,true);
       curl_setopt($info,CURLOPT_HEADER,0);
       curl_setopt($info,CURLOPT_NOBODY,0);
       curl_setopt($info,CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($info,CURLOPT_SSL_VERIFYHOST, false);
       curl_setopt($info,CURLOPT_URL,$url);
       $output= curl_exec($info);
       curl_close($info);
       return $output;
    }

    public function index(){
        
    }
    //手机验证码
    public function send_valiCode($phone)
    {
        $code = rand(10000, 99999);
        $sms_code = json_encode(['code' => $code]);
        $url = 'https://api.mysubmail.com/message/xsend.json';
        $data = "appid=14520&to=" . $phone . "&project=f7Br63&signature=fd766e9534c72a7856ab962bf8fc9059&vars=" . $sms_code;
        $res = $this->_requestPost($url, $data);
        $result = json_decode($res, true);
        if ($result['status'] == 'success') {
            return '{"code":'.$code.',"phone":'.$phone.'}';
        }
        return false;
    }
    public static function _requestPost($url, $data, $ssl = true)
    {
        // curl完成
        $curl = curl_init();

        //设置curl选项
        curl_setopt($curl, CURLOPT_URL, $url);//URL
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36';
        curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);//user_agent，请求代理信息
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);//referer头，请求来源
        //SSL相关
        if ($ssl) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//禁用后cURL将终止从服务端进行验证
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);//检查服务器SSL证书中是否存在一个公用名(common name)。
        }
        // 处理post相关选项
        curl_setopt($curl, CURLOPT_POST, true);// 是否为POST请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);// 处理请求数据
        // 处理响应结果 
        curl_setopt($curl, CURLOPT_HEADER, false);//是否处理响应头
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);//curl_exec()是否返回响应结果

        // 发出请求
        $response = curl_exec($curl);
        if (false === $response) {
            return false;
        }
        return $response;
    }
    //注册
    public function register(){
        if(empty($_POST['openid']) || empty($_POST['username']) || empty($_POST['head'])){
           return '2';//少数据
         }
         $openid = $_POST['openid'];
         $re = Db::connect($this->connect)->table('user')->where('openid',$openid)->find();
         $data = input('post.');
         //dump($data);die;
         if($re){
              $res = Db::connect($this->connect)->table('user')->where('openid',$data['openid'])->update($data);
         }else{
              $res = Db::connect($this->connect)->table('user')-> insert($data);
         }
         if($res!==false){
          return '1';
         }else{
          return '0';
         }
    }
    //填写邀请码
    public function wirte_code($openid,$up){
        
        $id = $this->changeInvite($up,$this->string);
        $res = Db::connect($this->connect)->table('admin')->where('id',$id)->find();
        if($res){
          $up = $id;
        }else{
          return '2';
        }
        
        $res = Db::connect($this->connect)->table('user')->where('openid',$openid)-> update(['up'=>$up]);
        if($res !== false){
            return '1';
        }else{
            return '0';
        }
    }

    //查询是否存在上级
    public function check_up($openid){
        $up = Db::connect($this->connect)->table('user')->where('openid',$openid)->value('up');
        return $up?'1':'0';
    }

    //填写手机号
    public function wirte_phone($openid,$phone){
        if(!$phone){
            return '0';
        }
        $res = Db::connect($this->connect)->table('user')->where('tel',$phone)->find();
        if($res){
            return '2';
        }
        $res = Db::connect($this->connect)->table('user')->where('openid',$openid)-> update(['tel'=>$phone]);
        if($res !== false){
            return '1';
        }else{
            return '0';
        }
    }

    //查询是否填了手机号
    public function check_phone($openid){
        $phone = Db::connect($this->connect)->table('user')->where('openid',$openid)->value('tel');
        return $phone?'1':'0';
    }

    // 根据邀请码转回id
    private function changeInvite($invite,$string){
        $string = array_flip(str_split($string));
        $invite = strrev(ltrim(ltrim($invite,'0'),$string['W']));
        $id = 0;
        for($i=0;$i<strlen($invite);$i++){
            $id = bcadd(bcmul($string[$invite{$i}] , bcpow(33, $i)) , $id);
        }
        return $id;
    }


    /*
       * 上传文件
       * @param file  小程序前端请求获取
       */
    public function upload(){
        $api = new Api();
        $file=request()->file('file');
        $data = $api->Upload_Img($file);
        return $data;
    }

    //首页的数据
    public function index_info($openid){
        $re = Db::connect($this->connect)->table('user')->where('openid',$openid)->field('username,head,money,integral')->find();
        return json_encode($re,JSON_UNESCAPED_UNICODE);
    }
    //注册骑士
    public function register_knight(){
        $area = explode(',',input('post.area'));
        if(sizeof($area)!=3){
            return '4';
        }
        if($area[0]==$area[1]){
          $area[1]='市辖区';
        }
        $area_str=implode("/",$area);
        if(empty(input('post.up'))){
          $up = 1;
        }else{
          $id = $this->changeInvite(input('post.up'),$this->string);
          $res = Db::connect($this->connect)->table('admin')->where('id',$id)->find();
          if($res){
            $up = $id;
          }else{
            return '3';
          }
        }
        $data = [
            'username'=>input('post.username'),
            'password'=>input('post.password'),
            'tel'=>input('post.tel'),
            'type'=>input('post.type'),
            'area'=>$area_str,
            'id_card_num'=>input('post.id_card_num'),
            'put_id_card'=>input('post.put_id_card'),
            'id_card'=>input('post.id_card'),
            'car'=>input('post.car'),
            'up'=>$up
        ];
        $knight = Db::connect($this->connect)->table('knight')->where('tel',$data['tel'])->find();
        if($knight){
          if($knight['state'] == '0'){
              $data['state'] = '2';
              $res = Db::connect($this->connect)->table('knight')->where('id',$knight['id'])->update($data);
              return $res?'1':'0';
          }else{
              return '2';//手机号已注册
          }
        }
        $res = Db::connect($this->connect)->table('knight')->insert($data);
        return $res?'1':'0';
    }
    //发起订单
    public function strat_order(){
        $data = input('post.');
        // if(in_array('undefined',$data)){
        //       return '3';
        // }
        if($data['ps'] == 'undefined'){
            $data['ps'] = '暂无备注';
        }
        $data['order_num'] = $this->rand_num();
        $data['start'] = time();
        $data['area'] =  Db::connect($this->connect)->table('user')->where('openid',$data['openid'])->value('area');
        if(!$data['area']){
            return '2';
        } 
        $res = Db::connect($this->connect)->table('order')->insert($data);
        return $res?$data['order_num']:'0';
    }
    //支付订单
    public function pay_order($openid,$order_num){
        $price = Db::connect($this->connect)->table('order')->where('order_num',$order_num)->value('price');
        $this ->in_wx_pay($openid,$order_num,$price);
        $body = '支付订单';   //商品描述
        $total_fee = $price*100;  //单位分(整数)
        $wx_pay = new WeixinPay($this->APPID,$openid,$this->MCH_ID,$this->KEY,$order_num,$body,$total_fee);
        return json_encode($wx_pay->pay());
    }
    //微信订单数据库
    private function in_wx_pay($openid,$order_num,$money){
        $data = [
                    'openid'=>$openid,
                    'order_num'=>$order_num,
                    'money'=>$money,
                    'time'=>time()
                  ];
          $re = Db::connect($this->connect)->table('wx_pay')->insert($data);
          return $re?1:0;
    }
    //订单支付成功
    public function pay_order_success($order_num){
        $res = Db::connect($this->connect)->table('order')->where('order_num',$order_num)->update(['pay'=>1]);
        return $res?'1':'0';
    }
    // 随机订单号
    public function rand_num(){
      $num = mt_rand(1,100);
      $time = time();
      $rand_num = $num.$time.$num;
      return $rand_num;
    }
    //订单管理
    public function order_info($openid,$state='0'){
        if(!$state){
          $map = null;
        }else if($state == '2'){
          $map='o.state=2 OR o.state=3';
        }else if($state == '6'){
          $map['state'] = '1';
          $map['pay'] = '0';
        }else{
          $map['o.state']=array('eq',$state);
        }
        $re = Db::connect($this->connect) ->table('order')
                                            ->alias('o')
                                            ->join('user u','o.openid = u.openid')
                                            ->field('o.*,u.username,u.head')
                                            ->where('o.openid',$openid)
                                            ->where($map)
                                            ->order('id desc')
                                            ->select();
        foreach ($re as $k=>$v) {
            if($re){
                $re[$k]['knight_tel'] = Db::connect($this->connect) ->table('knight') ->where('id',$v['knight']) ->value('tel');
            }else{
                $re[$k]['knight_tel'] = '';
            }
        }
        return json_encode($re,JSON_UNESCAPED_UNICODE);
    }



    //商务合作
    public function consociation(){
        $area = explode(',',input('post.area'));
        if($area[0]==$area[1]){
          $area[1]='市辖区';
        }
        $area_str=implode("/",$area);
        $data = [
            'agency'=>input('post.agency'),
            'tel'=>input('post.tel'),
            'username'=>input('post.username'),
            
            'area'=>$area_str
        ];
        $res = Db::connect($this->connect)->table('consociation')->insert($data);
         return $res?'1':'0';
    }
    //评价
    public function comment($order_num,$comment){
        $res = Db::connect($this->connect)->table('order')->where('order_num',$order_num)->update(['comment'=>$comment]);
        return $res?'1':'0';
    }
    //提现
    public function pull_money($openid,$money){
        $balance = Db::connect($this->connect)->table('user')->where('openid',$openid)->value('money');
       if($money>$balance){
            return 2;
       }
       $rand_num = $this->rand_num();
       $re = $this->in_money($openid,$money,$rand_num,2);
       if($re=='1'){
          $n =  $balance-$money;
          $res = Db::connect($this->connect)->table('user')->where('openid',$openid)->update(['money'=>$n]);
          if($res){
            return $this->withdraw($openid,$money,$rand_num);
          }
       }else{
          return 0;
       }
    }
    /*
     * 提现
     */
      public function withdraw($openid,$totalFee,$outTradeNo){
           $payservice =new PayService($this->MCH_ID,$this->APPID,$this->APPSECRET,$this->KEY);
           // $openid = 'o_hZK5F7Dr7LDDSX47AYRxNieRHw';
           // $totalFee =100;
           // $outTradeNo ="354kosdsdadj";
           $re = $payservice->createJsBizPackage($openid,$totalFee,$outTradeNo);
          // if($re == true){
          //     $data=[
          //         're' => '提现成功',
          //         'code' => 1,
          //         'msg' => 'success'
          //     ];
          // }else{
          //     $data=[
          //         're' => '提现失败',
          //         'code' => 0,
          //         'msg' => $re
          //     ];
          // }
          if($re == 'turn'){
            return 'success';
          }else{
            return $re;
          }
      }
    //充值
    public function pay_balance($openid,$money){
        $out_trade_no = $this->rand_num(); //订单号
        $body = '支付订单';   //商品描述
        $re = $this->in_money($openid,$money,$out_trade_no,1);
        if($re=='1'){
            $total_fee = $money*100;  //单位分(整数)
            $wx_pay = new WeixinPay($this->APPID,$openid,$this->MCH_ID,$this->KEY,$out_trade_no,$body,$total_fee);
            return json_encode($wx_pay->pay());
        }else{
            return '0';
        }
    }
    //充值成功
    public function pay_balance_success($openid,$money){
        $integral = Db::connect($this->connect)->table('integral')->order('money desc')->select();
        Db::startTrans();
        try {
            Db::connect($this->connect)->table('money')->where('openid',$openid)->update(['state'=>1]);//订单状态为1
            Db::connect($this->connect)->table('user')->where('openid',$openid)->setInc('money',$money);
            foreach ($integral as $value) {
                if($value['money'] <= $money){
                    Db::connect($this->connect)->table('user')->where('openid',$openid)->setInc('integral',$value['integral']);
                    break;
                }
            }
            Db::commit();
            return '1';
        } catch (Exception $ex) {
            Db::rollback();
            return '0';
        }
    }
    //交易订单
    private function in_money($openid,$money,$order_num,$type){
        $res=Db::connect($this->connect)->table('money')->insert(['openid'=>$openid,'money'=>$money,'time'=>time(),'order_num'=>$order_num,'type'=>$type]);
        return $res?'1':'0';
    }
    
    // 查询月份
    private function month($time,$k){
        if($time=='1'){//本月
            $start = strtotime(date('Y-m-01'));
            $end = strtotime(date('Y-m-01'))+(60*60*24*24);
            $month[$k] =array('between',[$start,$end]);
        }else if($time=='2'){//上个月
            $start = strtotime(date('Y-m-01'))-(60*60*24*24);
            $end = strtotime(date('Y-m-01'))-1;
            $month[$k] =array('between',[$start,$end]);
        }else{
            $month=null;
        } 
        return $month;
    }
    //提现明细
    public function get_balance($time,$openid){
        $month = $this->month($time,'time');
        $re = Db::connect($this->connect)->table('money')->where('openid',$openid)->where($month)->where('state',1)->field('time,money,type')->select();
        return json_encode($re,JSON_UNESCAPED_UNICODE);
    }

    //收益明细
    public function get_income($time,$openid){
         $month = $this->month($time,'start');
         $re = Db::connect($this->connect)->table('order')->where('knight',$openid)->where($month)->where('pay','2')->field('price')->select();
            $re[$k]['price']=$v['price']*0.75;
        return json_encode($re,JSON_UNESCAPED_UNICODE);
    }
    
    
    //百度定位
    public function location($longitude,$latitude){
         $key = '35DLaWTFLzP2qZviNwe2lzdG5KGjDSaV';
         $url = "http://api.map.baidu.com/geocoder/v2/?ak=$key&location=$latitude,$longitude&output=json";
         $res = $this->curl_get($url);
         $result = json_decode($res, true);
         //dump($result);die;
         return $result;
    }
    //拼接区域
    private function joint_area($result){
       $arr=[];
       $arr['province'] = $result['result']['addressComponent']['province'];
       $arr['city']= $result['result']['addressComponent']['city'];
       $arr['district'] = $result['result']['addressComponent']['district'];
       if($arr['province']==$arr['city']){
            $arr['city']='市辖区';
       }
       return $this->get_area($arr);//拼接区域
    }
    //定位
    public function address($longitude,$latitude,$openid){
       $result = $this->location($longitude,$latitude);
       $area = $this->joint_area($result);//拼接区域
       $area_jing=$result['result']['formatted_address'];//准确区域
       $re = Db::connect($this->connect)->table('user')->where('openid',$openid)->update(['area'=>$area,'area_jing'=>$area_jing]);
       return $re?'1':'0';
    }
    public function get_area($arr){
        $arr=array_filter($arr);
        $str=implode('/',$arr);
        return $str;
    }
    //定位结束
  
    //检查年费支付
    public function check_pay($openid,$order_num){
        $year = Db::connect($this->connect)->table('user')->where('openid',$openid)->value('year');
        if(!$year){
            return '0';//未支付
        }
        //检查年费过期
        if($year<time()){
            return '2';//过期
        }else{
            $state = Db::connect($this->connect)->table('order')->where('order_num',$order_num)->value('state');
            if($state != '1' && $state != '5'){
                return '4';//订单进行中
            }
            return $this->send_userk($openid,$order_num);//发送单给旗下的空闲骑士
        }
    }
       /*
     * 支付年费
     */
    public function year_pay(){
        $openid = input('openid');
        
        $out_trade_no = $this->rand_num(); //订单号
        $body = '支付订单';   //商品描述
        $total_fee = 0.01;//年费价格
        $total_fee = $total_fee*100;  //单位分(整数)
        $wx_pay = new WeixinPay($this->APPID,$openid,$this->MCH_ID,$this->KEY,$out_trade_no,$body,$total_fee);
        return json_encode($wx_pay->pay());
    }
    /*
      *支付年费成功
      */
    public function pay_year_success($openid){
        $time=time()+(60*60*24*365);
        $res = Db::connect($this->connect)->table('user')->where('openid',$openid)->update(['year'=>$time]);
        if($res){
          return $this->commission($openid,3980);
        }
    }

    //年费返回佣金
    public function commission($openid,$money){
        Db::startTrans();
        try {
            $id=Db::connect($this->connect)->table('user')->where('openid',$openid)->value('up');
            if($id && $id != '1'){
                $up_get = $money*0.4;
                Db::connect($this->connect)->table('admin')->where('id',$id)->setInc('money',$up_get);
                $data = ['name'=>$openid,'money'=>$up_get,'pid'=>$id,'time'=>time(),'type'=>'1'];//收益详情入表
                Db::connect($this->connect)->table('admin_income')->insert($data);
                $upid = Db::connect($this->connect)->table('admin')->where('id',$id)->value('up');
                if($upid && $upid!='1'|| $upid!='0'){
                    $upup_get = $money*0.2;
                    Db::connect($this->connect)->table('admin')->where('id',$id)->setInc('money',$upup_get);
                    $data = ['name'=>$openid,'money'=>$upup_get,'pid'=>$upid,'time'=>time(),'type'=>'1'];//收益详情入表
                    Db::connect($this->connect)->table('admin_income')->insert($data);
                }
            }
            Db::commit();
            return '1';
        } catch (Exception $ex) {
            Db::rollback();
            return '0';
        }
    }

    function get_between($input, $start, $end) { $substr = substr($input, strlen($start)+strpos($input, $start),(strlen($input) - strpos($input, $end))*(-1)); return $substr; }

    
    /**
     * @param string $address 地址
     * @param string $city  城市名
     * @return array
     */
    public function getLatLng($address)
    {
        if(!$address){
            return '2';
        }
        $ak = '3PWBZ-XKBKQ-YYE5R-G7M2X-2BLDZ-JSBD4';//你腾讯地图的k值
        $url = "http://apis.map.qq.com/ws/geocoder/v1/?address=".$address."&key=".$ak;
        $json = file_get_contents($url);
        $data = json_decode($json,TRUE);
        if (!empty($data) && $data['status'] == 0) {
          $result['lat'] = $data['result']['location']['lat'];
          $result['lng'] = $data['result']['location']['lng'];
          //dump($result);die;
          return $result;//返回经纬度结果
        }else{
          return null;
        }
    }
    function rad($d)
    {
        return $d * 3.1415926535898 / 180.0;
    }
 
    /**
     * 计算距离
     * @param $lat1 纬度 如：22.628547
     * @param $lng1 经度 如：114.036845
     * @param $lat2 纬度 如：22.628567
     * @param $lng2 经度 如：114.046055
     * @return float
     */
    public function GetDistance($lat1, $lng1, $lat2, $lng2)
    {
        $EARTH_RADIUS = 6378.137;
        $radLat1 = $this->rad($lat1);
        $radLat2 = $this->rad($lat2);
        $a = $radLat1 - $radLat2;
        $b = $this->rad($lng1) - $this->rad($lng2);
        $s = 2 * asin(sqrt(pow(sin($a/2),2) +
            cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));
        $s = $s *$EARTH_RADIUS;
        $s = round($s * 10000) / 10000;
        return round($s, 2); ;
    }
        
    
    public function get_price($openid,$area,$to_address){
        // $area='广东省广州市花都区龙珠路1-30';
        // $to_address='广东省广州市海珠区阅江西路222号';
        $order_area = Db::connect($this->connect)->table('user')->where('openid',$openid)->value('area');
        
        //dump($data);die;
        $result1= $this->getLatLng($area);
        $lat1 = $result1['lat'];
        $lng1 = $result1['lng'];
        $result2= $this->getLatLng($to_address);
        if(!$result1 || !$result2){
            return '2';
        }
        $lat2 = $result2['lat'];
        $lng2 = $result2['lng'];
        $km= $this->GetDistance($lat1, $lng1, $lat2, $lng2);
        $re = $this->compute_price($order_area,$km);
        if($re == '0'){
            return '0';//该区域没有设置价格
        }
        return json_encode($re,JSON_UNESCAPED_UNICODE);
    }
    //计算价格
    protected function compute_price($order_area,$km){
        $data = Db::connect($this->connect)->table('how_much_km')->where('area','like','%'.$order_area.'%')->find();
        if(!$data){
            return '0';//该区域没有设置价格
        }
        $re = array();
        if($km>$data['over_km']){
            $km = floor($km-$data['over_km']);
            $re['price'] = $km*$data['how_much_km']+$data['strat'];
        }else{
            $re['price']=$data['strat'];
        } 
        $re['km'] = $km;
        return $re;
    }
    //商家绑定骑士码
    public function knight_code($id){
        $code = $this->setInvite($id,$this->string);
        return json_encode($code,JSON_UNESCAPED_UNICODE);
    }

    // 根据id生成唯一邀请码
    private function setInvite($id,$string){
        $code = '';
        while ($id) {
            $mod = $id %33;
            $id = (int)$id /33;
            $code = $string[$mod].$code;
        }
        if(strlen($code) < 6){
            $code = str_pad($code,6,'0',STR_PAD_LEFT);
        }

        return $code;
    }

    //商家绑定骑士
    public function pull_knight($openid,$code){
        $id =$this->changeInvite($code,$this->string);
        $res = Db::connect($this->connect)->table('knight')->where('id',$id)->where('state',1)->find();
        if(!$res){
            return '2';//邀请码错误
        }
        $is_pull = Db::connect($this->connect)->table('u_and_k')->where('user',$openid)->where('state','neq','0')->where('knight',$id)->find();
        if($is_pull){
              return '3';//已经邀请过了
        }
        $re = Db::connect($this->connect)->table('u_and_k')->insert(['user'=>$openid,'knight'=>$id]);
        return $re?'1':'0';
    }
    //我的骑士
    public function my_knight($openid){
        $re = Db::connect($this->connect) ->table('u_and_k')
                                            ->alias('t1')
                                            ->join('knight t2','t1.knight=t2.id')
                                            ->field('t2.username,t2.tel,t2.status')
                                            ->where('t1.user',$openid)
                                            ->where('t1.state','1')
                                            ->select();
        return json_encode($re,JSON_UNESCAPED_UNICODE);
    }

    //取消订单
    public function cancel_order($order_num){
        $data = Db::connect($this->connect)->table('order')->where('order_num',$order_num)->field('price,openid,state')->find();
        if($data['state'] == '2' || $data['state'] == '4' || $data['state'] == '5'){
            return '2';
        }
        return $this ->order_refund($order_num,$data['price'],$data['openid']);
    }
    //查询是否被接收
    public function is_order($order_num){
        $is_order = Db::connect($this->connect)->table('order')->where('order_num',$order_num)->where('knight','neq','')->where('state',2)->find();
        if($is_order){
            return '1';
        }
        return '0';
    }

    //查询订单支付状态
    public function order_pay_status($order_num){
        if(!$order_num){
            return;
        }
        $order_pay = Db::connect($this->connect)->table('order')->where('order_num',$order_num)->value('pay');
        return $order_pay;
    }
    //获取骑士实时位置
    public function platform_send($order_num){
        $order = Db::connect($this->connect)->table('order')->where('order_num',$order_num)->where('pay','1')->find();
        if(!$order){
            return '5';//订单号错误或者还没给钱
        }
        if($order['state'] != '1' && $order['state'] != '5'){
            return '6';//订单正在进行中
        }

        $knight = Db::connect($this->connect) ->table('knight')->where('status','0')->where('socket_id','neq','')->where('current_area',$order['area'])->where('is_bind','0')->where('type',$order['order_type'])->field('socket_id')->select();

        if(empty($knight)){
              return json(true);//未找到骑士
        }

        $obj =new SocketService();
        $order_num_json = '{"order_num":'.$order_num.',"type":"2"}';
        foreach($knight as $value){
            $obj->sendMessage($value['socket_id'],$order_num_json);
        }
        sleep(2);
        $order['type'] = '1'; 
        return $this->screen_knight($order_num,$order);
    }
    //随机选一个骑士
    public function screen_knight($order_num,$order){
        //详细地址和缩略地址
        $current_area = $this->getLatLng($order['from_area']);//订单经纬度
        $knight = Db::connect($this->connect) ->table('knight_log')->where('order_num',$order_num)->select();
        if(empty($knight)){
              return;//未找到骑士
        }
        $arr = [];
        // if($value['local'] =='undefined,undefined'){
        //     continue;
        // }
        foreach($knight as $value){
              $result = explode(',',$value['local']);//骑士经纬度
              $km = $this->GetDistance($result[1], $result[0],$current_area['lat'], $current_area['lng']);//距离
              if($km<='2'){
                  $arr[] = ['knight'=>$value['knight'],'knight_km'=>$km];
              }
        }
        
        
        //发送给骑士的订单信息
        
        
        $obj =new SocketService();
        foreach($arr as $value){
            $client_id = Db::connect($this->connect) ->table('knight')->where('id',$value['knight'])->value('socket_id');
            $order['knight_km'] = $value['knight_km'];
            $order = json_encode($order,JSON_UNESCAPED_UNICODE);
            $send = $obj->sendMessage($client_id,$order);
        }
        return json(true);
    }
    //插入骑士日志
    public function insert_knight_log($knight,$order_num,$longitude,$latitude){
        $local = $longitude.','.$latitude;
        $kngiht = Db::connect($this->connect)->table('knight_log')->where('order_num',$order_num)->find();
        if($kngiht){
            Db::connect($this->connect)->table('knight_log')->where('order_num',$order_num)->delete();
        }
        $re = Db::connect($this->connect)->table('knight_log')->insert(['knight'=>$knight,'order_num'=>$order_num,'local'=>$local]);
        
        if($re !== false){
            return '1';
        }else{
            return '0';
        }
    }
    //获取公告
    public function get_notice(){
        $notice = Db::connect($this->connect)->table('notice')->value('notice');
        return $notice;
    }
    //---------------------------------------骑士端---------------------------------------------------
    // 财务中心
    public function finance($id){
        //本月
        $start = strtotime(date('Y-m-01'));
        $end = strtotime(date('Y-m-01'))+(60*60*24*30);
        $month['start'] =array('between',[$start,$end]);

        $re['m_income'] = Db::connect($this->connect)->table('order')->where('knight',$id)->where($month)->sum('price')*0.75;
        $re['m_num'] = Db::connect($this->connect)->table('order')->where('knight',$id)->where($month)->count();
        $re['d_income'] = Db::connect($this->connect)->table('order')->where('knight',$id)->whereTime('start', 'today')->sum('price')*0.75;
        $re['d_price'] = Db::connect($this->connect)->table('order')->where('knight',$id)->whereTime('start', 'today')->sum('price')*0.75;
        $re['avg'] = Db::connect($this->connect)->table('order')->where('knight',$id)->avg('price')*0.75;

        return json_encode($re,JSON_UNESCAPED_UNICODE);
    }
    //提现界面
    public function pull_ui($id){
        $re['money'] = Db::connect($this->connect)->table('knight')->where('id',$id)->value('money');
        $re['pulled'] = Db::connect($this->connect)->table('money')->where('openid',$id)->sum('money');
        $re['pull'] = Db::connect($this->connect)->table('order')->where('knight',$id)->where('state',4)->where('start','elt',time()-(60*60*24*30))->sum('price');
        $re['pull'] = $re['pull']-$re['pulled'];
        if($re['pull']<0){
            $re['pull'] = 0;
        }
        return json_encode($re,JSON_UNESCAPED_UNICODE);
    }
    //骑士提现
    public function k_pull_money($id,$money){
        $balance = Db::connect($this->connect)->table('knight')->where('id',$id)->value('money');
       if($money>$balance){
            return 2;
       }
       $rand_num = $this->rand_num();
       $re = $this->in_money($id,$money,$rand_num,2);
       if($re=='1'){
          $n =  $balance-$money;
          $res = Db::connect($this->connect)->table('knight')->where('id',$id)->update(['money'=>$n]);
          if($res){
              return $this->withdraw($id,$money,$rand_num);
          }
       }else{
          return 0;
       }
    }
    
    /*
    *余额支付
    *@param  $openid
    *@param  $order_num  订单号
     */
    public function balance_pay($openid,$order_num,$pay_type='money'){
        if($pay_type != 'money' && $pay_type!= 'integral'){
              return '3';
        }
        $price = Db::connect($this->connect)->table('order')->where('order_num',$order_num)->value('price');
        $balance = Db::connect($this->connect)->table('user')->where('openid',$openid)->value($pay_type);
        if($price>$balance){
          return '2';
        }
        $re1 = Db::connect($this->connect)->table('user')->where('openid',$openid)->update([$pay_type=>$balance-$price]);
        $re2 = Db::connect($this->connect)->table('order')->where('order_num',$order_num)->update(['pay'=>1]);
        if($re1 !== false && $re2 !== false){
            return '1';
        }else{
            return '0';
        }
    }
    
    //骑士的订单
    public function my_order($order_num){
        $re = Db::connect($this->connect) ->table('order')
                                          ->alias('t1')
                                          ->join('user t2','t1.openid=t2.openid')
                                          ->field('t1.*,t2.username,t2.head')
                                          ->where('t1.order_num',$order_num)
                                          ->find();
        return json_encode($re,JSON_UNESCAPED_UNICODE);
    }
    //发送socket给店家骑士
     public function send_userk($openid,$order_num){
          $knight=Db::connect($this->connect)->table('knight')
                                                ->alias('t1')
                                                ->join('u_and_k t2','t1.id=t2.knight')
                                                ->where('t2.user',$openid)
                                                ->where('t1.state','1')
                                                ->where('t1.socket_id','neq','')
                                                ->where('t2.state','1')
                                                ->field('t1.socket_id,t1.id')
                                                ->select();
          //$client_id = Db::connect($this->connect)->table('knight')->where('id',5)->value('socket_id');     
          if(empty($knight)){
              return json(true);
          }
          //发送给骑士的订单信息
          $arr = Db::connect($this->connect)->table('order')->where('order_num',$order_num)->find();
          $arr['type'] = '1';
          $arr = json_encode($arr,JSON_UNESCAPED_UNICODE);

          $obj =new SocketService();
          foreach($knight as $value){
              $bool = $obj->sendMessage($value['socket_id'],$arr);
          }
          return json(true);
     }
     //离线骑士查询订单
     public function off_line_order($id){
          $is_bind = Db::connect($this->connect)->table('knight')->where('id',$id)->value('is_bind');
          if($is_bind == '1'){
             return $this ->knight_off_line($id);
          }else{
             return $this ->platform_off_line($id);
          }
     }
    //骑士离线订单 
    protected function knight_off_line($id){
          $openid = Db::connect($this->connect)->table('user')
                                                ->alias('t1')
                                                ->join('u_and_k t2','t1.openid=t2.user')
                                                ->where('t2.knight',$id)
                                                ->where('t2.state','1')
                                                ->field('t1.openid')
                                                ->select();
          $arr = [];
          foreach($openid as $v){
              $arr[] = $v['openid'];
          }
          $orders = Db::connect($this->connect)->table('order')->where('openid','in',$arr)->where('state','1')->select();
          return json_encode($orders,JSON_UNESCAPED_UNICODE);
    }
    //平台离线订单
    public function platform_off_line($id){
           $knight = Db::connect($this->connect)->table('knight')->where('id',$id)->field('current_area,current_location,type')->find();
           if(in_array("", $knight)){
                return '0';
           }
           $arr = explode(',',$knight['current_location']);
           
           //找出区域订单
           $area_orders =  Db::connect($this->connect)->table('order')->where('area',$knight['current_area'])->where('state','1')->where('pay','1')->where('order_type',$knight['type'])->select();
           $orders = array();
           foreach ($area_orders as $value) {
                $result1= $this->getLatLng($value['from_area']);
                $lat2 = $result1['lat'];
                $lng2 = $result1['lng'];
                $km= $this->GetDistance($arr[1],$arr[0],$lat2,$lng2);//距离
                if($km<2){
                    $value['knight_km'] = $km;
                    $orders[] = $value;//距离1.5公里以内的单子
                }
           }
           return json_encode($orders,JSON_UNESCAPED_UNICODE);
    }
     /*
     *骑士登录
     *@param string $tel 电话
     *@param string $password 密码
     *return str 1 成功
      */
      public function knight_login($tel,$password){
          $knight = Db::connect($this->connect)->table('knight')->where('tel',$tel)->where('password',$password)->find();
          if($knight['state'] == '2'){
              return '{"checking":"checking"}'; //您的账号正在接受审核
          }
          if($knight['state'] == '0'){
              return '{"noPass":"noPass"}';//您的账号未通过审核，是否重新注册
          }
          if($knight){
              return $knight['id'];
          }else{
              return '0';
          }
      }          
      

      /*
     *骑士接单
     *@param int $id 骑士id
     *@param string $order_num 订单号
     *return str 1 成功
      */
      public function take_orders($id,$order_num,$knight_km=0){
          $order = Db::connect($this->connect)->table('order')->where('order_num',$order_num)->field('state,order_id')->find();
          if($order['state']=='5'){
              return '2';//订单取消了
          }

          if($order['state']=='4'){
              return '3';//订单完成了
          }

          if($order['state']=='2'){
              return '4';//订单被接了
          }
          if($order['order_id']){
              Db::connect($this->connect2)->table('wm_order')->where('id',$order['order_id'])->update(['statu'=>3]);
          }
          $re = Db::connect($this->connect)->table('order')->where('order_num',$order_num)->update(['state'=>3,'knight'=>$id,'knight_km'=>$knight_km]);
          if($re!==false){
            return $order_num;
          }else{
            return '0';
          }
      }
      //骑士点取件
      public function knight_pick_up($order_num){
          $res = Db::connect($this->connect)->table('order')->where('order_num',$order_num)->update(['state'=>2]);
          if($res !== false){
              return '1';
          }else{
              return '0';
          }
      }
      //当前订单
      public function current_order($order_num){
          $re = Db::connect($this->connect)->table('order')->where('order_num',$order_num)->field('order_num,to_name,to_tel,to_address,g,km,start')->find();
          return json_encode($re,JSON_UNESCAPED_UNICODE);
      }
      //完成订单
      public function finish_orders($id,$order_num){
          $order = Db::connect($this->connect)->table('order')->where('order_num',$order_num)->field('state,order_id')->find();
          if($order['state'] != '2'){
              return '2';//订单状态异常
          }
          if($order['order_id']){
              Db::connect($this->connect2)->table('wm_order')->where('id',$order['order_id'])->update(['statu'=>4]);
          }
          // $re1 = Db::connect($this->connect)->table('knight')->where('id',$id)->update(['status'=>0]);
          $re = Db::connect($this->connect)->table('order')->where('order_num',$order_num)->update(['state'=>4,'over'=>time()]);
          if($re!==false){
            return '1';
          }else{
            return '0';
          }
      }
      //订单完成返回佣金
      public function order_commission($order_num){
          $pay = Db::connect($this->connect)->table('order')->where('order_num',$order_num)->where('state',4)->value('pay');
          if($pay == '0'){
              return '1';
          }
          if($pay == '2'){
              return '3';
          }
          if($pay =='1'){
              $knight = Db::connect($this->connect)->table('order')->where('order_num',$order_num)->value('knight');//骑士
              $money = Db::connect($this->connect)->table('order')->where('order_num',$order_num)->value('price');//订单价钱
              $id = Db::connect($this->connect)->table('knight')->where('id',$knight)->value('up');
              $upid = Db::connect($this->connect)->table('admin')->where('id',$id)->value('up');
              Db::startTrans();
              try {
                  Db::connect($this->connect)->table('order')->where('order_num',$order_num)->where('state',4)->update(['pay'=>'2']);
                  Db::connect($this->connect)->table('knight')->where('id',$knight)->setInc('money',$money*0.75);
                  if($id && $id != '1'){
                      $up_get = $money*0.06;
                      Db::connect($this->connect)->table('admin')->where('id',$id)->setInc('money',$up_get);
                      $data = ['name'=>$order_num,'money'=>$up_get,'pid'=>$id,'time'=>time(),'type'=>'2'];//收益详情入表
                      Db::connect($this->connect)->table('admin_income')->insert($data);
                      $upid = Db::connect($this->connect)->table('admin')->where('id',$id)->value('up');
                      if($upid && $upid!='1'|| $upid!='0'){
                          $upup_get = $money*0.04;
                          Db::connect($this->connect)->table('admin')->where('id',$id)->setInc('money',$upup_get);
                          $data = ['name'=>$order_num,'money'=>$upup_get,'pid'=>$upid,'time'=>time(),'type'=>'2'];//收益详情入表
                          Db::connect($this->connect)->table('admin_income')->insert($data);
                      }
                  }
                  Db::commit();
                  return '1';
              } catch (Exception $ex) {
                  Db::rollback();
                  return '0';
              }
          }
      }
      
      //骑士定位
      public function knight_address($id,$longitude,$latitude){
            $current_location = $longitude.','.$latitude;
            $result = $this->location($longitude,$latitude);
            $current_area = $this->joint_area($result);//拼接区域
            $re = Db::connect($this->connect)->table('knight')->where('id',$id)->update(['current_area'=>$current_area,'current_location'=>$current_location]);
            if($re !== false){
                return '1';
            }else{
                return '0';
            }
      }
      //意见反馈
      public function insert_opinion(){
          $data = input('post.');
          $res = Db::connect($this->connect)->table('opinion')->insert($data);
          return $res?'1':'0';
      }
      //骑士订单详情页
      //订单管理
    public function knight_order_info($id,$state='0'){
        if(!$id){
            return;
        }
        if($state == '0'){
          $map = null;
        }else{
          $map['o.state']=array('eq',$state);
        }
        $re = Db::connect($this->connect) ->table('order')
                                            ->alias('o')
                                            ->join('user u','o.openid = u.openid')
                                            ->field('o.*,u.username,u.head')
                                            ->where('o.knight',$id)
                                            ->where($map)
                                            ->order('id desc')
                                            ->select();
        foreach($re as $key=>$value){
            $re[$key]['price'] = $re[$key]['price']*0.75;
        }
        return json_encode($re,JSON_UNESCAPED_UNICODE);
    }
    //首页的数据
    public function knight_index_info($id){
        $re = Db::connect($this->connect)->table('knight')->where('id',$id)->field('username,money,tel,integral,base_integral,integral,grade')->find();
        return json_encode($re,JSON_UNESCAPED_UNICODE);
    }
    //骑士审核绑定页面
    public function knight_check_user($id){
        $re = Db::connect($this->connect)->table('u_and_k')
                                         ->alias('t1')
                                         ->join('user t2','t1.user = t2.openid')
                                         ->field('t1.id as id_kn,t2.username,t2.head')
                                         ->where('knight',$id)
                                         ->where('state','2')
                                         ->select();
        return json_encode($re,JSON_UNESCAPED_UNICODE);
    }
    //骑士审核绑定
    public function do_knight_check_user($id_kn,$state){
        $id = Db::connect($this->connect)->table('u_and_k')->where('id',$id_kn)->value('knight');
        $is_bind = Db::connect($this->connect)->table('knight')->where('id',$id)->value('is_bind');
        if($is_bind == '1'){
            $re1 = Db::connect($this->connect)->table('u_and_k')->where('id',$id_kn)->update(['state'=>$state]);
            $re2 = true;
        }else{
            $re1 = Db::connect($this->connect)->table('u_and_k')->where('id',$id_kn)->update(['state'=>$state]);
            $re2 = Db::connect($this->connect)->table('knight')->where('id',$id)->update(['is_bind'=>$state]);
        }
        if($re1 !== false && $re2 !== false){
            return '1';
        }else{
            return '0';
        }
    }
    //-----------------------------------外部小程序---------------------------------------------
    public function tel_bind($miniapps_id,$tel){
        $is_bind = Db::connect($this->connect)->table('user')->where('miniapps_id',$miniapps_id)->find();
        if($is_bind){
            return '2';//已绑定
        }
        $is_tel = Db::connect($this->connect)->table('user')->where('tel',$tel)->find();
        if(!$is_tel){
            return '3';//不存在手机号
        }
        $res = Db::connect($this->connect)->table('user')->where('tel',$tel)->update(['miniapps_id'=>$miniapps_id]);
        if($res !== false){
            return '1';
        }else{
            return '0';
        }
    }
    //解密手机号
    public function get_valiCode($sessionKey,$encryptedData,$iv){
        $appid = $this->APPID;  
        // $sessionKey = 'FWI9l1DpbaoinakTouWSNg==';

        // $encryptedData="ixj74LNxBFU7/OCJSsU4i8CG0zBv4X70naaZJow/i5c8eK/piXXYeNgG0KMphr95c/w9UsrdlapIXsAZRoKDQwrOeU1kPkHhyTPO7K/f1m4IUAqZ44V58Y0NR7wUdzeq9WSzPb2R1ou3lYKyOpXv2O26pShb1ZyinsLpYwYWj6h1Efo6RCSJZoPMuay4ZtoiMPzHH4AhNljjvF2cznNdaw==";

        // $iv = 'oxwasV6qdEMWR9D9Qk+4KA==';
        $data = '';
        $pc = new WXBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);

        if ($errCode == 0) {
            $data = json_decode($data,true);
            return $this->send_valiCode($data['phoneNumber']);
        } else {
            return '0';
        }
    }
    //餐饮APP获取价格
    public function exterior_get_price($miniapps_id,$id){
        $shop = Db::connect($this->connect2)->table('wm_shop')->where('miniapps_id',$miniapps_id)->field('lat,lng')->find();
        $order = Db::connect($this->connect2)->table('wm_order')->where('id',$id)->field('address,lat,lng')->find();
        $km = $this->GetDistance($shop['lat'], $shop['lng'], $order['lat'], $order['lng']);
        $order_area = Db::connect($this->connect)->table('user')->where('miniapps_id',$miniapps_id)->value('area');
        $re = $this->compute_price($order_area,$km);
        return $re;
    }
    //外部插入订单
    //$id订单的记录id
    public function insert_exterior_order($miniapps_id,$id,$price,$km){
        $user = Db::connect($this->connect)->table('user')->where('miniapps_id',$miniapps_id)->field('openid,area,area_jing,tel')->find();
        $order = Db::connect($this->connect2)->table('wm_order')->where('id',$id)->field('id,order_info,name,phone,address')->find();
        $order_num = $this->rand_num();
        $data = [
            'openid'=>$user['openid'],
            'order_num'=>$order_num,
            'from_tel'=>$user['tel'],
            'to_name'=>$order['name'],
            'to_tel'=>$order['phone'],
            'to_address'=>$order['address'],
            'start'=>time(),
            'price'=>$price,
            'from_area'=>$user['area_jing'],
            'order_type'=>2,
            'area'=>$user['area'],
            'km'=>$km,
            'ps'=>$order['order_info'],
            'order_id'=>$id
        ];
        $res = Db::connect($this->connect)->table('order')->insert($data);
        return $res?$order_num:'0';
    }
    //找到关联的商家
    public function my_self($miniapps_id){
        $openid = Db::connect($this->connect)->table('user')->where('miniapps_id',$miniapps_id)->value('openid');
        if(!$openid){
            return '0';
        }
        return $openid;
    }

    public function run_exterior_order($miniapps_id,$id){
        $openid = $this->my_self($miniapps_id);
        if($openid == '0'){
            return '2';//没有绑定
        }
        $order = Db::connect($this->connect)->table('order')->where('order_id',$id)->find();
        if(!$order){
            $re = $this->exterior_get_price($miniapps_id,$id);
            $order_num = $this->insert_exterior_order($miniapps_id,$id,$re['price'],$re['km']);
        }else{
            $order_num = $order['order_num'];
        }
        return '{"openid":"'.$openid.'","order_num":"'.$order_num.'"}';
    }
    //插入常用地址
    public function set_often($openid,$address,$tel,$name = ''){
      if($name != ''){
          $data = [
              'openid'=>$openid,
              'address'=>$address,
              'tel'=>$tel,
              'name'=>$name
          ];
          $res = Db::connect($this->connect)->table('to_often')->insert($data);
      }else{
          $data = [
              'openid'=>$openid,
              'address'=>$address,
              'tel'=>$tel
          ];
          $res = Db::connect($this->connect)->table('from_often')->insert($data);
      }
      return $res?'1':'0';
    }
    //查询常用地址
    public function get_often($openid,$type){
        if($type == '1'){
            $re = Db::connect($this->connect)->table('from_often')->where('openid',$openid)->order('id desc')->select();
        }else{
            $re = Db::connect($this->connect)->table('to_often')->where('openid',$openid)->order('id desc')->select();
        }
        return json_encode($re,JSON_UNESCAPED_UNICODE);
    }
    //删除常用地址
    public function del_often($id,$type){
        if($type == '1'){
            $re = Db::connect($this->connect)->table('from_often')->where('id',$id)->delete();
        }else{
            $re = Db::connect($this->connect)->table('to_often')->where('id',$id)->delete();
        }
        return $re?'1':'0'; 
    }
    //查询socket是否在线
    public function exist_socket($id){
        $socket = Db::connect($this->connect)->table('knight')->where('id',$id)->value('socket_id');
        return $socket?'1':'0';
    }
    //获取版本号
    public function get_versions(){
        $v = Db::connect($this->connect)->table('versions')->order('id desc')->find();
        if(!$v){
            return '0';//出错
        }
        return json_encode($v,JSON_UNESCAPED_UNICODE);
    }

    //订单退款
    private function order_refund($order_num,$price,$openid){
        $order = Db::connect($this->connect)->table('wx_pay')->where('order_num',$order_num)->find();
        if($order){
            return $this ->wx_refund($order_num,$price);
        }else{
            return $this ->balance_refund($order_num,$openid);
        }
    }
    //微信退款
    private function wx_refund($order_num,$price){
        $wx_refund = new WeixinRefund($this->$MCH_ID,$this->APPID,$this->KEY);
        $refundNo = $this ->rand_num();
        $res = $wx_refund ->doRefund($price,$price,$refundNo,'',$order_num);
        return json_encode($res);
    }
}
