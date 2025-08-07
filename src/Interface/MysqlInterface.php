<?php
namespace Suxianjia\xianjia_short_code\Interface;
defined('ROOT_PATH') or die('Missing constant ROOT_PATH.' . PHP_EOL);

// MysqlInterface
// MySQL 驱动接口 Suxianjia\xianjia_short_code\Interface\MysqlInterface
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


        public function all();
    public function find($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);



}