<?php

// 定义根目录常量
define('ROOT_PATH', dirname(__DIR__));

// 引入应用入口文件
// require_once ROOT_PATH . '/src/core.php';
require_once ROOT_PATH . '/vendor/autoload.php';
// 扫描代码  整个项目中 所有文件的，大约 头部的  第二三行左右， 都 要增加  ROOT_PATH 常量的定义 是否有定义。  代码片段为：  !defined( 'ROOT_PATH') or die('Missing constant ROOT_PATH.' . PHP_EOL);

// 初始化应用
$app = new \Suxianjia\xianjia_short_code\core();
$app->run();