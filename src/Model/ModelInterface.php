<?php
namespace Suxianjia\xianjia_short_code\Model;

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