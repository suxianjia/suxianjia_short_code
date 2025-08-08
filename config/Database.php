<?php
 use Suxianjia\xianjia_short_code\Core\Env;

return [
'path' => 'config/Database.php',
'Database_version' => '1.0',

//  $driverType 驱动类型 (redis|predis)
'driverType' => Env::getEnv( 'DB_TYPE','pdo' ),
    'host' => Env::getEnv('DB_HOST'),
    'port' => Env::getEnv('DB_PORT'),
    'username' => Env::getEnv('DB_USER'),
    'password' => Env::getEnv('DB_PASS'),
    'database' => Env::getEnv('DB_NAME'),
     'charset' =>   Env::getEnv( 'DB_CHAR_SET','utf8mb4'),


'master' => [
    'driverType' => Env::getEnv( 'DB_TYPE','pdo' ),
    'host' => Env::getEnv('DB_HOST'),
    'port' => Env::getEnv('DB_PORT'),
    'username' => Env::getEnv('DB_USER'),
    'password' => Env::getEnv('DB_PASS'),
    'database' => Env::getEnv('DB_NAME'),
    'charset' =>   Env::getEnv( 'DB_CHAR_SET','utf8mb4'),
 ],
'slaves' => [
    [
        // DB_HOST_SLAVES_1
        'driverType' => Env::getEnv( 'DB_TYPE_SLAVES_1','pdo' ),
        'host' => Env::getEnv('DB_HOST_SLAVES_1'),
        'port' => Env::getEnv('DB_PORT_SLAVES_1'),
        'username' => Env::getEnv('DB_USER_SLAVES_1'),
        'password' => Env::getEnv('DB_PASS_SLAVES_1'),
        'database' => Env::getEnv('DB_NAME_SLAVES_1'),
        'charset' =>  Env::getEnv( 'DB_CHAR_SET_SLAVES_1','utf8mb4'),  
    ]
 ], 

];