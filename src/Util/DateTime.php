<?php
namespace Suxianjia\xianjia_short_code\Util;

/**
 * 日期类
 */
class DateTime {
    /**
     * 获取当前时间
     * @return string
     */
    public static function now() {
        return date('Y-m-d H:i:s');
    }

    /**
     * 格式化时间
     * @param string $format 格式
     * @param int|null $timestamp 时间戳
     * @return string
     */
    public static function format($format = 'Y-m-d H:i:s', $timestamp = null) {
        return date($format, $timestamp ?: time());
    }

    /**
     * 计算时间差
     * @param string $start 开始时间
     * @param string $end 结束时间
     * @return int 秒数
     */
    public static function diff($start, $end) {
        return strtotime($end) - strtotime($start);
    }
}