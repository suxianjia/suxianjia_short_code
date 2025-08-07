<?php

 use Suxianjia\xianjia_short_code\Core\Env;
return [
'path' => 'config/App.php',
'Database_version' => '1.0',

'env' => Env::getEnv('APP_ENV'),

'port' => Env::getEnv('PORT'),
'config_dir' => Env::getEnv('CONFIG_DIR'),
'public_dir' => Env::getEnv('PUBLIC_DIR'),
'domain' => Env::getEnv('DOMAIN'),
];