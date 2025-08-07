<?php
namespace Suxianjia\xianjia_short_code\Factory;

/**
 * 服务工厂容器类
 */
class ContainerFactory {
    private $services = [];

    /**
     * 注册服务
     * @param string $name 服务名称
     * @param callable $factory 工厂方法
     */
    public function register($name, callable $factory) {
        $this->services[$name] = $factory;
    }

    /**
     * 获取服务
     * @param string $name 服务名称
     * @return mixed
     */
    public function get($name) {
        if (!isset($this->services[$name])) {
            throw new \RuntimeException("Service {$name} not found.");
        }
        return $this->services[$name]();
    }
}