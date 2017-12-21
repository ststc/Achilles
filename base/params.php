<?php
/**
 * 全局参数配置
 */
class params{
    //mysql配置
    public static $mysql = [
         'host' => '127.0.0.1',
         'user' => 'root',
         'pass' => 'blued',
         'db'   => 'achilles',
    ];
    //redis配置
    public static $redis = [
         'host' => '127.0.0.1',
         'port' => '6379',
    ];
}