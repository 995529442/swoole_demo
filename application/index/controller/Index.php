<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        var_dump($_GET);
    }

   public function test()
    {
        echo "tests4444444";
    }

   public function login()
    {
		$redis = new swoole_redis;
		$redis->connect("127.0.0.1",6379,function(swoole_redis $redis,$result){
			echo "success".PHP_EOL;
			
			$redis->set("name","sam",function(swoole_redis $redis,$result){
				 var_dump($result);
			});
			
			$redis->close();
		});
    }
}
