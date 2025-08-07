<?php
namespace Suxianjia\xianjia_short_code\Driver;
use Suxianjia\xianjia_short_code\Interface\RedisInterface;
use Predis\Client;

/**
 * Redis 驱动类（基于 Predis）
 */
class RedisDriver implements RedisInterface {
    private $client;

    public function __construct($config) {
        $this->client = new Client([
            'scheme' => 'tcp',
            'host'   => getenv('REDIS_HOST'),
            'port'   => getenv('REDIS_PORT'),
            'password' => getenv('REDIS_PASSWORD') ?: null,
        ]);
    }

    /**
     * 设置缓存
     * @param string $key 键
     * @param mixed $value 值
     * @param int $ttl 过期时间（秒）
     * @return bool
     */
    public function set($key, $value, $ttl = 0) {
        if ($ttl > 0) {
            $this->client->setex($key, $ttl, $value);
        } else {
            $this->client->set($key, $value);
        }
        return true;
    }

    /**
     * 获取缓存
     * @param string $key 键
     * @return mixed
     */
    public function get($key) {
        return $this->client->get($key);
    }

    /**
     * 删除缓存
     * @param string $key 键
     * @return bool
     */
    public function isConnected(): bool {
        return $this->client->isConnected();
    }

    /**
     * 关闭连接
     */
    public function close(): void {
        $this->client->disconnect();
    }

    /**
     * 删除缓存
     * @param string $key 键
     * @return bool
     */
    public function delete($key) {
        return $this->client->del([$key]) > 0;
    }
}