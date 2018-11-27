<?php
namespace app\index\controller;

class Index
{
	public function __construct(){
		$this->redis = new \Swoole\Coroutine\Redis();

		$this->redis->connect('127.0.0.1',6379);

	}
    public function index()
    {
        var_dump($_GET);
    }

   public function checkLogin()
    {
       $phone = $_GET['phone_num'];
	   $code = $_GET['code'];
	   
	    $return = array(
           "errcode" => -1,
           "errmsg" => "fail"
    	);
		
	   $redis = new \Redis();
	   $redis->connect("127.0.0.1",6379);
	   
	   $old_num = $redis->get("sms_".$phone);
	   
	   if(!$old_num){
		   $return['errmsg'] = "验证码已过期";
	   }elseif($old_num != $code){
		   $return['errmsg'] = "验证码错误";
	   }else{
	        $return['errcode'] = 1;
        	$return['errmsg'] = "success";
	   }
	   
	   return json_encode($return);
    }

   public function login()
    {
    	$return = array(
           "errcode" => -1,
           "errmsg" => "fail"
    	);
    	$phone = $_GET['phone_num'];
        $code = mt_rand(1000,9999);

		//$_POST['task']->task($return);
		//exit;
        //协程redis
        $redis = new \Swoole\Coroutine\Redis();

        $redis->connect('127.0.0.1',6379);
		
        $res = $redis->set("sms_".$phone,$code,120);
        
        if($res){
        	$return['errcode'] = 1;
        	$return['errmsg'] = "success";
        }
        return json_encode($return);
    }
}
