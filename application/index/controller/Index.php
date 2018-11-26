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
        echo "tests";
    }

   public function login()
    {
    	$return = array(
           "errcode" => -1,
           "errmsg" => "fail"
    	);
        $code = mt_rand(1000,9999);

        $redis = new swoole_redis;

        $redis = $redis->connect('127.0.0.1',6379,function(swoole_redis $redis,$result){
             $redis->set("phone",$code,function(swoole_redis $redis,$result){
                 $return['errcode'] = 1;
                 $return['errmsg'] = "success";
             });

             $redis->close();
        });
        echo $return;
    }
}
