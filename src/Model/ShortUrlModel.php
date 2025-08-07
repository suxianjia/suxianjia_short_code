<?php
namespace Suxianjia\xianjia_short_code\Model;
defined('ROOT_PATH') or die('Missing constant ROOT_PATH.' . PHP_EOL);
use Suxianjia\xianjia_short_code\Core\DB;
use Suxianjia\xianjia_short_code\Services\ShortUrlService; // 本项目核心业务类 
use Suxianjia\xianjia_short_code\Driver\MysqlDriver; // 数据库 
use Suxianjia\xianjia_short_code\Driver\RedisDriver;//缓存

use   Suxianjia\xianjia_short_code\Interface\ModelInterface; // 数据库模型-接口类 
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

 

class ShortUrlModel implements ModelInterface {
    protected $table = 'short_urls';
    protected $db;
    protected $cache;

    public function __construct() {
        $this->db = DB::connection('mysql');
        $this->cache = DB::connection('redis');
    }

    public function all(int $page = 1, int $perPage = 15): array {
        $offset = ($page - 1) * $perPage;
        $query = "SELECT * FROM {$this->table} LIMIT {$perPage} OFFSET {$offset}";
        return $this->db->query($query)->fetchAll();
    }

    public function find($id): ?array {
        $cacheKey = "short_url:{$id}";
        if ($data = $this->cache->get($cacheKey)) {
            return json_decode($data, true);
        }

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();

        if ($result) {
            $this->cache->set($cacheKey, json_encode($result), 3600);
        }

        return $result ?: null;
    }

    public function findByCode(string $code): ?array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE short_code = ?");
        $stmt->execute([$code]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $fields = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} ({$fields}) VALUES ({$placeholders})"
        );
        $stmt->execute(array_values($data));

        return $this->db->lastInsertId();
    }

    public function update($id, array $data): bool {
        $setClause = implode('=?,', array_keys($data)) . '=?';
        
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET {$setClause} WHERE id = ?"
        );
        
        $values = array_values($data);
        $values[] = $id;
        
        $this->cache->delete("short_url:{$id}");
        return $stmt->execute($values);
    }

    public function delete($id): bool {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $this->cache->delete("short_url:{$id}");
        return $stmt->execute([$id]);
    }

    public function incrementHits(string $code): bool {
        return $this->db->exec(
            "UPDATE {$this->table} SET hits = hits + 1 WHERE short_code = '{$code}'"
        ) > 0;
    }
}
