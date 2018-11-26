<?php
namespace app\index\controller;

class Index
{
	public function __construct(){
		$this->redis = new \Swoole\Coroutine\Redis()->connect('127.0.0.1',6379);
	}
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
        $res = $this->redis->set("sms_".$phone,$code,120);
        
        if($res){
        	$return['errcode'] = 1;
        	$return['errmsg'] = "success";
        }
        return json_encode($return);
    }
}
