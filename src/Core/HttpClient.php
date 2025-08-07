<?php
namespace Suxianjia\xianjia_short_code\Core;

use InvalidArgumentException;

/**
 * 远程 HTTP 请求类
 */
class HttpClient {
    /**
     * 发送 GET 请求
     * @param string $url 请求 URL
     * @param array $headers 请求头
     * @return array
     */
    public function get($url, $headers = []) {
        return $this->request('GET', $url, [], $headers);
    }

    /**
     * 发送 POST 请求
     * @param string $url 请求 URL
     * @param array $data 请求数据
     * @param array $headers 请求头
     * @return array
     */
    public function post($url, $data = [], $headers = []) {
        return $this->request('POST', $url, $data, $headers);
    }

    /**
     * 发送 HTTP 请求
     * @param string $method 请求方法
     * @param string $url 请求 URL
     * @param array $data 请求数据
     * @param array $headers 请求头
     * @return array
     */
    private function request($method, $url, $data = [], $headers = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $headers[] = 'Content-Type: application/json';
        }

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status' => $httpCode,
            'data' => json_decode($response, true)
        ];
    }
}