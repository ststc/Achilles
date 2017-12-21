<?php
//设置时区
date_default_timezone_set('Asia/Shanghai');
//设置host
define('HOST','127.0.0.1');
//包含组件和模型的文件夹
set_include_path(get_include_path() . PATH_SEPARATOR
. __DIR__ . '/component' . PATH_SEPARATOR
. __DIR__ . '/models' . PATH_SEPARATOR);
//引入配置
include_once(__DIR__."/base/params.php");
//引入基类
include_once(__DIR__."/base/app.php");

//普通http服务入口文件
$script_url = preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI']);
if (empty($script_url) || $script_url == '/') {
     $ad = "<h1>Hey Achilles. #</h1>
    	    <h1>auth:  范祯 #</h1>";
     echo $ad;die;
}

//路由匹配
$matched = false;
if (preg_match("#^/(\w+)/(\w+)$#", $script_url, $match)) {
     $matched = true;
}
//未匹配的返回
if (!$matched) {
    error(404,'api not found');
}
//若匹配，解析出控制器和方法
$controller = $match[1];
$action = $match[2];

//检查控制器的存在
$filename   = __DIR__. "/controller/".$controller."Controller.php";
if(file_exists($filename) === false){
    error(500,'controller '.$controller.' not exist');
}
//引入文件
include_once($filename);

//检查类的存在
$class_name = $controller."Controller";
if (class_exists($class_name) === false) {
     error(500,'class '.$class_name.' not exist');
}
//实例化类
$class = new $class_name($_SERVER);
if($class === false){
     error(500,'class '.$class_name.' new fail');
}
//检查方法的存在
if(method_exists($class, $action.'Action') === false){
     error(500,'action '.$action.' not exist');
}
//调用对应的方法
$class->{$action.'Action'}();

//统一错误返回
function error($code,$message){
    $res = [
       'code'  => $code,
       'desc'  => $message
    ];
    echo json_encode($res);die;
}