<?php
namespace Suxianjia\xianjia_short_code\Core;
defined('ROOT_PATH') or die('Missing constant ROOT_PATH.' . PHP_EOL);
use Exception; 
//   Env.php  
// use Suxianjia\xianjia_short_code\Core\Env::loadFile()::get('database.hostname');

class Env {
    const ENV_PREFIX = 'PHP_';
        private static $instance = null;


        // # === 日志配置 读取 .env 时，请过滤 # 的行 
    public static function loadFile( $filePath = ROOT_PATH ) {

    	// $filePath = ROOT_PATH;

    	$filePath = $filePath.'/.env';

        if (!file_exists($filePath)) throw new Exception('配置文件不存在');
        // echo $filePath;
        $env = [];
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            // 跳过注释行和空行
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }
            // 解析键值对
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $env[trim($key)] = trim($value);
            }
        }
        foreach ($env as $key => $val) {
            $prefix = self::ENV_PREFIX.strtoupper($key);
            if (is_array($val)) {
                foreach ($val as $k => $v) {
                    putenv("{$prefix}_".strtoupper($k)."=$v");
                }
            } else {
                putenv("$prefix=$val");
            }
        }
    }

        public static function getInstance( ) { 
        if (self::$instance === null) {
            self::loadFile();
            self::$instance = new self(  );
        }
        return self::$instance;
    }

    
    public static function getEnv($name, $default = null) {

        self::getInstance();
        $result = getenv(self::ENV_PREFIX.strtoupper(str_replace('.', '_', $name)));
        return false !== $result ? $result : $default;
    }
}

// 使用示例
// Env::loadFile(__DIR__.'/.env');
// $dbHost = Env::getenv('database.hostname');

