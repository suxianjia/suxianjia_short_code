<?php
namespace Suxianjia\xianjia_short_code\Validator;

/**
 * 验证器管理类
 */
class ValidatorManager {
    private $validators = [];

    /**
     * 添加验证器
     * @param string $name 验证器名称
     * @param callable $validator 验证器方法
     */
    public function add($name, callable $validator) {
        $this->validators[$name] = $validator;
    }

    /**
     * 验证数据
     * @param string $name 验证器名称
     * @param mixed $value 验证值
     * @return bool
     */
    public function validate($name, $value) {
        if (!isset($this->validators[$name])) {
            throw new \RuntimeException("Validator {$name} not found.");
        }
        return $this->validators[$name]($value);
    }
}