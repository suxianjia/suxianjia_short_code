<?php
namespace Suxianjia\xianjia_short_code\Driver;
use Suxianjia\xianjia_short_code\Interface\RedisInterface;
use Predis\Client;

/**
 * Redis 驱动类（基于 Predis） 驱动类（支持主从配置）
 */
class PredisDriver implements RedisInterface {
    private $master;
    private $slaves = [];
    private $currentConnection;

    /**
     * 创建 Redis 连接
     * @param array $config Redis 配置
     * @return Client
     */
    private function createConnection(array $config): Client {
        return new Client([
            'scheme' => $config['scheme'] ?? 'tcp',
            'host'   => $config['host'],
            'port'   => $config['port'],
            'password' => $config['password'] ?? null,
        ]);
    }

        public function __construct(array $masterConfig, array $slaveConfigs = []) {
        $this->master = $this->createConnection($masterConfig);
        foreach ($slaveConfigs as $config) {
            $this->slaves[] = $this->createConnection($config);
        }
        $this->currentConnection = $this->master;
    }

    /**
     * 切换到主库
     */
    public function useMaster() {
        $this->currentConnection = $this->master;
    }


    /**
     * 切换到从库
     */
    public function useSlave() {
        if (!empty($this->slaves)) {
            $this->currentConnection = $this->slaves[array_rand($this->slaves)];
        }
    }
 
    /**
     * 设置带过期时间的键值对
     * @param string $key 键名
     * @param int $ttl 过期时间（秒）
     * @param string $value 值
     * @return bool
     */
    public function setex(string $key, int $ttl, string $value): bool {
        return $this->currentConnection->setex($key, $ttl, $value);
    }
 


    /**
     * 设置缓存
     * @param string $key 键
     * @param mixed $value 值
     * @param int $ttl 过期时间（秒）
     * @return bool
     */
    public function set($key, $value, $ttl = 0) {
        $this->useMaster();
        try {
            if ($ttl > 0) {
                $this->currentConnection->setex($key, $ttl, $value);
            } else {
                $this->currentConnection->set($key, $value);
            }
            return true;
        } catch (\Exception $e) {
            throw new \RuntimeException("Redis 设置缓存失败: " . $e->getMessage());
        }
    }

    /**
     * 获取缓存
     * @param string $key 键
     * @return mixed
     */
    public function get($key) {
        $this->useSlave();
        try {
            return $this->currentConnection->get($key);
        } catch (\Exception $e) {
            throw new \RuntimeException("Redis 获取缓存失败: " . $e->getMessage());
        }
    }

    /**
     * 删除缓存
     * @param string $key 键
     * @return bool
     */
    public function isConnected(): bool {
        return $this->currentConnection->isConnected();
    }

    /**
     * 关闭连接
     */
    public function close(): void {
        $this->currentConnection->disconnect();
    }

    /**
     * 删除缓存
     * @param string $key 键
     * @return bool
     */
    public function delete($key) {
        return $this->currentConnection->del([$key]) > 0;
    }

    

}