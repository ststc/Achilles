<?php
//log相关
class checkController extends BaseCtrl{
    /**
     * 访问log处理并展示
     */
    public function logAction(){
        //查询的是哪天的
        $date = isset($_REQUEST['date']) ? $_REQUEST['date'] : date('Y-m-d',time());
        //访问日志文件按天储存，判断文件的存在
        $log_file = __DIR__ . '/../log/visitor/' . $date . '.log';
        if(!file_exists($log_file)){
            echo "no visitors";die;
        }
        //获取原始log
        $log = file_get_contents($log_file);
        $raw_log = explode("\n",$log);
        array_pop($raw_log);
        //处理log
        $ip    = [];
        $hours = [];
        $api   = [];
        foreach($raw_log as $one){
            $row = explode("@",$one);
            //ip
            if(key_exists($row[0],$ip)){
                $ip[$row[0]] +=1;
            }else {
                $ip[$row[0]] =1;
            }
            //hours
            if(key_exists($row[1],$hours)){
                $hours[$row[1]] +=1;
            }else {
                $hours[$row[1]] =1;
            }
            //api
            if(key_exists($row[2],$api)){
                $api[$row[2]] +=1;
            }else {
                $api[$row[2]] =1;
            }
        }
        //组装数据
        $data = [
            'ip'    => $ip,
            'hours' => $hours,
            'api'   => $api,
            'raw'   => $raw_log,
            'title' => 'Visit_'.$date,
        ];
        //去页面上渲染
        $this->render('log',$data);
    }
}