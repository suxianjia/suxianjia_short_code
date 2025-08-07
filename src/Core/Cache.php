<?php
namespace Suxianjia\xianjia_short_code\Core;
defined('ROOT_PATH') or die('Missing constant ROOT_PATH.' . PHP_EOL);
use \Predis\Client AS PredisClient;


class Cache {

    private static $cache_expire = 3600;

    private static $cache_prefix = 'cache_';

 


    private static $instance = null;
 

    private static $redis = null;
   private static $config = [ 
     'cache_type'  => 'session', //  session|redis|memcache|memcached|file |mysql
        'session' => [  ],
        'file' => [  
            'cache_path' =>   ''
        ],
        'redis' => [
            'host' => '',
            'port' => 0,
            'auth' => '',
            'db_select' => '',
        ] 
   ];

 

    private function __construct(array $config = []) {  
        foreach( $config as $key => $value) {  
            if (is_array($value)) {
                foreach( $value as $key2 => $value2) {
                    self::$config[$key][$key2] = $value2;
                }
            }else   {
                self::$config[$key] = $value;
            }
             
        } 
    }
  
    public static function getInstance(array $config = []): Cache { 
        if (self::$instance === null) {
            self::init();
            self::$instance = new self( $config    );
        }
        return self::$instance;
    }

    private static function init() {
        // Set default cache path if not already set
        if (empty(self::$config ['file']['cache_path']  )) {
            self::$config ['file']['cache_path'] = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;
            
            // Create cache directory if it doesn't exist
            if (!file_exists(self::$config ['file']['cache_path'] )) {
                mkdir(self::$config ['file']['cache_path'], 0777, true);
            }
        }
    }
    // redis_client  host port db_select  auth password timeout
    // 首先需要通过 composer 安装 predis/predis: composer require predis/predis
    private static function redis_client()
    {

     



        if (self::$redis === null) {
            self::$redis = new PredisClient([
                'scheme' => 'tcp',
                'host' => self::$config['redis']['host'],
                'port' => self::$config['redis']['port'],
                'password' => self::$config['redis']['auth'],
                'database' => self::$config['redis']['db_select']
            ]);
        }
        return self::$redis;
    }












    //==========set========

    public static function set($key, $value, $expire = 0) {
        
     return match (self::$config  [  'cache_type' ]) {
        'session' => self::setSession($key, $value, $expire),
        'redis'     => self::setRedis($key, $value, $expire),
        'file'       => self::setFile($key, $value, $expire),
        default     => self::setSession($key, $value, $expire),
    }; 
    }

    //setSession. $cache_prefix $cache_expire
    private static function setSession($key, $value, $expire = 0) {
        $key = self::$cache_prefix . $key;
        $_SESSION[$key] = $value;
        if ($expire > 0) {
            $_SESSION[$key . '_expire'] = time() + $expire;
        }
        return true;
        
    }
    // setRedis. $cache_expire
    private static function setRedis($key, $value, $expire = 0) {
 
        $key = self::$cache_prefix . $key;
        $value = serialize($value);
        if ($expire > 0) {
            $expire = time() + $expire;
        }
        $redis = self::redis_client();  
 
        $redis->set($key, $value);
        if ($expire > 0) {
            $redis->expire($key, $expire);
        }
    }   
//  setFile $cache_expire
    private static function setFile($key, $value, $expire = 0) {
        $key = self::$cache_prefix . $key;
        $value = serialize($value);
        if ($expire > 0) {
            $expire = time() + $expire;
        }
        $file = fopen(self::$config ['file']['cache_path'] . $key, 'w');
        fwrite($file, $value);
        fclose($file);
        if ($expire > 0) {
            touch(self::$config ['file']['cache_path'] . $key, $expire);
        }
    }
//==========get========

    // get
    public static function get($key) {
         return match ( self::$config  [  'cache_type' ] ) {
        'session' => self::getSession($key),
        'redis'     => self::getRedis($key),
        'file'       => self::getFile($key),
        default     => self::getSession($key),
    };
     }


    // getSession $cache_prefix
//getSession $cache_prefix    $cache_expire
    private static function getSession($key) {
        $key = self::$cache_prefix . $key;
        if (isset($_SESSION[$key])) {
            if (isset($_SESSION[$key . '_expire']) && $_SESSION[$key . '_expire'] > time()) {
                return $_SESSION[$key];
            }
            unset($_SESSION[$key]);
            unset($_SESSION[$key . '_expire']);
        }
        return null;
    } 


    // getFile $cache_expire
    private static function getFile($key) {
        $key = self::$cache_prefix . $key;
        if (file_exists(self::$config ['file']['cache_path'] . $key)) {
            $file = fopen(self::$config ['file']['cache_path']. $key, 'r');
            $value = fread($file, filesize(self::$config ['file']['cache_path']. $key));
            fclose($file);
            return unserialize($value);
        }
        return null;
    }
 
    // getRedis $cache_expire
    private static function getRedis($key) {
        $key = self::$cache_prefix . $key;
        $redis = self::redis_client();  
        $value = $redis->get($key);
        if ($value) {
            return unserialize($value);
        }
        return null;
    }
//==========delete========


// delete
    public static function delete($key) {
        return match (self::$config  [  'cache_type' ] ) {
            'session' => self::deleteSession($key),
            'redis'   => self::deleteRedis($key),
            'file'    => self::deleteFile($key),
            default   => self::deleteSession($key),
        };
    }



    // deleteSession. $cache_expire
    private static function deleteSession($key) {
          $key = self::$cache_prefix . $key;
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
            if (isset($_SESSION[$key . '_expire'])) {
                unset($_SESSION[$key . '_expire']);
            }
            return true;
        }
        return false;
    }

    // deleteRedis
    private static function deleteRedis($key) {
        $key = self::$cache_prefix . $key;
           $redis = self::redis_client();  
        
        return $redis->del($key);
    }

    // deleteFile
    private static function deleteFile($key) {
        $key = self::$cache_prefix . $key;
        if (file_exists(self::$config ['file']['cache_path'] . $key)) {
            return unlink(self::$config ['file']['cache_path'] . $key);
        }
        return false;
    }
//==========has========

// has
     public static function has($key): bool {
        return match (self::$config  [  'cache_type' ]) {
            'session' => self::hasSession($key),
            'redis'   => self::hasRedis($key),
            'file'    => self::hasFile($key),
            default   => self::hasSession($key),
        };
    }



    // hasSession. $cache_expire
    private static function hasSession($key): bool {
          $key = self::$cache_prefix . $key;
          if (isset($_SESSION[$key])) {
                return true;
            }
            return false; 
    }

    // hasRedis $cache_expire
    private static function hasRedis($key): bool {
        $key = self::$cache_prefix . $key;
        $redis = self::redis_client();  
        return $redis->exists($key);
    }

    // hasFile $cache_expire
    private static function hasFile($key): bool {
        $key = self::$cache_prefix . $key;
        return file_exists(self::$config ['file']['cache_path'] . $key);
    }
 


 





}
