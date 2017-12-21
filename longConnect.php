<?php
//设置时区
date_default_timezone_set('Asia/Shanghai');
//设置host
define('HOST','127.0.0.1');
//包含组件和模型的文件夹
set_include_path(get_include_path() . PATH_SEPARATOR
. __DIR__ . '/component' . PATH_SEPARATOR
. __DIR__ . '/models' . PATH_SEPARATOR);
//引入配置
include_once(__DIR__."/base/params.php");
//引入基类
include_once(__DIR__."/base/app.php");

//创建websocket服务器对象，监听0.0.0.0:9502端口
$ws = new swoole_websocket_server("0.0.0.0", 9502);

//监听WebSocket连接打开事件
$ws->on('open', function ($ws, $request) {
    echo "new client is coming\n";
});

//监听WebSocket消息事件
$ws->on('message', function ($ws, $frame) {
	//接受参数
    $raw = $frame->data;
    $message = json_decode($raw);
    //处理消息
    $data = message_handle($message,$frame->fd);
    //推送消息
    if(isset($data['fds'])){
       //正儿八经的推送
       $sent['code'] = $data['code'];
       $sent['desc'] = $data['desc'];
       if(!$data['fds']){
           //发送后还要推送
       	   $ws->push($frame->fd, json_encode(['status'=>0]));
       }else{
       	   //挨个推送
       	   foreach($data['fds'] as $one){
               $ws->push($one, json_encode($sent));
	       }
	       //完成后的反馈
	       $ws->push($frame->fd, json_encode(['status'=>1]));
       }
    }else {
       //客户端注册的推送
       $ws->push($frame->fd, json_encode($data));
    }
});

//监听WebSocket连接关闭事件
$ws->on('close', function ($ws, $fd) {
    //从redis里取出与fd对应的raw，判断怎么处理关闭事件
    $redis = redis_instance::init();
    $message = unserialize($redis->hget('long_session', $fd));
    //过滤未发消息的
    if($message){
        //首先sent就不处理了
	    if($message->sent == 0){
	    	//访问参数
	    	$data = $message->data;
            if($message->type == 'one'){
               //单客户端的去哈希里除名
               $redis->hdel('long_'.$data->event, $data->param);
            }else if($message->type == 'group'){
               //群组推送的去set里除名
               $redis->sRem('long_'.$data->event,$fd);
            }
	    }
	    //去除访问session
        $redis->hdel('long_session', $fd);
    }
    echo "client-{$fd} is closed\n";
});

$ws->start();

//消息分拣处理
function message_handle($message,$fd){
    //参数检查
    if(!isset($message->controller) || !isset($message->action) || !isset($message->data)){
        $data = [
            'code' => '500',
            'desc' => 'lose params',
        ];
        return $data;
    }
    $controller = $message->controller;
    $action     = $message->action;
    $raw_data   = $message->data;

    //检查控制器的存在
    $filename   = __DIR__. "/controller/".$controller."Controller.php";
    if(file_exists($filename) === false){
        $data = [
            'code' => '500',
            'desc' => 'controller '.$controller.' not exist',
        ];
        return $data;
    }
    include_once($filename);
    
    //检查类的存在
    $class_name = $controller."Controller";
    if (class_exists($class_name) === false) {
         $data = [
            'code' => '500',
            'desc' => 'class '.$class_name.' not exist',
         ];
         return $data;
    }
    //实例化类
    $class = new $class_name($raw_data);
    if($class === false){
         $data = [
            'code' => '500',
            'desc' => 'class '.$class_name.' new fail',
         ];
         return $data;
    }
    //检查方法的存在
    if(method_exists($class, $action.'Action') === false){
         $data = [
            'code' => '500',
            'desc' => 'action '.$action.' not exist',
         ];
         return $data;
    }
    //调用对应的方法
    $data =  $class->{$action.'Action'}($fd);
    //在redis里建立fd与raw的哈希
    $redis = redis_instance::init();
    $redis->hset('long_session', $fd, serialize($message)); 
    return $data;
}