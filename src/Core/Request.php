<?php
namespace Suxianjia\xianjia_short_code\Core;
defined('ROOT_PATH') or die('Missing constant ROOT_PATH.' . PHP_EOL);

// Request对象实现 接收用户输入类  在PHP中接收用户输入数据主要通过超全局变量和Request对象实现
 // use  Suxianjia\xianjia_short_code\Core\Request

class Request {
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    public static function all(): array {
        $data = array_merge(
            $_GET,
            $_POST,
            self::parseBodyParams(),
            self::getJsonInput()
        );
        return self::filterXSS($data);
    }

    public static function get(string $key, $default = null) {
        $data = self::all();
        return self::getNestedValue($data, $key, $default);
    }

    public static function json(): ?array {
        return self::getJsonInput();
    }

    public static function method(): string {
        $method = $_SERVER['REQUEST_METHOD'] ?? self::METHOD_GET;
        if ($method === 'POST' && isset($_SERVER['HTTP_X_HTTP_METHOD'])) {
            $override = strtoupper($_SERVER['HTTP_X_HTTP_METHOD']);
            if (in_array($override, [self::METHOD_PUT, self::METHOD_DELETE])) {
                $method = $override;
            }
        }
        return $method;
    }

    private static function parseBodyParams(): array {
        if (!in_array(self::method(), [self::METHOD_PUT, self::METHOD_DELETE])) {
            return [];
        }
        parse_str(file_get_contents('php://input'), $params);
        return $params ?: [];
    }

    private static function getJsonInput(): array {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            $input = file_get_contents('php://input');
            return json_decode($input, true) ?? [];
        }
        return [];
    }

    private static function getNestedValue(array $data, string $key, $default) {
        foreach (explode('.', $key) as $segment) {
            if (!isset($data[$segment])) {
                return $default;
            }
            $data = $data[$segment];
        }
        return $data;
    }

    private static function filterXSS($data) {
        if (is_array($data)) {
            return array_map([self::class, 'filterXSS'], $data);
        }
        return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
