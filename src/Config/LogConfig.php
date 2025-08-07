<?php 
namespace Suxianjia\xianjia_short_code\Config; 
defined('ROOT_PATH') or die('Missing constant ROOT_PATH.' . PHP_EOL);
/**
 * 日志配置类
 */
class LogConfig {
    public static function getConfig() {
        return [
            'level' => getenv('LOG_LEVEL') ?: 'info',
            'path' => getenv('LOG_PATH') ?: __DIR__ . '/../../log',
            'filename' => getenv('LOG_FILENAME') ?: 'app.log'
        ];
    }
}