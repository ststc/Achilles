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

//创建http服务
$http = new swoole_http_server("0.0.0.0", 9501);
//设置任务数
$http->set(['task_worker_num' => 10]);
//请求响应
$http->on('request', function ($request, $response) use($http){
    //获取路径
    $path_info  = isset($request->server['path_info']) ?
                  strval($request->server['path_info']) : '/';
    //浏览器的多余访问的过滤
    if($path_info === '/favicon.ico'){
        return true;
    }
    //设置响应头部
    $response->header("Content-Type", "text/html; charset=utf-8");
    //根路径提示[打个广告]
    if($path_info === '/'){
    	$ad = "<h1>Hey Achilles. #</h1>
    	       <h1>auth:  范祯 #</h1>";
        $response->end($ad);
        return true;
    }
    //请求数据
    $data = [
       'path'   => $path_info,
       'post'   => $request->post,
       'get'    => $request->get,
       'file'   => $request->post,
       'cookie' => $request->cookie,
    ];
    //后端任务投递
    $http->task($data);
    //直接响应
    echo "mission send\n";
    done($response);
});

//任务完成
$http->on('task', function ($serv, $task_id, $from_id, $data) {
	//路由匹配
    $matched = false;
    if (preg_match("#^/(\w+)/(\w+)$#", $data['path'], $match)) {
         $matched = true;
    }
    //未匹配的返回
    if (!$matched) {
	    error(404,'api not found');
	    return true;
    }
    //若匹配，解析出控制器和方法
    $controller = $match[1];
    $action = $match[2];
    
    //检查控制器的存在
    $filename   = __DIR__. "/controller/".$controller."Controller.php";
    if(file_exists($filename) === false){
        error(500,'controller '.$controller.' not exist');
        return true;
    } 
    //引入文件
    include_once($filename);
    
    //检查类的存在
    $class_name = $controller."Controller";
    if (class_exists($class_name) === false) {
         error(500,'class '.$class_name.' not exist');
         return true;
    }
    //实例化类
    $class = new $class_name($data);
    if($class === false){
         error(500,'class '.$class_name.' new fail');
         return true;
    }
    //检查方法的存在
    if(method_exists($class, $action.'Action') === false){
         error(500,'action '.$action.' not exist');
         return true;
    }
    //调用对应的方法
    $class->{$action.'Action'}();
    return true;
});

//任务结束
$http->on('finish', function ($serv, $task_id, $data) {
   echo "mission complete\n";
});

$http->start();

//统一正确返回
function done($response){
	$data = [
        'code'    => 200,
        'message' => 'back mission send',
    ];
	$response->end(json_encode($data));
}

//统一错误显示
function error($code,$message){
	$data = [
            'code'    => $code,
            'message' => $message,
	];
	var_dump($data);
}