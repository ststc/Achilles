<?php
/**
 * 控制器基类
 */
class BaseCtrl{
    //请求
    public $request;
    //构造函数
    public function __construct($request){
        $this->request = $request;
    }
    //渲染页面
    public function render($view,$data=[]){
       //获取子类名
       $son = get_called_class();
       $folder = str_replace('Controller', '', $son);
       //解析数据
       if(!empty($data)){
           extract($data);
       }
       //视图路径
       $dir = __DIR__."/../view/".$folder."/".$view.".php";
       include_once($dir);
    }
}

/**
 * 控制台基类
 */
class BaseCmd{
    //请求
    public $params;
    //构造函数
    public function __construct($params){
        $this->params = $params;
    }
}

/**
 * model基类
 */
class BaseModel{
    //返回表名
    public function table(){
       //获取子类名
       $son = get_called_class();
       $table = str_replace('Model', '', $son);
       return $table;
    }
    //新增方法
    public function insert($params){
        $sql = "insert into ".$this->table();
        $key = "(`";
        $value= "('";
        $num = 1;
        $count = count($params);
        foreach($params as $k=>$v){
            if($num == $count){
               $key .= $k."`)";
               $value .= $v."')";
            }else {
               $key .= $k."`, `";
               $value .= $v."', '";
            }
            $num ++;
        }
        $sql .= $key." values".$value;
        return $sql;
    }
}

/**
 * redis实例
 */
class redis_instance{
    public static function init(){
        //获取redis参数
        $params = params::$redis;
        //连接redis
        $redis = new Redis();
        $redis->connect($params['host'], $params['port']);
        return $redis;
    }
}

/**
 * mysql实例
 */
class mysql_instance{
    public static function init(){
        //获取mysql参数
        $params = params::$mysql;
        $mysqli = new mysqli($params['host'], $params['user'], $params['pass'], $params['db']);
        return $mysqli;
    }
}