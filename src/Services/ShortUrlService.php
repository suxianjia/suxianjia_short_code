<?php
namespace Suxianjia\xianjia_short_code\Services;
use Suxianjia\xianjia_short_code\Core\SystemConfig; // $config = SystemConfig::getInstance():: getModel('Database');
 
use Suxianjia\xianjia_short_code\Services\MysqlServices; //      MysqlServices::getInstance()
use Suxianjia\xianjia_short_code\Services\RedisServices; //        RedisServices::getInstance()
 

/**
 * 短链接服务类
 */
class ShortUrlService {
  
    private static $instance;
    /**
     * 获取服务实例
     * @param array $dbConfig 数据库配置
     * @param array $redisConfig Redis 配置
     * @return ShortUrlService
     */
    public static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 缩短 URL
     * @param string $longUrl 原始 URL
     * @return array 包含短码的数组
     */
    public function shorten($longUrl) {
        $shortCode = $this->generateShortCode();
 $db = MysqlServices::getInstance();
        $redis = RedisServices::getInstance();
        $db->insert('short_urls', [
            'long_url' => $longUrl,
            'short_code' => $shortCode
        ]);
        $redis->setex("shorturl:$shortCode", 3600, $longUrl);
        return ['short_code' => $shortCode];
    }

    /**
     * 获取原始 URL
     * @param string $shortCode 短码
     * @return array 包含原始 URL 的数组
     */
    public function getOriginalUrl($shortCode) {
$db = MysqlServices::getInstance();
$redis = RedisServices::getInstance();

        $longUrl =        $redis->get("shorturl:$shortCode");
        if ($longUrl) {
            return ['long_url' => $longUrl];
        } 
        $result =  $db->fetch("SELECT long_url FROM short_urls WHERE short_code = ?", [$shortCode]);
        if ($result) {
            $redis->setex("shorturl:$shortCode", 3600, $result['long_url']);
            return $result['long_url'];
        }

        return  $result;
    }

    private function generateShortCode() {
        return substr(md5(uniqid()), 0, 6);
    }
}