<?php
include_once("toolsComponent.php");

//Rabbitmq相关
class Rabbitmq extends BaseCmd{
    
    //群组关系
    public $group = [
                'uid_1' => ['gid_1'],
                'uid_2' => ['gid_1'],
                'uid_3' => ['gid_1', 'gid_2'],
                'uid_4' => ['gid_2'],
           ];
    //自我标识
    public static $me;
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
        //创建连接
        $conn = new AMQPConnection($conn_args);
        if(!$conn->connect()){
            die('Cannot connect to the broker');
        }
        //创建频道
        $channel = new AMQPChannel($conn);

        //创建单聊交换机
        $ex = new AMQPExchange($channel);
        //交换机名称
        $ex->setName('chat');
        $ex->setType(AMQP_EX_TYPE_DIRECT);//direct类型
        $ex->setFlags(AMQP_DURABLE);//持久化
        //声明单聊交换机
        echo "Exchange Status: ".$ex->declare()."\n";
        //开始聊天
        toolsComponent::chat($send_uid, $ex, $channel);   
    }

    /**
     * 接收聊天信息
     */
    public function message(){
        //参数接收
        $params = $this->params;
        if(isset($params[2])){
            $receive_uid  = $params[2];
            $queue        = $params[2];
            self::$me     = $params[2];
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
        //创建连接
        $conn = new AMQPConnection($conn_args);
        if(!$conn->connect()){
            die('Cannot connect to the broker');
        }
        //创建频道
        $channel = new AMQPChannel($conn);

        //创建单聊交换机
        $ex = new AMQPExchange($channel);
        //交换机名称
        $ex->setName('chat');
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
        echo 'Queue Bind: '.$q->bind('chat', $receive_uid)."\n";
        
        //获取群组关系
        $all = $this->group;
        if(isset($all[$receive_uid])){
            $groups = $all[$receive_uid];
            foreach($groups as $v){
                //创建群聊交换机
                $g_ex = new AMQPExchange($channel);
                //交换机名称
                $g_ex->setName($v);
                $g_ex->setType(AMQP_EX_TYPE_FANOUT);//fanout类型
                $g_ex->setFlags(AMQP_DURABLE);//持久化
                //声明群聊交换机
                echo "Exchange ".$v." Status: ".$g_ex->declare()."\n";
                echo 'Queue '.$v.' Bind: '.$q->bind($v, '')."\n";             
            }
        }
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
    //自己的信息不处理
    $me = Rabbitmq::$me;
    if($msg['from'] != $me){
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
}