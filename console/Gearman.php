<?php
//Gearman相关
class Gearman extends BaseCmd{

    public $func_list = ['normal', 'reverse'];
    /**
     * worker
     */
    public function worker(){
        print gearman_version() . "\n";
        $worker = new GearmanWorker();
        $worker->addServer("127.0.0.1", 4730);
        //挂载多个后台进程[具体操作在/base/gearman里][并注册在上方]
        $worker->addFunction("normal", "normal");
        $worker->addFunction("reverse", "reverse");
        while ($worker->work());
    }

    /**
     * client
     */
    public function client(){
        $params = $this->params;
        $func_list = $this->func_list;
        //参数检查
        if(count($params) < 4){
            echo "params not enough!\n";die;
        }
        //函数检查
        if(!in_array($params[2], $func_list)){
            echo "function not found!\n";die;
        }
        $client = new GearmanClient();    
        $client->addServer("127.0.0.1", 4730);    
        print $client->do($params[2], $params[3]);
        print "\n";
    }
    
}