<?php
namespace Suxianjia\xianjia_short_code\Middleware;

use Suxianjia\xianjia_short_code\Driver\RedisDriver;

/**
 * 速率限制中间件
 */
class RateLimiter {
    private $redis;
    private $limit;
    private $window;

    public function __construct($limit = 10, $window = 60) {
        $this->redis = new RedisDriver();
        $this->limit = $limit;
        $this->window = $window;
    }

    /**
     * 检查是否超过限制
     * @param string $key 限制键
     * @return bool
     */
    public function check($key) {
        $count = $this->redis->get($key) ?: 0;
        if ($count >= $this->limit) {
            return false;
        }
        $this->redis->set($key, $count + 1, $this->window);
        return true;
    }
}