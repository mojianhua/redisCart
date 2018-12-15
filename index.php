<?php
define('BASE', realpath('./'));
/**
* redis 购物车类 
*/
class Index
{
	private $redis;
	protected $host = '127.0.0.1';
    protected $port = '6379';
	function __construct()
	{
		include BASE.'/RedisClass.php';
		$config['host'] = $this->host;
		$config['port'] = $this->port;
		$this->redis = new RedisClass($config);
	}

	/**
	* 处理购物车
	* @param $uid 用户id
	* @param $good_id 产品id
	* @param $num 增加或减少的数量
	*/
	public function add_goods($uid = '',$good_id = '',$num = 1){
		if(!empty($uid) && !empty($good_id)){
			$exits = $this->good_is_exits($uid,$good_id);
			if($exits){
				$total = $this->redis->hGet('user_'.$uid,'goodId_'.$good_id);
				//如果购物车数量不够减去删除的数量，直接报错
				if(intval($total) + intval($num) < 0){
					$this->json('','cart Goods not enouth');
				}
				//产品数量
				$add = $this->redis->hIncrBy('user_'.$uid,'goodId_'.$good_id,$num);
				$res = json_encode(['goodId_'.$good_id=>$add,'uid'=>$uid]);
				$this->json(200,'success',$res);
			}else{
				//判断购物车是否存在商品，如果商品不存在还执行减的操作则报错
				if(empty($exits) && $num <= 0){
					$this->json('','cart Goods Is empty');
				}
				$this->redis->hSet('user_'.$uid,'goodId_'.$good_id,1);
				$res = json_encode(['goodId_'.$good_id=>1,'uid'=>$uid]);
				$this->json(200,'success',$res);
			}
		}else{
			$this->json();
		}
	}

	/**
	* 购物车列表
	* @param $uid 用户id
	*/
	public function cart_list($uid = ''){
		if(!empty($uid)){
			$res = json_encode($this->redis->hGetAll('user_'.$uid));
			$this->json(200,'success',$res);
		}else{
			$this->json();
		}
	}

	/**
	* 移除商品
	* @param $uid 用户id
	* @param $good_id 产品id
	*/
	public function del_goods($uid = '',$good_id = ''){
		if(!empty($uid) && !empty($good_id)){
			$res = $this->redis->hDel('user_'.$uid,'goodId_'.$good_id);
			$this->json(200,'success',$res);
		}else{
			$this->json();
		}
	}

	/**
	* 清空购物车
	* @param $uid 用户id
	*/
	public function del_cart($uid){
		if(!empty($uid)){
			$res = $this->redis->delete('user_'.$uid);
			$this->json(200,'success',$res);
		}else{
			$this->json();
		}
	}

	//json 公共函数
	private function json($code = 0,$msg = 'error', $data = ''){
		$res['code'] = $code;
		$res['msg'] = $msg;
		$res['data'] = $data;
		exit(json_encode($res));
	}

	/**
	* 判断商品是否存在
	* @param $uid 用户id
	* @param $good_id 产品id
	*/
	private function good_is_exits($uid = '',$good_id = ''){
		if(empty($uid) || empty($good_id)){
			$this->json();
		}
		return $this->redis->hExists('user_'.$uid,'goodId_'.$good_id);
	}
}

$index = new Index();
//$add = $index->add_goods(1,2,1);
//{"code":200,"msg":"success","data":"{\"goodId_1\":5,\"uid\":1}"}
//$cart_list = $index->cart_list(1);
//{"code":200,"msg":"success","data":"{\"goodId_1\":\"5\",\"goodId_2\":\"1\"}"}
//print_r($cart_list);
//$del = $index->del_cart(1);
//{"code":200,"msg":"success","data":1}
//$cart_list = $index->cart_list(1);
//{"code":200,"msg":"success","data":"{\"goodId_1\":\"5\",\"goodId_2\":\"1\"}"}
//$del = $index->del_goods(1,1);
//{"code":200,"msg":"success","data":1}
$cart_list = $index->cart_list(1);
print_r($cart_list);
//{"code":200,"msg":"success","data":"{\"goodId_2\":\"1\"}"}