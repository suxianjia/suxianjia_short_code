<?php
namespace Suxianjia\xianjia_short_code\Interface;
defined('ROOT_PATH') or die('Missing constant ROOT_PATH.' . PHP_EOL);
use Suxianjia\xianjia_short_code\Core\DB;
use Suxianjia\xianjia_short_code\Services\ShortUrlService; // 本项目核心业务类 
use Suxianjia\xianjia_short_code\Driver\MysqlDriver; // 数据库 
use Suxianjia\xianjia_short_code\Driver\RedisDriver;//缓存
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