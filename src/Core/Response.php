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
            'message' => $message
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
}
