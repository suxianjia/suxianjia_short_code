<?php
namespace Suxianjia\xianjia_short_code\Driver;

use Redis;

/**
 * Redis 驱动类
 */
class RedisDriver {
    private $redis;

    public function __construct() {
        $this->redis = new Redis();
        $this->redis->connect(getenv('REDIS_HOST'), getenv('REDIS_PORT'));
        if (getenv('REDIS_PASSWORD')) {
            $this->redis->auth(getenv('REDIS_PASSWORD'));
        }
    }

    /**
     * 设置缓存
     * @param string $key 键
     * @param mixed $value 值
     * @param int $ttl 过期时间（秒）
     * @return bool
     */
    public function set($key, $value, $ttl = 0) {
        return $ttl > 0 ? $this->redis->setex($key, $ttl, $value) : $this->redis->set($key, $value);
    }

    /**
     * 获取缓存
     * @param string $key 键
     * @return mixed
     */
    public function get($key) {
        return $this->redis->get($key);
    }

    /**
     * 删除缓存
     * @param string $key 键
     * @return bool
     */
    public function delete($key) {
        return $this->redis->del($key);
    }
}