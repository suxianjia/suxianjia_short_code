<?php
namespace Suxianjia\xianjia_short_code\Cache;

use Suxianjia\xianjia_short_code\Driver\RedisDriver;
use Suxianjia\xianjia_short_code\Interface\CacheInterface;

/**
 * 缓存管理类
 */
class CacheManager implements CacheInterface {
    private $driver;

    public function __construct() {
        $this->driver = new RedisDriver();
    }

    /**
     * 设置缓存
     * @param string $key 键
     * @param mixed $value 值
     * @param int $ttl 过期时间（秒）
     * @return bool
     */
    public function set($key, $value, $ttl = 0) {
        return $this->driver->set($key, $value, $ttl);
    }

    /**
     * 获取缓存
     * @param string $key 键
     * @return mixed
     */
    public function get($key) {
        return $this->driver->get($key);
    }

    /**
     * 删除缓存
     * @param string $key 键
     * @return bool
     */
    public function delete($key) {
        return $this->driver->delete($key);
    }
}