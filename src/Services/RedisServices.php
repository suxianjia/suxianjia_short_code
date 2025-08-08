<?php
namespace Suxianjia\xianjia_short_code\Services;
use Suxianjia\xianjia_short_code\Factory\RedisFactory;

// use Suxianjia\xianjia_short_code\Driver\RedisDriver;

use Suxianjia\xianjia_short_code\Core\SystemConfig;
// use Suxianjia\xianjia_short_code\Interface\RedisInterface;
use Redis;
use RedisException;

class RedisServices  extends RedisFactory 
{
    private static $instance;
 
     public static $obj;

 

    /**
     * 单例模式
     * @return RedisServices
     */
    public static function getInstance(): RedisFactory {
        if (self::$instance === null) {
            self::getOBJ();
            self::$instance = new self();
        }
        return self::$instance;
    } 
    
    /**
     * 构造函数
     */
 
    private static function getOBJ(){
        $Database = SystemConfig::getInstance():: getModel('Redis');
        $master_config = $Database['master']; 
        $slaves_config = $Database['slaves']; 
        $driverType = $master_config['driverType'];
         self::$obj = self::createDriver(      $driverType , $master_config, $slaves_config);
    }

 
}
