<?php
 use Suxianjia\xianjia_short_code\Core\Env;

return [
'path' => 'config/Redis.php',
'redis_version' => '1.0',

'host' => Env::getEnv('REDIS_HOST'),
'port' => Env::getEnv('REDIS_PORT'),
'password' => Env::getEnv('REDIS_PASS'),

   

'master' => [
    'host' => Env::getEnv('REDIS_HOST'),
    'port' => Env::getEnv('REDIS_PORT'),
    'password' => Env::getEnv('REDIS_PASS')
 ],
 'slaves' => [
    [
        'host' => Env::getEnv('REDIS_HOST_SLAVES_1'),
        'port' => Env::getEnv('REDIS_PORT_SLAVES_1'),
        'password' => Env::getEnv('REDIS_PASS_SLAVES_1'),
     ]
  ],

];