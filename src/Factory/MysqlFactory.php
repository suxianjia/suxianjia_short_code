<?php
namespace Suxianjia\xianjia_short_code\Factory;

use Suxianjia\xianjia_short_code\Driver\MysqliDriver;
use Suxianjia\xianjia_short_code\Driver\PdoDriver;
use Suxianjia\xianjia_short_code\Driver\MysqlDriver;

class MysqlFactory {
    /**
     * 创建数据库驱动实例
     * @param string $driverType 驱动类型 (mysqli|pdo|mysql)
     * @param array $config 数据库配置
     * @return MysqliDriver|PdoDriver|MysqlDriver
     * @throws \InvalidArgumentException
     */
    public static function createDriver(string $driverType, array $config) {
        switch ($driverType) {
            case 'mysqli':
                return new MysqliDriver($config);
            case 'pdo':
                return new PdoDriver($config);
            case 'mysql':
                return new MysqlDriver($config);
            default:
                throw new \InvalidArgumentException("Unsupported driver type: {$driverType}");
        }
    }
}