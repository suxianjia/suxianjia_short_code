<?php

 use Suxianjia\xianjia_short_code\Core\Env;
return [
'path' => 'config/Logs.php',
'version' => '1.0',

'logs_dir' => Env::getEnv('LOG_DIR'), 
];