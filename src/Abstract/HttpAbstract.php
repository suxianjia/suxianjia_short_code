<?php
namespace Suxianjia\xianjia_short_code\Abstract;
defined('ROOT_PATH') or die('Missing constant ROOT_PATH.' . PHP_EOL);
/**
 * HTTP 控制器基类
 */
abstract class HttpAbstract{
    /**
     * 处理请求
     * @param array $request 请求数据
     * @return array
     */
    abstract public function handle($request);

    /**
     * 返回 JSON 响应
     * @param array $data 数据
     * @param int $status HTTP 状态码
     * @return string
     */
    protected function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        return json_encode($data);
    }
}