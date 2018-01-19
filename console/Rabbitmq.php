<?php
include_once("toolsComponent.php");

//Rabbitmq相关
class Rabbitmq extends BaseCmd{
    /**
     * 发送聊天信息
     */
    public function chat(){
        $params = $this->params;
        //参数接收
        if(isset($params[2])){
            //消息
            $send_uid  = $params[2];
            echo "user: ".$send_uid." log in!\n";
        }else{
            echo "user miss\n";die;
        }
        //连接配置
        $conn_args = array(
                'host'     => '127.0.0.1', //rabbitmq 服务器host
                'port'     => 5672,        //rabbitmq 服务器端口
                'login'    => 'guest',     //登录用户
                'password' => 'guest',     //登录密码
                'vhost'    => '/'          //虚拟主机
            );
        //单聊交换机
        $exchange = 'chat';
        //创建连接
        $conn = new AMQPConnection($conn_args);
        if(!$conn->connect()){
            die('Cannot connect to the broker');
        }
        //创建频道
        $channel = new AMQPChannel($conn);
        //创建交换机
        $ex = new AMQPExchange($channel);
        //交换机名称
        $ex->setName($exchange);
        $ex->setType(AMQP_EX_TYPE_DIRECT);//direct类型
        $ex->setFlags(AMQP_DURABLE);//持久化
        //声明一个交换机
        echo "Exchange Status: ".$ex->declare()."\n";
        //开始聊天
        toolsComponent::chat($send_uid, $ex);   
    }

    /**
     * 接收聊天信息
     */
    public function message(){
        //参数接收
        $params = $this->params;
        if(isset($params[2])){
            $receive_uid  = $params[2];
            $queue = $params[2];
            echo "receiver is ".$receive_uid."\n";
        }else{
            echo "receiver miss\n";die;
        }
        //连接配置
        $conn_args = array(
                'host'     => '127.0.0.1', //rabbitmq 服务器host
                'port'     => 5672,        //rabbitmq 服务器端口
                'login'    => 'guest',     //登录用户
                'password' => 'guest',     //登录密码
                'vhost'    => '/'          //虚拟主机
            );
        //队列信息
        $exchange  = 'chat';

        //创建连接
        $conn = new AMQPConnection($conn_args);
        if(!$conn->connect()){
            die('Cannot connect to the broker');
        }
        //创建频道
        $channel = new AMQPChannel($conn);
        //创建交换机
        $ex = new AMQPExchange($channel);
        //交换机名称
        $ex->setName($exchange);
        $ex->setType(AMQP_EX_TYPE_DIRECT);//direct类型
        $ex->setFlags(AMQP_DURABLE);//持久化
        //声明一个交换机
        echo "Exchange Status: ".$ex->declare()."\n";

        //创建队列
        $q = new AMQPQueue($channel);
        //队列名称
        $q->setName($queue);
        $q->setFlags(AMQP_DURABLE); //持久化
        echo "Queue Status: ".$q->declare()."\n";
        //绑定交换机与队列，并指定路由键 
        echo 'Queue Bind: '.$q->bind($exchange, $receive_uid)."\n";

        //自动ACK应答
        $q->consume('processMessage', AMQP_AUTOACK);
        //断开连接
        $conn->disconnect();        
    }    
}

/**
 * 消费回调函数
 * 处理消息
 */
function processMessage($envelope, $queue){
    $data = $envelope->getBody();
    $__ = json_decode($data);
    $msg = array();
    foreach($__ as $k=>$v){
        $msg[$k] = $v;
    }
    //处理消息
    echo "#-----------------#\n";
    if(isset($msg['group'])){
        echo "From ".$msg['from']." in ".$msg['group']."\n";
    }else{
        echo "From ".$msg['from']."\n";
    }
    echo "msg: ".$msg['msg']."\n";
    echo "time: ".$msg['time']."\n";
    echo "#-----------------#\n";     
}