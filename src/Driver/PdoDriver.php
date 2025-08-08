<?php
namespace Suxianjia\xianjia_short_code\Driver;
use Suxianjia\xianjia_short_code\Interface\DBInterface;
 
use PDO;
use PDOException;

/**
 * MySQL 驱动类（支持主从配置）
 */
class PdoDriver implements DBInterface {
       private  $type = 'mysql';
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
    public function all() {
           $args = func_get_args();
           $tableName = $args[0] ?? '';
           $this->useSlave();
        $sql = "SELECT * FROM {$tableName}";
        return $this->currentConnection->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find() {
           $args = func_get_args();
           $tableName = $args[0] ?? '';
           $id = $args[1] ?? null;
           $this->useSlave();
        $sql = "SELECT * FROM {$tableName} WHERE id = :id";
        $stmt = $this->currentConnection->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
             $args = func_get_args();
             $tableName = $args[0] ?? '';
             $data = $args[1] ?? [];
             $this->useMaster();
        $columns = implode(', ', array_keys($data));
        $values = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$tableName} ($columns) VALUES ($values)";
        $stmt = $this->currentConnection->prepare($sql);
        $stmt->execute($data);
        return $this->currentConnection->lastInsertId();
    }

    public function update() {
             $args = func_get_args();
             $tableName = $args[0] ?? '';
             $id = $args[1] ?? null;
             $data = $args[2] ?? [];
             $this->useMaster();
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "$key = :$key";
        }
        $set = implode(', ', $set);
        $sql = "UPDATE {$tableName} SET $set WHERE id = :id";
        $data['id'] = $id;
        $stmt = $this->currentConnection->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete() {
             $args = func_get_args();
             $tableName = $args[0] ?? '';
             $id = $args[1] ?? null;
             $this->useMaster();
        $sql = "DELETE FROM {$tableName} WHERE id = :id";
        $stmt = $this->currentConnection->prepare($sql);
        return $stmt->execute(['id' => $id]);
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
    public function query() {
        $args = func_get_args();
        $sql = $args[0] ?? '';
        $params = $args[1] ?? [];
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
    public function execute() {
        $args = func_get_args();
        $sql = $args[0] ?? '';
        $params = $args[1] ?? [];
        $this->useMaster();
        $stmt = $this->currentConnection->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
}