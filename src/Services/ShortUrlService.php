<?php
namespace Suxianjia\xianjia_short_code\Services;
use Suxianjia\xianjia_short_code\Core\SystemConfig; // $config = SystemConfig::getInstance():: getModel('Database');
 use Suxianjia\xianjia_short_code\Model\ShortUrlModel;
use Suxianjia\xianjia_short_code\Services\MysqlServices; //      MysqlServices::getInstance()
use Suxianjia\xianjia_short_code\Services\RedisServices; //        RedisServices::getInstance()

use   Suxianjia\xianjia_short_code\Interface\DBInterface; // 数据库模型-接口类 

 /**
  * 
  * 
  * 
  * 
  -- 短链接数据表结构
CREATE TABLE IF NOT EXISTS `short_urls` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `long_url` TEXT NOT NULL,
  `short_code` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `expires_at` TIMESTAMP NULL DEFAULT NULL,
  `hits` INT UNSIGNED DEFAULT 0,
  `user_id` BIGINT UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_short_code` (`short_code`),
  KEY `idx_user` (`user_id`),
  KEY `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
  * 
  * 
  * */

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
    public static function shorten($longUrl) {
        $shortCode = $this->generateShortCode();
        // $db = MysqlServices::getInstance();

              $Model= new ShortUrlModel;

        $redis = RedisServices::getInstance();
        $Model->insert('short_urls', [
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
    public static function getOriginalUrl($shortCode) {
  

        $longUrl =   RedisServices::getInstance()::$obj->get("shorturl:$shortCode");
        if ($longUrl) {
            return ['long_url' => $longUrl];
        } 
   
        $Model= new ShortUrlModel ();
         $result = $Model->findByCode($shortCode); 
        if ($result) {
 
            RedisServices::getInstance()::$obj->setex("shorturl:$shortCode", 3600, $result['long_url']);
            return $result['long_url'];
        }

        return  $result;
    }
/**
 *  生成 short_code 
 * short_code
 * 
 * */
    private  static function generateShortCode() {
        return substr(md5(uniqid()), 0, 8);
    }
}