<?php
namespace Suxianjia\xianjia_short_code\Util;

/**
 * 单例模式实现类
 */
trait Singleton {
    private static $instance;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}