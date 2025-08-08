<?php
namespace Suxianjia\xianjia_short_code\Services;
use Suxianjia\xianjia_short_code\Core\SystemConfig; // $config = SystemConfig::getInstance():: getModel('Database');
// use Suxianjia\xianjia_short_code\Core\SystemConfig;
use Suxianjia\xianjia_short_code\Driver\RedisDriver;
use Suxianjia\xianjia_short_code\Interface\RedisInterface;
use Redis;
use RedisException;

class RedisServices implements RedisInterface  
{
    private static $instance;
    private $redis;
    private $redisDriver;

    public function __construct( )
    { 
                $Redis = SystemConfig::getInstance():: getModel('Redis');
        $master_config = $Redis['master']; 
        $slaves_config = $Redis['slaves']; 

        $this->redis = new RedisDriver($master_config, $slaves_config);
 
    }

    public static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): Redis
    {
        if (!$this->redis->ping()) {
            $this->__construct(); // 自动重连
        }
        return $this->redis;
    }

    public function set($key, $value, $ttl = 0): bool
    {
        try {
            if ($ttl > 0) {
                return $this->redis->setex($key, $ttl, $value);
            }
            return $this->redis->set($key, $value);
        } catch (RedisException $e) {
            throw new \RuntimeException("Redis操作失败: ".$e->getMessage());
        }
    }

    public function setex(string $key, int $ttl, $value): bool
    {
        try {
            return $this->redis->setex($key, $ttl, $value);
        } catch (RedisException $e) {
            throw new \RuntimeException("Redis操作失败: ".$e->getMessage());
        }
    }

    public function get($key)
    {
        try {
            return $this->redis->get($key);
        } catch (RedisException $e) {
            throw new \RuntimeException("Redis操作失败: ".$e->getMessage());
        }
    }

    public function exists(string $key): bool
    {
        try {
            return (bool)$this->redis->exists($key);
        } catch (RedisException $e) {
            throw new \RuntimeException("Redis操作失败: ".$e->getMessage());
        }
    }

    public function delete($key): bool
    {
        try {
            return $this->redis->del($key) > 0;
        } catch (RedisException $e) {
            throw new \RuntimeException("Redis操作失败: ".$e->getMessage());
        }
    }

    public function __destruct()
    {
        if ($this->redis && $this->redis->isConnected()) {
            $this->redis->close();
        }
    }
}
