<?php
namespace Suxianjia\xianjia_short_code\Server;
defined('ROOT_PATH') or die('Missing constant ROOT_PATH.' . PHP_EOL);
/**
 * 路由类
 */
class RouteServer {
    private $routes = [];

    /**
     * 添加路由
     * @param string $method HTTP 方法
     * @param string $path 路径
     * @param callable $handler 处理器
     */
    public function add($method, $path, $handler) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        ];
    }

    /**
     * 匹配路由
     * @param string $method HTTP 方法
     * @param string $path 路径
     * @return array|null
     */
    public function match($method, $path) {
        foreach ($this->routes as $route) {
            if ($route['method'] === strtoupper($method) && $route['path'] === $path) {
                return $route;
            }
        }
        return null;
    }
}