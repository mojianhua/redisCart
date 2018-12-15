<?php
define('BASE', realpath('./'));
/**
* redis 购物车类 
*/
class Index
{
	
	function __construct()
	{
		include BASE.'/RedisClass.php';
		$redis = new RedisClass();
	}
}

$index = new Index();
var_dump($index);