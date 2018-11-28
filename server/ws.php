<?php
class Ws {

    CONST HOST = "0.0.0.0";
    CONST PORT = 9502;

    public $ws = null;
	public $redis = null;
    public function __construct() {
        $this->ws = new swoole_websocket_server("0.0.0.0", 9502);

		$this->redis = new \Redis();
		$this->redis->connect("127.0.0.1",6379);
		
        $this->ws->set(
            [
			    'enable_static_handler' => true,
                'document_root' => '/data/wwwroot/swoole_demo/public/static',
                'worker_num' => 2,
                'task_worker_num' => 2,
            ]
        );
        $this->ws->on("open", [$this, 'onOpen']);
        $this->ws->on("message", [$this, 'onMessage']);
        $this->ws->on("task", [$this, 'onTask']);
        $this->ws->on("finish", [$this, 'onFinish']);
        $this->ws->on("close", [$this, 'onClose']);
        $this->ws->on("WorkerStart", [$this, 'onWorkerStart']);
		$this->ws->on("request", [$this, 'onRequest']);
        $this->ws->start();
    }

	 public function onWorkerStart($serv, $workerId) {
	 	// 定义应用目录
		define('APP_PATH', __DIR__ . '/../application/');
		require __DIR__ . '/../thinkphp/base.php';
	 }
	 
	public function onRequest($request, $response) {
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
         
		 $_POST['task'] = $this->ws;
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
	 }
	 
    /**
     * 监听ws连接事件
     * @param $ws
     * @param $request
     */
    public function onOpen($ws, $request) {
		$this->redis->sadd("fd",$request->fd);
        var_dump($request->fd);
    }

    /**
     * 监听ws消息事件
     * @param $ws
     * @param $frame
     */
    public function onMessage($ws, $frame) {
        echo "ser-push-message:{$frame->data}\n";
        // todo 10s
        $data = [
            'task' => 1,
            'fd' => $frame->fd,
        ];
        //$ws->task($data);

        /*swoole_timer_after(5000, function() use($ws, $frame) {
            echo "5s-after\n";
            $ws->push($frame->fd, "server-time-after:");
        });
        $ws->push($frame->fd, "server-push:".date("Y-m-d H:i:s"));*/
    }

    /**
     * @param $serv
     * @param $taskId
     * @param $workerId
     * @param $data
     */
    public function onTask($serv, $taskId, $workerId, $data) {
		if($data){
		   for($k=0;$k<count($data);$k++){
			  $this->ws->push($data[$k],"发送消息：".date("Y-m-d H:i:s",time())); 
		   }
		}
    
        return "on task finish"; // 告诉worker
    }

    /**
     * @param $serv
     * @param $taskId
     * @param $data
     */
    public function onFinish($serv, $taskId, $data) {
        echo "taskId:{$taskId}\n";
        echo "finish-data-sucess:{$data}\n";
    }

    /**
     * close
     * @param $ws
     * @param $fd
     */
    public function onClose($ws, $fd) {
		$this->redis->srem("fd",$fd);
        echo "clientid:{$fd}\n";
    }
}

$obj = new Ws();