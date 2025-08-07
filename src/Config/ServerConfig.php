<?php 
namespace Suxianjia\xianjia_short_code\Config; 
defined('ROOT_PATH') or die('Missing constant ROOT_PATH.' . PHP_EOL);
class ServerConfig {
    private static $instance;
    private $config;

    private function __construct() {
        $this->loadConfig();
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadConfig() {
        $configFile = __DIR__.'/http_server.json';
        
        if (!file_exists($configFile)) {
            throw new \RuntimeException('Config file not found');
        }

        $this->config = json_decode(file_get_contents($configFile), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Invalid config file: '.json_last_error_msg());
        }

        $this->setDefaults();
    }

    private function setDefaults() {
        $defaults = [
            'server' => [
                'port' => 8080,
                'host' => '0.0.0.0'
            ],
            'logging' => [
                'path' => 'logs/server.log',
                'level' => 'info'
            ],
            'debug' => false
        ];

        $this->config = array_replace_recursive($defaults, $this->config);
    }

    public function get($key, $default = null) {
        $keys = explode('.', $key);
        $value = $this->config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
}
