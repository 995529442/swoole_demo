<?php                                                                                                                                                
//实例化
$http_server = new swoole_http_server('0.0.0.0',9501);
 
//服务器配置
$http_server->set(
    [
        'enable_static_handler' => true,
        'document_root' => '/data/wwwroot/swoole_demo/public/static',
        'worker_num'=> 4,
		'task_worker_num' => 2,
    ]
);
 
 $http_server->on('WorkerStart',function($http_server,$worker_id){
	// 定义应用目录
	define('APP_PATH', __DIR__ . '/../application/');
	require __DIR__ . '/../thinkphp/base.php';
 });
$http_server->on('request',function($request ,$response) use($http_server){
     if(isset($request->server)){
     	foreach($request->server as $k=>$v){
     		$_SERVER[strtoupper($k)] = $v;
     	}
     }

    if(isset($request->header)){
     	foreach($request->header as $k=>$v){
     		$_SERVER[strtoupper($k)] = $v;
     	}
     }

    $_GET = [];
    if(isset($request->get)){
     	foreach($request->get as $k=>$v){
     		$_GET[$k] = $v;
     	}
     }

    $_POST = [];
    if(isset($request->post)){
     	foreach($request->post as $k=>$v){
     		$_POST[$k] = $v;
     	}
     }
     $_POST['task'] = $http_server;
     ob_start();
     // 2. 执行应用
     try{
        think\App::run()->send();
     }catch(\Exception $e){
     	echo $e->getMessage();
     }
     
     $res = ob_get_contents();
     ob_end_clean();
    //服务器返回信息
    $response->end($res);
});
 
  $http_server->on('task',function($task_id,$src_worker_id,$data){
	// 定义应用目录
	print_r($data);
 });
 
   $http_server->on('finish',function($task_id,$data){
      echo "finish".PHP_EOL;
 });


$http_server->start();