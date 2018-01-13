<?php
//设置时区
date_default_timezone_set('Asia/Shanghai');
//包含组件和模型的文件夹
set_include_path(get_include_path() . PATH_SEPARATOR
. __DIR__ . '/component' . PATH_SEPARATOR
. __DIR__ . '/models' . PATH_SEPARATOR);
//引入配置
include_once(__DIR__."/base/params.php");
//引入基类
include_once(__DIR__."/base/app.php");

//显示命令列表
if($argc == 1){
    cosole_list();
}else{
    //执行具体命令
    //路由匹配
    $matched = false;
    if (preg_match("#^(\w+)\:(\w+)$#", $argv[1], $match)) {
         $matched = true;
    }
    //未匹配的返回
    if (!$matched) {
        echo "api not found!\n";die;
    }
    //若匹配，解析出控制器和方法
    $controller = $match[1];
    $action = $match[2];
    //检查控制器的存在
    $filename   = __DIR__. "/console/".$controller.".php";
    if(file_exists($filename) === false){
        echo "controller ".$controller." not exist\n";die;
    }
    //引入文件
    include_once($filename);

    //检查类的存在
    if(class_exists($controller) === false) {
         echo "class ".$controller." not exist\n";die;
    }
    //实例化类
    $class = new $controller($argv);
    if($class === false){
         echo "class ".$controller." new fail\n";die;
    }
    //检查方法的存在
    if(method_exists($class, $action) === false){
         echo "action ".$action." not exist\n";die;
    }
    //Gearman需要调用后台任务
    if($controller == 'Gearman'){
        include_once(__DIR__."/base/gearman.php");
    }
    //最后调用对应的方法
    $class->{$action}();
}

//列出所有命令
function cosole_list(){
    $path = __DIR__.'/console';
    $files = [];
    foreach(scandir($path) as $file){ 
        //去除.和..以及隐藏文件 
        if(substr($file,0,1) == '.'){
            continue;
        }
        $files[] = $file;
    }
    if(!empty($files)){
        $comand = [];
        //获取所有命令
        foreach($files as $value){
            include_once($path.'/'.$value);
            $class = str_replace('.php', '', $value);
            $tmp = get_class_methods($class);
            //过滤构造函数
            if(!empty($tmp)){
                foreach($tmp as $v){
                    if($v != '__construct'){
                        $methods[] = $v;
                    }
                }
                $comand[$class] = $methods;
            }
        }
        if(!empty($comand)){
            echo "welcome to cosole!\n";
            foreach($comand as $k=>$v){
                echo $k."\n";
                foreach($v as $n){
                    echo '  '.$k.':'.$n."\n";
                }
            } 
        }else{
            echo "nothing a all!\n";die;
        }
        
    }else{
        echo "nothing b all!\n";die;
    }
}