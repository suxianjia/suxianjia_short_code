<?php
namespace Suxianjia\xianjia_short_code\Util;

/**
 * Token 类
 */
class Token {
    public static function generate($length = 32) {
        return bin2hex(random_bytes($length));
    }
}