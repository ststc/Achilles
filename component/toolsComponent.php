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
	public static function chat($send_uid, $ex){
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
		$to  = $__[0];
		$raw = $__[1];
	    //发送消息
		self::send($send_uid, $raw, $ex, $to);
		//输出发送信息
		fwrite(STDOUT, "send to ".$to." !\n");
		self::chat($send_uid, $ex);
	}

	/**
     * 发送至消息队列
     */
	public static function send($send_uid, $raw, $ex, $target){
		//设置群组信息
		$groups = [
		    'gid_1' => ['uid_1', 'uid_2', 'uid_3'],
		    'gid_2' => ['uid_3', 'uid_4'],
		];
		//指定路由[接受者]，发送消息
		date_default_timezone_set('PRC');
		$data = array();
		$data['from']  = $send_uid;
		$data['msg']   = $raw;
		$data['time']  = date('Y-m-d H:i:s',time());
		if(strpos($target, 'gid') === 0){
		    $data['group'] = $target;
		    //推到每个成员的队列上
		    $members = $groups[$target];
		    foreach($members as $v){
		        if($v != $send_uid){
		            $data['to'] = $v;
		            $msg = json_encode($data);
		            $ex->publish($msg, $v);
		        }        
		    }
		}else{
		    $data['to'] = $target;
		    $msg = json_encode($data);
		    //推到指定的用户上
		    $ex->publish($msg, $target);
		}
	}
}