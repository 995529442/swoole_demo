<?php
class Ws
{
	CONST HOST = "0.0.0.0";
	CONST PORT = 9502;

	public $ws = null;
	public function __contruct(){
		$this->ws = new swoole_websocket_server(HOST,PORT);

		$this->ws->set([
              'worker_num' => 4,
              'task_worker_num' => 2,
		]);

		$this->ws->on("open",[$this,'onOpen']);
		$this->ws->on("message",[$this,'onMessage']);
		$this->ws->on("task",[$this,'onTask']);
		$this->ws->on("finish",[$this,'onFinish']);
		$this->ws->on("close",[$this,'onClose']);

		$this->ws->start();
	}

	public function onOpen($ws,$request){
		echo 'fd:'.$request->fd.PHP_EOL;
	}

	public function onMessage($ws,$frame){
		$ws->push($frame->fd,"返回的消息：".date("Y-m-d H:i:s",time()).PHP_EOL);
	}

	public function onTask($serv, $taskId, $workerId, $data){
		print_r($data);
		return "task finish:".$workerId.PHP_EOL;
	}

	public function onFinish($serv,$taskId,$data){
		echo "taskId:{$taskId}\n";
        echo "finish-data-sucess:{$data}\n";
	}

	public function onClose($ws,$fd){
		echo "clientid:{$fd}\n";
	}
}

$obj = new Ws();