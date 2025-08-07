<?php 
namespace Suxianjia\xianjia_short_code\Config; 
defined('ROOT_PATH') or die('Missing constant ROOT_PATH.' . PHP_EOL);
/**
 * 应用运行配置
 */
return [
    'short_code_length' => 6, // 短码长度
    'cache_ttl' => 3600, // 缓存有效期（秒）
    'max_url_length' => 2048, // URL 最大长度
    'default_redirect_code' => 302, // 默认重定向状态码
];