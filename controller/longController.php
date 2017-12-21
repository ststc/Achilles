<?php
//长连接控制器
class longController extends BaseCtrl{
    
    /**
     * 显示支付二维码，登记客户端等待推送
     */
    public function qrcodeAction($fd){
    	//获取请求参数
    	$request = $this->request;
        //注册客户端到redis对应的key上
        $redis = redis_instance::init();
        $redis->hset('long_'.$request->event, $request->param, $fd); 
        //返回数据
        $data = [
           'code' => '300',
           'desc' => 'waiting...',
        ];
        return $data;
    }
    
    /**
     * 支付完成推送转跳消息
     */
    public function paidAction($fd){
        //获取请求参数
        $request = $this->request;
        //获取要推送的fd
        $redis = redis_instance::init();
        $fd = $redis->hget('long_'.$request->event, $request->param); 
        //重复推送事件的处理
        $fds = $fd ? [$fd] : $fd;
        //消掉注册的信息
        $redis->hdel('long_'.$request->event, $request->param);
        //返回数据
        $data = [
           'code' => '200',
           'desc' => 'ok',
           'fds'  => $fds,
        ];
        return $data;
    }

    /**
     * 直播页面，等待转跳
     */
    public function showAction($fd){
        //获取请求参数
        $request = $this->request;
        //注册客户端到redis对应的set上
        $redis = redis_instance::init();
        $redis->sAdd('long_'.$request->event,$fd);
        //返回数据
        $data = [
           'code' => '300',
           'desc' => 'waiting...',
        ];
        return $data;
    }

    /**
     * 直播控制台
     */
    public function consoleAction($fd){
        //获取请求参数
        $request = $this->request;
        //注册客户端到redis对应的set上
        $redis = redis_instance::init();
        //获取所有的客户端
        $fds = $redis->sMembers('long_'.$request->event);
        //重复推送事件的处理
        if(empty($fds)){
           $fds = false;
        }
        //消掉注册的信息
        $redis->del('long_'.$request->event);
        //返回数据
        $data = [
           'code' => '200',
           'desc' => 'ok',
           'fds'  => $fds,
        ];
        return $data;
    }

}