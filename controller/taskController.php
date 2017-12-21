<?php
include_once("toolsComponent.php");
include_once("logModel.php");
//后台任务
class taskController extends BaseCtrl{
    /**
     * 后台任务demo，插入一条log
     */
    public function demoAction(){
    	//获取请求参数
    	$request = $this->request;
        //log数据
        $params = [
            'desc' => toolsComponent::random_str(),
            'date' => date('Y-m-d H:i:s',time()),
            'time' => time(),
        ];
        //插入数据
        $model = new logModel;
        $model->add_log($params);
        
        return true;
    }
}