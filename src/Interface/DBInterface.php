<?php
namespace Suxianjia\xianjia_short_code\Interface;
defined('ROOT_PATH') or die('Missing constant ROOT_PATH.' . PHP_EOL);

// MysqlInterface
// MySQL 驱动接口 Suxianjia\xianjia_short_code\Interface\MysqlInterface
/**
 * MySQL 驱动接口
 */
interface DBInterface {

public function query();
public function execute();
public function all();
public function find();
public function create();
public function update();
public function delete();



}