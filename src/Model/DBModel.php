<?php
namespace Suxianjia\xianjia_short_code\Model;

use Suxianjia\xianjia_short_code\Core\DB;

/**
 * 数据库模型基类
 */
abstract class DBModel {
    protected $table;
    protected $db;

    public function __construct() {
        $this->db = DB::getInstance([
            'host' => getenv('DB_HOST'),
            'database' => getenv('DB_DATABASE'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD')
        ]);
    }

    /**
     * 获取所有记录
     * @return array
     */
    public function all() {
        return $this->db->fetchAll("SELECT * FROM {$this->table}");
    }

    /**
     * 根据 ID 获取记录
     * @param int $id 记录 ID
     * @return array|null
     */
    public function find($id) {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    /**
     * 创建记录
     * @param array $data 数据
     * @return int 插入的 ID
     */
    public function create($data) {
        return $this->db->insert($this->table, $data);
    }
}