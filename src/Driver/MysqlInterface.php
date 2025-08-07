<?php
namespace Suxianjia\xianjia_short_code\Driver;

/**
 * MySQL 驱动接口
 */
interface MysqlInterface {
    /**
     * 执行查询（读操作）
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function query(string $sql, array $params = []): array;

    /**
     * 执行更新（写操作）
     * @param string $sql
     * @param array $params
     * @return int 受影响的行数
     */
    public function execute(string $sql, array $params = []): int;
}