<?php 
namespace Suxianjia\xianjia_short_code\Config; 
defined('ROOT_PATH') or die('Missing constant ROOT_PATH.' . PHP_EOL);
/**
 * API 错误码定义
 */
return [
    // 通用错误
    10000 => '未知错误',
    10001 => '参数错误',
    10002 => '数据库错误',
    10003 => 'Redis 错误',

    // 业务错误
    20000 => 'URL 格式错误',
    20001 => '短码已存在',
    20002 => '短码不存在',
];