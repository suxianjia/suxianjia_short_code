<?php

namespace Suxianjia\xianjia_short_code\Core;
// use Suxianjia\xianjia_short_code\Core\Config; // $config = Config::getInstance():: getModel('Database');

// ROOT_PATH
use Exception;
class SystemConfig {
    private static $instance;
    private static $config = [];

    private function __construct() {
 
        $this->loadAppConfig();
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
 

    private function loadAppConfig() {
        // 加载其他配置文件  ROOT_PATH.'/config/*.php'
        foreach (glob(ROOT_PATH . '/config/*.php') as $configFile) {
            // 文件名  /config/*.php 去掉目录与 .php
            $key = basename($configFile, '.php');
           self::$config[$key] = include $configFile;
        }
         
        // ROOT_PATH
    }
// getModel
public static function getModel (string $Model= '' ,array $default = []){
        return self::$config[$Model]; 
}

    public  static function get($Model= '',$key= '',  $default = '') {
        return self::$config[$Model][$key] ?? $default;
    }

    public static  function all(): array {
        return self::$config;
    }

}