<?php 
namespace Suxianjia\xianjia_short_code\Config; 
defined('ROOT_PATH') or die('Missing constant ROOT_PATH.' . PHP_EOL);
use Suxianjia\xianjia_short_code\Core\SystemConfig; // SystemConfig::getInstance()::getModel('Routes');
// Suxianjia\xianjia_short_code\Config\RouteConfig::getRoutes()
class RouteConfig 
{
    private static $routes = [
        'GET' => [
            '/' => 'HomeController@index',
            '/shorten' => 'ShortUrlController@shorten',
            '/{code}' => 'ShortUrlController@redirect'
        ],
        'POST' => [
            '/api/shorten' => 'ShortUrlController@shorten'
        ]
    ];

    public static function getRoutes()
    {
        $routesConfig = SystemConfig::getInstance()::getModel('Routes');
        return array_merge_recursive(self::$routes, $routesConfig);
    }
}
