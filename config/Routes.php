<?php

use Suxianjia\xianjia_short_code\Core\Env;
//   config/Routes.php
return [
    'path' => 'config/Routes.php',
    'Routes_version' => '1.0',
    'routes' => [
            // HomeController
            ['GET', '/', 'HomeController@index'],
            // ShortUrlController
            ['GET','/{code}' , 'ShortUrlController@redirect'],
            ['GET', '/short-url', 'ShortUrlController@index'],
            ['GET', '/short-url/all', 'ShortUrlController@all'],
            ['GET', '/short-url/find', 'ShortUrlController@find'],
            ['POST', '/short-url/create', 'ShortUrlController@create'],
            ['POST', '/short-url/create_code', 'ShortUrlController@create_code'],
            ['PUT', '/short-url/update', 'ShortUrlController@update'],
            ['DELETE', '/short-url/delete', 'ShortUrlController@delete'],
            ['GET', '/short-url/redirect', 'ShortUrlController@redirect'],
            ['POST', '/short-url/shorten', 'ShortUrlController@shorten'],
            // SDKController
            // ['GET', '/sdk/init', 'ShortUrlService@getInstance'],
            // ['POST', '/sdk/init', 'ShortUrlService@init']
        ]
];