<?php
namespace Suxianjia\xianjia_short_code\Services;
defined('ROOT_PATH') or die('Missing constant ROOT_PATH.' . PHP_EOL);
// use Suxianjia\xianjia_short_code\Core\SystemConfig;
use Suxianjia\xianjia_short_code\Driver\MysqlDriver;
// use Suxianjia\xianjia_short_code\Driver\RedisDriver;
use Suxianjia\xianjia_short_code\Interface\MysqlInterface;
use Suxianjia\xianjia_short_code\Core\SystemConfig; // $config = SystemConfig::getInstance():: getModel('Database');


/**
 * MySQL 服务类
 */
class MysqlServices implements MysqlInterface {
      private static $instance;
    private $mysql;
 

    /**
     * 单例模式
     * @return MysqlServices
     */
    public static function getInstance(): MysqlServices {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    } 
    
    /**
     * 构造函数
     */
    private function __construct() {
        $Database = SystemConfig::getInstance():: getModel('Database');
        $master_config = $Database['master']; 
        $slaves_config = $Database['slaves']; 
        $this->mysql = new MysqlDriver($master_config, $slaves_config);
 
    }

    /**
     * 执行查询（读操作）
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function query(string $sql, array $params = []): array {
        return $this->mysql->query($sql, $params);
    }

    /**
     * 执行更新（写操作）
     * @param string $sql
     * @param array $params
     * @return int
     */
    public function execute(string $sql, array $params = []): int {
        return $this->mysql->execute($sql, $params);
    }

    /**
     * 获取所有记录
     * @return array
     */
    public function all(): array {
        return $this->mysql->all();
    }

    /**
     * 根据ID查询记录
     * @param int $id
     * @return array
     */
    public function find($id): array {
        return $this->mysql->find($id);
    }

    /**
     * 创建记录
     * @param array $data
     * @return int
     */
    public function create($data): int {
        return $this->mysql->create($data);
    }

    /**
     * 更新记录
     * @param int $id
     * @param array $data
     * @return int
     */
    public function update($id, $data): int {
        return $this->mysql->update($id, $data);
    }

    /**
     * 删除记录
     * @param int $id
     * @return int
     */
    public function delete($id): int {
        return $this->mysql->delete($id);
    }

    /**
     * 分页查询
     * @param string $sql
     * @param array $params
     * @param int $page
     * @param int $pageSize
     * @return array
     */
    public function paginate(string $sql, array $params = [], int $page = 1, int $pageSize = 10): array {
        return $this->mysql->paginate($sql, $params, $page, $pageSize);
    }



        public function __destruct()
    {
        // $this->mysql->close();

       
    }



}