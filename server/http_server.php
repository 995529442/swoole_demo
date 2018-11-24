<?php                                                                                                                                                
//实例化
$http_server = new swoole_http_server('0.0.0.0',9501);
 
//服务器配置
$http_server->set(
    [
        'enable_static_handler' => true,
        'document_root' => '/data/wwwroot/swoole_demo/public/static',
        'worker_num'=> 4,
    ]
);
 
 $http_server->on('WorkerStart',function($http_server,$worker_id){
	// 定义应用目录
	define('APP_PATH', __DIR__ . '/../application/');
	require __DIR__ . '/../thinkphp/base.php';
 });
$http_server->on('request',function($request ,$response){
    //设置响应头信息
    //$response->cookie('xyj','hello',86400);
    //服务器返回信息
    $response->end('http_server' . json_encode($request->get));
});
 
$http_server->start();