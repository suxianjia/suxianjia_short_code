<?php
 use Suxianjia\xianjia_short_code\Core\Env;

return [
'path' => 'config/Redis.php',
'redis_version' => '1.0',
'host' => Env::getEnv('REDIS_HOST'),
'port' => Env::getEnv('REDIS_PORT'),
'password' => Env::getEnv('REDIS_PASS')

            //REDIS_HOST=127.0.0.1
// REDIS_PORT=6379
// REDIS_PASS=654321mm

];