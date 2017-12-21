<?php
//客户端控制器
class clientController extends BaseCtrl{

    /**
     * 显示支付二维码，登记客户端等待推送
     */
    public function qrcodeAction(){
    	//获取请求参数
    	$request = $this->request;
        $data = [
           'title'=>'扫二维码后待推送',
        ];
        $this->render('qrcode',$data);
    }

    /**
     * 支付完成推送转跳消息
     */
    public function payAction(){
        //获取请求参数
        $request = $this->request;
        $data = [
           'title'=>'支付喽',
        ];
        $this->render('pay',$data);
    }

    /**
     * 直播页面，等待转跳
     */
    public function showAction(){
        //获取请求参数
        $request = $this->request;
        $data = [
           'title'=>'直播待转跳',
        ];
        $this->render('show',$data);
    }

    /**
     * 直播控制台
     */
    public function consoleAction(){
        //获取请求参数
        $request = $this->request;
        $data = [
           'title'=>'直播控制台',
        ];
        $this->render('console',$data);
    }
}