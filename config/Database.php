<?php
 use Suxianjia\xianjia_short_code\Core\Env;

return [
'path' => 'config/Database.php',
'Database_version' => '1.0',



    'host' => Env::getEnv('DB_HOST'),
    'port' => Env::getEnv('DB_PORT'),
    'username' => Env::getEnv('DB_USER'),
    'password' => Env::getEnv('DB_PASS'),
    'database' => Env::getEnv('DB_NAME'),
    'charset' =>  'utf8mb4',


'master' => [
    'host' => Env::getEnv('DB_HOST'),
    'port' => Env::getEnv('DB_PORT'),
    'username' => Env::getEnv('DB_USER'),
    'password' => Env::getEnv('DB_PASS'),
    'database' => Env::getEnv('DB_NAME'),
    'charset' =>  'utf8mb4',
 ],
'slaves' => [
    [
        'host' => Env::getEnv('DB_HOST'),
        'port' => Env::getEnv('DB_PORT'),
        'username' => Env::getEnv('DB_USER'),
        'password' => Env::getEnv('DB_PASS'),
        'database' => Env::getEnv('DB_NAME'),
        'charset' =>  'utf8mb4',
    ]
 ], 

];