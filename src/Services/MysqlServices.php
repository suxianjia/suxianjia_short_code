<?php
namespace Suxianjia\xianjia_short_code\Services;
defined('ROOT_PATH') or die('Missing constant ROOT_PATH.' . PHP_EOL);
use Suxianjia\xianjia_short_code\Factory\MysqlFactory;
use Suxianjia\xianjia_short_code\Core\SystemConfig;
use Suxianjia\xianjia_short_code\Interface\DBInterface;

/**
 * MySQL 服务类
 */
class MysqlServices extends MysqlFactory {
    private static $instance;
    public static $obj;

    /**
     * 单例模式
     * @return MysqlServices
     */
    public static function getInstance(): MysqlServices {
        if (self::$instance === null) {
            self::getOBJ();
            self::$instance = new self();
        }
        return self::$instance;
    } 
    
    /**
     * 构造函数
     */
    // private function __construct() {

    // } 

    private static function getOBJ(){
        $Database = SystemConfig::getInstance():: getModel('Database');
        $master_config = $Database['master']; 
        $slaves_config = $Database['slaves']; 
        $driverType = $master_config['driverType'];
        self::$obj = self::createDriver(      $driverType , $master_config, $slaves_config);
    }

    /**
     * 插入数据
     * @param string $table 表名
     * @param array $data 数据
     * @return bool
     */
    public function insert(string $table, array $data): bool {
        return self::$obj->insert($table, $data);
    }


}