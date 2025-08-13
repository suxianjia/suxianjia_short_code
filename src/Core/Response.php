<?php 
namespace Suxianjia\xianjia_short_code\Core;
defined('ROOT_PATH') or die('Missing constant ROOT_PATH.' . PHP_EOL);
//Response.php
//use Suxianjia\xianjia_short_code\Core\Response
class Response {
    public static function success($data, $message = '') {
        self::output([
            'code' => 200,
            'message' => $message,
            'data' => $data
        ]);
    }

    public static function error($message, $code = 400) {
        self::output([
            'code' => $code,
            'message' => $message,
            'data' => []
        ], $code);
    }

    private static function output($data, $statusCode = 200) {
        header('Content-Type: application/json', true, $statusCode);
        $json = json_encode($data);
        
        if ($json === false) {
            self::handleJsonError();
        }
        
        echo $json;
        exit;
    }

    private static function handleJsonError() {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode([
            'code' => 500,
            'message' => 'JSON encoding error: ' . json_last_error_msg()
        ]);
        exit;
    }

    //  增加 redirect 方法   // 4. 执行301永久重定向
    // return $response->redirect($longUrl, 301);

    /**
     * 执行URL重定向
     * @param string $url 目标URL
     * @param int $statusCode 重定向状态码(默认301)
     * @param bool $validate 是否验证URL有效性
     */
    public static function redirect($url, $statusCode = 301, $validate = true) {
        if ($validate && !filter_var($url, FILTER_VALIDATE_URL)) {
            self::error('Invalid redirect URL', 400);
        }

        header("HTTP/1.1 {$statusCode} " . self::getStatusText($statusCode));
        header("Location: {$url}");
        exit;
    }

    /**
     * 获取HTTP状态文本
     * @param int $code 状态码
     * @return string
     */
    private static function getStatusText($code) {
        $statusTexts = [
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect'
        ];
        return $statusTexts[$code] ?? 'Unknown Status';
    }
    
}
