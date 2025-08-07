<?php
namespace Suxianjia\xianjia_short_code\Core;

/**
 * JSON 解析类
 */
class JsonDecoder {
    public static function decode($json, $assoc = true) {
        return json_decode($json, $assoc);
    }

    public static function encode($data) {
        return json_encode($data);
    }
}