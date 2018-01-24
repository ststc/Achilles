<?php
//工具组件
class toolsComponent{
	/**
     * 获取随机字符串
     */
	public static function random_str(){
        $order_date = date('Y-m-d');
		//订单号码主体（YYYYMMDDHHIISSNNNNNNNN）
		$order_id_main = date('YmdHis') . rand(10000000,99999999);
		//订单号码主体长度
		$order_id_len = strlen($order_id_main);
		$order_id_sum = 0;
		for($i=0; $i<$order_id_len; $i++){
		     $order_id_sum += (int)(substr($order_id_main,$i,1));
		}
		//唯一订单号码（YYYYMMDDHHIISSNNNNNNNNCC）
		$order_id = $order_id_main . str_pad((100 - $order_id_sum % 100) % 100,2,'0',STR_PAD_LEFT);

		return $order_id;
	}

	/**
     * 聊天信息
     */
	public static function chat($send_uid, $ex, $channel){
		//等待输入
		fwrite(STDOUT, "Enter your msg: \n");  
		//获取输入  
		$msg = trim(fgets(STDIN));
		if($msg == 'bye'){
			//结束聊天
			echo "chat over!\n";die;
		}
		//解析信息
		$__ = explode(':', $msg);
		//错误的信息格式
		if(count($__) < 2){
			echo "!message formated wrong\n";
			self::chat($send_uid, $ex, $channel);
		}
		$to  = $__[0];
		$raw = $__[1];
	    //发送消息
	    self::send($send_uid, $raw, $ex, $channel, $to);	
		//输出发送信息
		fwrite(STDOUT, "send to ".$to." !\n");
		self::chat($send_uid, $ex, $channel);
	}

	/**
     * 发送至消息队列
     */
	public static function send($send_uid, $raw, $ex, $channel, $target){
		//指定路由[接受者id]，发送消息
		date_default_timezone_set('PRC');
		$data = array();
		$data['from']  = $send_uid;
		$data['msg']   = $raw;
		$data['time']  = date('Y-m-d H:i:s',time());
		if(strpos($target, 'gid') === 0){
		    $data['group'] = $target;
		    $msg = json_encode($data);
		    //创建群聊交换机
	        $g_ex = new AMQPExchange($channel);
	        //交换机名称
	        $g_ex->setName($target);
	        $g_ex->setType(AMQP_EX_TYPE_FANOUT);//fanout类型
	        $g_ex->setFlags(AMQP_DURABLE);//持久化
	        //声明群聊交换机
	        echo "#Exchange Status: ".$g_ex->declare()."\n";
		    //推送到群组
		    $g_ex->publish($msg, '');
		    echo "#use group!\n";
		    echo "#target is ".$target."\n";
		}else if(strpos($target, 'uid') === 0){
		    $data['to'] = $target;
		    $msg = json_encode($data);
		    //推到指定的用户
		    $ex->publish($msg, $target);
		    echo "#use chat!\n";
		    echo "#target is ".$target."\n";
		}else{
			//错误的信息格式
			echo "!message formated wrong\n";
			self::chat($send_uid, $ex, $channel);
		}
	}
}