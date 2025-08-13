<?php
 
define('ROOT_PATH', dirname( __DIR__ )  );
date_default_timezone_set('PRC'); // 设置为中国时区
// 引入应用入口文件
require_once ROOT_PATH . '/src/core.php';
$app = new \Suxianjia\xianjia_short_code\core();
$app->run();