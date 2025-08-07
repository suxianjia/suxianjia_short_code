<?php
namespace Suxianjia\xianjia_short_code\Core;

use Suxianjia\xianjia_short_code\Driver\MysqlDriver;
use Suxianjia\xianjia_short_code\Driver\MysqlInterface;

use PDO;
use PDOException;

/**
 * MySQL PDO 数据库类
 */
class DB implements MysqlInterface {
    private static $instance = null;
    private $connection;

    /**
     * 初始化数据库连接
     * @param array $config 数据库配置
     */
    private function __construct($config) {
        try {
            $this->connection = new MysqlDriver($config);
        } catch (\Exception $e) {
            throw new \RuntimeException("数据库连接失败: " . $e->getMessage());
        }
    }

    /**
     * 获取数据库实例
     * @param array $config 数据库配置
     * @return DB
     */
    public static function getInstance($config) {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    /**
     * 执行查询
     * @param string $sql SQL 语句
     * @param array $params 参数
     * @return \PDOStatement
     */
    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * 获取单行结果
     * @param string $sql SQL 语句
     * @param array $params 参数
     * @return array|null
     */
    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 获取所有结果
     * @param string $sql SQL 语句
     * @param array $params 参数
     * @return array
     */
    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 插入数据
     * @param string $table 表名
     * @param array $data 数据
     * @return int 插入的 ID
     */
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, array_values($data));
        return $this->connection->lastInsertId();
    }
}