<?php 
namespace Suxianjia\xianjia_short_code\Config; 
defined('ROOT_PATH') or die('Missing constant ROOT_PATH.' . PHP_EOL);
use Suxianjia\xianjia_short_code\Core\SystemConfig; // SystemConfig::getInstance()::getModel('Routes');
// Suxianjia\xianjia_short_code\Config\RouteConfig::getRoutes()
class RouteConfig 
{
    private static $routes = [
        'GET' => [
               '/index' => 'HomeController@index',
            // '/' => 'HomeController@index',
            // '/shorten' => 'ShortUrlController@shorten',
            // '/{code}' => 'ShortUrlController@redirect'
        ],
        'POST' => [
            // '/api/shorten' => 'ShortUrlController@shorten'
        ]
    ];

    public static function getRoutes()
    {
        $routesConfig = SystemConfig::getInstance()::getModel('Routes'); 
        foreach ($routesConfig['routes'] AS $key => $value){ 
            match ($value[0] ) {
                'GET'  => self::$routes['GET'][$value[1]] =  $value[2] ,
                'POST'  => self::$routes['POST'][$value[1]] =  $value[2] ,
                'PUT'  => self::$routes['PUT'][$value[1]] =  $value[2] ,
                'DELETE'  => self::$routes['DELETE'][$value[1]] =  $value[2] ,
                default => null,
            }; 

        } 
        return  self::$routes;
    }
}
