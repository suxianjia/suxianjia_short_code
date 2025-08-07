<?php
namespace Suxianjia\xianjia_short_code\Interface;

/**
 * 缓存接口
 *  // Suxianjia\xianjia_short_code\Interface\RedisInterface
 */
interface RedisInterface {
    /**
     * 设置缓存
     * @param string $key 键
     * @param mixed $value 值
     * @param int $ttl 过期时间（秒）
     * @return bool
     */
    public function set($key, $value, $ttl = 0);

    /**
     * 获取缓存
     * @param string $key 键
     * @return mixed
     */
    public function get($key);

    /**
     * 删除缓存
     * @param string $key 键
     * @return bool
     */
    public function delete($key);
}