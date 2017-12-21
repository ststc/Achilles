<?php
//log表
class logModel extends BaseModel{
	/**
   * 新增一条记录
   */
	public function add_log($params){
		//连接数据库
    $mysql = mysql_instance::init();
    //拼接sql语句
    $sql = $this->insert($params);
    $res = $mysql->query($sql);
    //显示结果
    if($res){
      echo "add log ok\n";
    }else{
      echo "add log fail\n";
    }
    return true;
	}
}