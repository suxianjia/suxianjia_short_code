<?php
namespace Suxianjia\xianjia_short_code\Factory;

use Suxianjia\xianjia_short_code\Driver\RedisDriver;
use Suxianjia\xianjia_short_code\Driver\PredisDriver;

class RedisFactory {
    /**
     * 创建 Redis 驱动实例
     * @param string $driverType 驱动类型 (redis|predis)
     * @param array $config Redis 配置
     * @return RedisDriver|PredisDriver
     * @throws \InvalidArgumentException
     */
    public static function createDriver(string $driverType, array $config) {
        switch ($driverType) {
            case 'redis':
                return new RedisDriver($config);
            case 'predis':
                return new PredisDriver($config);
            default:
                throw new \InvalidArgumentException("Unsupported driver type: {$driverType}");
        }
    }
}