<?php
 
define('ROOT_PATH', dirname( __DIR__ )  );

// 引入应用入口文件
require_once ROOT_PATH . '/src/core.php';
$app = new \Suxianjia\xianjia_short_code\core();
$app->run();