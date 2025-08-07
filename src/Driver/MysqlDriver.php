<?php
namespace Suxianjia\xianjia_short_code\Driver;

use PDO;
use PDOException;

/**
 * MySQL 驱动类（支持主从配置）
 */
class MysqlDriver implements MysqlInterface {
    private $master;
    private $slaves = [];
    private $currentConnection;

    /**
     * 初始化主从配置
     * @param array $masterConfig 主库配置
     * @param array $slaveConfigs 从库配置列表
     */
    public function __construct(array $masterConfig, array $slaveConfigs = []) {
        $this->master = $this->createConnection($masterConfig);
        foreach ($slaveConfigs as $config) {
            $this->slaves[] = $this->createConnection($config);
        }
        $this->currentConnection = $this->master;
    }

    /**
     * 创建数据库连接
     * @param array $config 数据库配置
     * @return PDO
     * @throws PDOException
     */
    private function createConnection(array $config) {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['port'] ?? 3306,
            $config['database'],
            $config['charset'] ?? 'utf8mb4'
        );
        return new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    /**
     * 切换到从库
     */
    public function useSlave() {
        if (!empty($this->slaves)) {
            $this->currentConnection = $this->slaves[array_rand($this->slaves)];
        }
    }

    /**
     * 切换到主库
     */
    public function useMaster() {
        $this->currentConnection = $this->master;
    }

    /**
     * 执行查询（读操作默认使用从库）
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function query(string $sql, array $params = []): array {
        $this->useSlave();
        $stmt = $this->currentConnection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * 执行更新（写操作强制使用主库）
     * @param string $sql
     * @param array $params
     * @return int 受影响的行数
     */
    public function execute(string $sql, array $params = []): int {
        $this->useMaster();
        $stmt = $this->currentConnection->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
}