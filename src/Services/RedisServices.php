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

    public function __construct(RedisDriver $redisDriver = null)
    {
        $this->redisDriver = $redisDriver ?? new RedisDriver();
        $config = SystemConfig::getInstance()::getModel('Redis');
        $this->redis = new Redis();
       
        
        try {
            $connected = $this->redis->connect(
                $config['host'] ?? '127.0.0.1',
                $config['port'] ?? 6379,
                $config['timeout'] ?? 2.5
            );
            
            if (!$connected) {
                throw new RedisException("Redis连接超时");
            }
            
            if (!empty($config['password'])) {
                $this->redis->auth($config['password']);
            }
            
            if (!empty($config['database'])) {
                $this->redis->select($config['database']);
            }
        } catch (RedisException $e) {
            throw new \RuntimeException("Redis连接失败: ".$e->getMessage());
        }
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

    public function set(string $key, $value, int $ttl = 0): bool
    {
        try {
            if ($ttl > 0) {
                return $this->setex($key, $ttl, $value);
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

    public function get(string $key)
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

    public function delete(string $key): bool
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
