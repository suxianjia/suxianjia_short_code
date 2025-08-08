<?php
 use Suxianjia\xianjia_short_code\Core\Env;

return [
'path' => 'config/Redis.php',
'redis_version' => '1.0',
// //  $driverType 驱动类型 (redis|predis)
'driverType'  => Env::getEnv( 'REDIS_TYPE','predis' ),
'host' => Env::getEnv('REDIS_HOST'),
'port' => Env::getEnv('REDIS_PORT'),
'password' => Env::getEnv('REDIS_PASS'),

   

'master' => [
            'driverType'  => Env::getEnv( 'REDIS_TYPE','predis' ),
            'host' => Env::getEnv('REDIS_HOST'),
            'port' => Env::getEnv('REDIS_PORT'),
            'password' => Env::getEnv('REDIS_PASS'),
 ],
 'slaves' => [
    [
      'driverType'  => Env::getEnv( 'REDIS_TYPE_SLAVES_1','predis' ),
      'host' => Env::getEnv('REDIS_HOST_SLAVES_1'),
      'port' => Env::getEnv('REDIS_PORT_SLAVES_1'),
      'password' => Env::getEnv('REDIS_PASS_SLAVES_1'),
     ]
  ],

];