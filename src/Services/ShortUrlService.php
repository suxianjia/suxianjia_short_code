<?php
namespace Suxianjia\xianjia_short_code\Services;
use Suxianjia\xianjia_short_code\Core\SystemConfig; // $config = SystemConfig::getInstance():: getModel('Database');
use Suxianjia\xianjia_short_code\Model\ShortUrlModel;
// use Suxianjia\xianjia_short_code\Services\MysqlServices; //      MysqlServices::getInstance()
// use Suxianjia\xianjia_short_code\Services\RedisServices; //        RedisServices::getInstance()

// use   Suxianjia\xianjia_short_code\Interface\DBInterface; // 数据库模型-接口类 

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
    private static  $tableName = 'short_urls';
      private static  $expire = 3600;
   private static $result = [
                'code' => 500,
                'message' => 'error',
                'data' =>[]
        ];
  
    private static $instance;


    public static function setresult($code = 500,string $message = 'error',array $data =[] ,array $adddata =[]): array
    {
        self::$result ['code'] = $code; 
        self::$result ['data'] =    $data ; 
        foreach ($adddata as $key => $value) {
            self::$result ['data'][$key ] = $value;
        }  
        self::$result ['message'] =  $message;
        return self::$result;
    }
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
     * 生成短码
     * @param string $long_url 原始URL
     * @return array|false 返回短码和原始URL，失败返回false
     */
    
    /**
     * 生成短码
     * @param string $long_url 原始URL
     * @return array|false 返回短码和原始URL，失败返回false
     */
    public static function create_code(string $long_url = '')  :string |array
    { 
        if ( $long_url =='') {
             return   self::setresult (500,' long_url  is empty ', [] );
        }
      
        $cache_key = "shorturl:". md5( $long_url );
        $result  =   RedisServices::getInstance()::$obj->get($cache_key);
        // var_dump(       $result    ); exit;
        if ($result) { 
            $data = json_decode( $result , true);
            $data['expires_at'] = date('Y-m-d H:i:s', time() + self::$expire);// 3600); //
            RedisServices::getInstance()::$obj->set($cache_key,json_encode($data), self::$expire );//   
            // RedisServices::getInstance()::$obj->setttl($cache_key, 3600);//   
            // return   self::setresult (200,'redis cache Success', json_decode( $result , true) ); 
            return   self::setresult (200,'redis cache Success', $data ); 
        } 
        $Model= new ShortUrlModel();
        $result =   $Model->findByLong_url($long_url); 
        if ($result) {
            $result['expires_at'] = date('Y-m-d H:i:s', time() + self::$expire); //
            RedisServices::getInstance()::$obj->set($cache_key,json_encode ($result ), self::$expire );//   
            return   self::setresult (200,'db cache Success', $result); 
        } 
        $data = [];
        $data['long_url' ] = $long_url;
        $data['short_code' ] = self::generateShortCode();  
        $data['expires_at'] = date('Y-m-d H:i:s', time() + self::$expire);  
        $result =  $Model->createOne($data );
        if (!$result) { 
            return   self::setresult (500,'db create error', $data ); 
        } 
        RedisServices::getInstance()::$obj->set($cache_key,json_encode($data), self::$expire);//   
        return   self::setresult (200,'db create Success',$data, ['result' => $result]);  
    }

    /**
     * 缩短 URL
     * @param string $longUrl 原始 URL
     * @return array 包含短码的数组
     */
    public static function shorten($longUrl) {
        $cache_key = "shorturl:". md5( $longUrl ); 

         $shortCode =  self::generateShortCode();
        // $db = MysqlServices::getInstance();

              $Model= new ShortUrlModel;

        $redis = RedisServices::getInstance();
        $Model->insert( self::$tableName, [
            'long_url' => $longUrl,
            'short_code' => $shortCode
        ]);
        $redis->setex( $cache_key , self::$expire, $longUrl);
        return ['short_code' => $shortCode];
    }

    /**
     * 获取原始 URL
     * @param string $shortCode 短码
     * @return array 包含原始 URL 的数组
     */
    public static function getOriginalUrl( string $shortCode ='') {

        if ( $shortCode =='') {
             return   self::setresult (500,' shortCode  is empty ', [] );
        }
        $Model= new ShortUrlModel ();
         $cache_key = "shortCode:".   $shortCode  ;

         $result  =   RedisServices::getInstance()::$obj->get($cache_key);
         
        if ($result) { 
                $data = json_decode( $result , true);
                $data['expires_at'] = date('Y-m-d H:i:s', time() + self::$expire); //
                RedisServices::getInstance()::$obj->set($cache_key,json_encode ($data ), self::$expire );// 
                $Model->incrementHits($shortCode );
                
                return   self::setresult (200,'redis   Success', $data     );  
        } 
   
        
         $result = $Model->findByCode($shortCode); 
        if ($result) { 
                $data =  $result  ;
                $data['expires_at'] = date('Y-m-d H:i:s', time() + self::$expire); 
                RedisServices::getInstance()::$obj->set($cache_key,json_encode ($data ), self::$expire );
                    $Model->incrementHits($shortCode );
                return   self::setresult (200,'db   Success',$data   );  
        }
        // echo $shortCode;

         return   self::setresult (400,'shortCode 短码    not found',['findByCode'=>$result]   );  
    }
/**
 *  生成 short_code 
 * short_code
 * 
 * */
    private  static function generateShortCode() {
        return substr(md5(uniqid()), 0, 10);
    }
}