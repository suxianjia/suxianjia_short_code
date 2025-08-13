<?php
namespace Suxianjia\xianjia_short_code\Model;
defined('ROOT_PATH') or die('Missing constant ROOT_PATH.' . PHP_EOL);
use Suxianjia\xianjia_short_code\Core\DB;
use Suxianjia\xianjia_short_code\Services\ShortUrlService; // 本项目核心业务类 
// use Suxianjia\xianjia_short_code\Driver\MysqlDriver; // 数据库 
// use Suxianjia\xianjia_short_code\Driver\RedisDriver;//缓存

use Suxianjia\xianjia_short_code\Model\CommonModel;
// ShortUrlModel.php

// -- 短链接数据表结构
// CREATE TABLE IF NOT EXISTS `short_urls` (
//   `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
//   `long_url` TEXT NOT NULL,
//   `short_code` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
//   `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//   `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//   `expires_at` TIMESTAMP NULL DEFAULT NULL,
//   `hits` INT UNSIGNED DEFAULT 0,
//   `user_id` BIGINT UNSIGNED DEFAULT NULL,
//   PRIMARY KEY (`id`),
//   UNIQUE KEY `idx_short_code` (`short_code`),
//   KEY `idx_user` (`user_id`),
//   KEY `idx_expires` (`expires_at`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

// 功能 ： 增加，修改，删除，查询与列表，分页/
    // public function all();
    // public function find($id);
    // public function create($data);
    // public function update($id, $data);
    // public function delete($id);

 

class ShortUrlModel extends CommonModel {
    protected $table = 'short_urls';

    public function findByCode(string $code = ''): ?array {
// var_dump( $code);
        $conditions = [
            'short_code' => $code
        ];
        return $this->whereOne($this->table,$conditions );  
    }

    public function incrementHits(string $code): bool {
        return $this->updateByField(
            $this->table,
            ['short_code' => $code],
            ['hits' => 'hits + 1']
        );
    }

    /**
     * 插入数据
     * @param array $data 数据数组
     * @return bool 是否成功
     */
    public function insertOne(array $data = []): bool {
        return $this->insert($this->table, $data);
    }
}
