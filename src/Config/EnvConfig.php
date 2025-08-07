<?php 
namespace Suxianjia\xianjia_short_code\Config; 
defined('ROOT_PATH') or die('Missing constant ROOT_PATH.' . PHP_EOL);
/**
 * 环境配置类
 */
class EnvConfig {
    public static function get($key, $default = null) {
        return getenv($key) ?: $default;
    }
}