<?php
namespace Suxianjia\xianjia_short_code\Core;

/**
 * 依赖注入服务管理类
 */
class ServiceManager {
    private static $services = [];

    public static function register($name, $service) {
        self::$services[$name] = $service;
    }

    public static function get($name) {
        if (!isset(self::$services[$name])) {
            throw new \RuntimeException("Service {$name} not found.");
        }
        return self::$services[$name];
    }
}