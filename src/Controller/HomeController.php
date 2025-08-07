<?php 
 
namespace Suxianjia\xianjia_short_code\Controller;
defined('ROOT_PATH') or die('Missing constant ROOT_PATH.' . PHP_EOL);
use Suxianjia\xianjia_short_code\Core\SystemConfig; // SystemConfig::getInstance()::getModel('Routes');
 

use Suxianjia\xianjia_short_code\Services\ShortUrlService; // 本项目核心业务类 
use Suxianjia\xianjia_short_code\Services\MysqlServices; //      MysqlServices::getInstance()
use Suxianjia\xianjia_short_code\Services\RedisServices; //        RedisServices::getInstance()

use  Suxianjia\xianjia_short_code\Core\Request; //用户请求类
use Suxianjia\xianjia_short_code\Core\Response;// 接口返回类 

class HomeController {
	// src/Controller/ 目录的所有控制器文件 
	// src/Controller/HomeController.php
	// HomeController.php 所有的 Controller.php 文件 必须按这个格式编写 
	// function 方法 输入类 /输入类 
	public function index (Request $request, Response $response) {
		$SystemConfig = SystemConfig::getInstance(); //系统配置
		$ShortUrlService =   ShortUrlService::getInstance();// Short  UrlS
		$db =MysqlServices::getInstance();// 数据库   		
		$redis =RedisServices::getInstance();  //缓存
		$response->success([],"hello index!");
	 
	}
//src/Controller/ 目录的所有控制器文件，所有的 Controller.php 文件 必须按这个格式编写 

}