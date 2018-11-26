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
    	$return = array(
           "errcode" => -1,
           "errmsg" => "fail"
    	);
    	$phone = $_GET['phone_num'];
        $code = mt_rand(1000,9999);

        //协程redis
        $redis = new \Swoole\Coroutine\Redis();

        $redis = $redis->connect('127.0.0.1',6379);
        $res = $redis->set("sms_".$phone,$code,120);
        
        if($res){
        	$return['errcode'] = 1;
        	$return['errmsg'] = "success";
        }
        echo $return;
    }
}
