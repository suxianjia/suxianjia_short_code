<?php
namespace Suxianjia\xianjia_short_code\Interface;
defined('ROOT_PATH') or die('Missing constant ROOT_PATH.' . PHP_EOL);
/**
 * 模型层接口
 */
interface ModelInterface {
    public function all();
    public function find($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
}