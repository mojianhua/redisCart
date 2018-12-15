<?php
/**
* redis类
*/
class RedisClass
{
	private $redis;
	function __construct()
	{
		$this->redis = new Redis();
		try{
			$this->redis->connect('127.0.0.1', 6379);
		}catch (Exception $e) {
			exit($e->getMessage());
		}
	}

	public function delete($key){
		return $this->redis->delete($key);
	}

	public function hSet($key,$field,$value)
    {
        return $this->redis->hSet($key,$field,$value);
    }

	public function hGet($key,$filed){
		return $this->redis->hGet($key,$filed);
	}

	public function hDel($key,$filed){
		if(is_array($filed)){
			$delNum  = 0;
			foreach ($filed as $value) {
				if($this->redis->hDel($key,$value)){
					$delNum++;
				}
			}
			return $delNum;
		}else{
			return $this->redis->hDel($key,$filed);
		}
	}

	public function hGetAll($key){
		return $this->redis->hGetAll($key);
	}

	public function hExists($key,$filed){
		return $this->redis->hExists($key,$filed);
	}

	public function hIncrBy($key,$filed,$value){
		$num = $this->redis->hGet($key,$filed);
		if($num > 0){
			return $this->redis->hIncrBy($key,$filed,$value);
		}else{
			return $this->redis->hIncrBy($key,$filed,abs($num));
		}
	}
}