<?php
namespace Suxianjia\xianjia_short_code\Model;
// use   Suxianjia\xianjia_short_code\Interface\DBInterface; // 数据库模型-接口类 
use Suxianjia\xianjia_short_code\Services\MysqlServices; // 数据库 实例化好了 直接使用 
/**
 * 模型层接口
 */
class CommonModel  extends MysqlServices {
    public function all(string $tableName = '' ) {
 
        return MysqlServices::getInstance()::$obj->query("SELECT * FROM {$tableName}");
    }

    public function find(string $tableName = '' , int  $id = 0 ) {
   
        return MysqlServices::getInstance()::$obj->query("SELECT * FROM {$tableName} WHERE id = ?", [$id]);
    }
 



    public function create(string $tableName= '' , array $data = []) {
     
        $fields = implode(', ', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        return MysqlServices::getInstance()::$obj->execute("INSERT INTO {$tableName} ($fields) VALUES ($values)", array_values($data));
    }

    public function update(string $tableName= '' , int  $id = 0, array $data = [] ) {
      
        $setClause = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data)));
        $params = array_merge(array_values($data), [$id]);
        return MysqlServices::getInstance()::$obj->execute("UPDATE {$tableName} SET $setClause WHERE id = ?", $params);
    }

    public function delete(string $tableName= ''  , int  $id = 0 ) {
 
        return MysqlServices::getInstance()::$obj->execute("DELETE FROM {$tableName} WHERE id = ?", [$id]);
    }

    /**
     * 分页查询
     * @param string $tableName 表名
     * @param int $page 当前页码
     * @param int $perPage 每页记录数
     * @return array
     */
    public function paginate(string $tableName = '' ,int  $page = 1, int $perPage = 15) {
    
        $offset = ($page - 1) * $perPage;
        return MysqlServices::getInstance()::$obj->query("SELECT * FROM {$tableName} LIMIT ? OFFSET ?", [$perPage, $offset]);
    }

    /**
     * 条件查询
     * @param string $tableName 表名
     * @param array $conditions 条件数组，如 ['name' => 'John', 'status' => 1]
     * @return array
     */
    public function where(string $tableName = '' , array  $conditions= []) { 
        $whereClause = implode(' AND ', array_map(fn($k) => "$k = ?", array_keys($conditions)));
        return MysqlServices::getInstance()::$obj->query("SELECT * FROM {$tableName} WHERE $whereClause", array_values($conditions));
    }


    /**
     * 条件查询
     * @param string $tableName 表名
     * @param array $conditions 条件数组，如 ['name' => 'John', 'status' => 1]
     * @return array
     */
    public function whereOne(string $tableName = '' , array  $conditions= []) { 
        $whereClause = implode(' AND ', array_map(fn($k) => "$k = ?", array_keys($conditions)));
        $res =  MysqlServices::getInstance()::$obj->query("SELECT * FROM {$tableName} WHERE $whereClause", array_values($conditions));
        if ( $res ) {
            return $res[0];
        }
    }







}