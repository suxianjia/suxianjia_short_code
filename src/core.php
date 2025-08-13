<?php 
namespace Suxianjia\xianjia_short_code;
defined('ROOT_PATH') or die('Missing constant ROOT_PATH.' . PHP_EOL);
require_once ROOT_PATH . '/vendor/autoload.php';
use Exception; 
use Suxianjia\xianjia_short_code\Core\SystemConfig; // $config = SystemConfig::getInstance():: getModel('Database');
// use Suxianjia\xianjia_short_code\Config\RouteConfig; 
use Suxianjia\xianjia_short_code\Config\RouteConfig; 
use Suxianjia\xianjia_short_code\Services\ShortUrlService; // 本项目核心业务类  
use Suxianjia\xianjia_short_code\Services\MysqlServices; // 数据库 实例化好了 直接使用 
use Suxianjia\xianjia_short_code\Services\RedisServices;//  缓存  实例化好了 直接使用 
use Suxianjia\xianjia_short_code\Core\Request; //用户请求类
use Suxianjia\xianjia_short_code\Core\Response;// 接口返回类 
 

/**
 * 短域名系统应用主类
 * 提供命令行和 Web 接口
 */
 

 use Suxianjia\xianjia_short_code\Core\Env;

class core {
    private $sdk;
    private $Config;

    public function __construct() {   
        // 初始化业务逻辑
    }

    /**
     * 运行应用
     */
    public function run() { 
        if (php_sapi_name() === 'cli') {
            $this->runCli();
        } else {
            $this->runWeb();
        }
    }

    /**
     * 缩短 URL
     * @param string $longUrl 原始 URL
     * @return array 包含短码的数组
     */
    // public function shorten($longUrl) {
    //     $shortCode = $this->generateShortCode();
    //     $db = MysqlServices::getInstance();
    //     $redis = RedisServices::getInstance();
 
    //     $db->insert('short_urls', [
    //         'long_url' => $longUrl,
    //         'short_code' => $shortCode
    //     ]);
    //     $redis->setex("shorturl:$shortCode", 3600, $longUrl);
    //     return ['short_code' => $shortCode];
    // }

    /**
     * 获取原始 URL
     * @param string $shortCode 短码
     * @return array 包含原始 URL 的数组
     */
    // public function getOriginalUrl($shortCode) {

    //   $db = MysqlServices::getInstance();
    //     $redis = RedisServices::getInstance();
    //     $longUrl = $redis->get("shorturl:$shortCode");
    //     if ($longUrl) {
    //         return ['long_url' => $longUrl];
    //     }

    //     $result = $db->fetch("SELECT long_url FROM short_urls WHERE short_code = ?", [$shortCode]);
    //     if ($result) {
    //         $redis->setex("shorturl:$shortCode", 3600, $result['long_url']);
    //         return $result['long_url'];
    //     }

    //     return null;
    // }

    // private function generateShortCode() {
    //     return substr(md5(uniqid()), 0, 6);
    // }

    // private function shortenUrl($url) {
    //     if (empty($url)) {
    //         throw new \InvalidArgumentException('Bad Request: URL is required');
    //     }
    //     return substr(md5($url), 0, 6);
    // }

    private function redirect($shortCode) {
        // 实现重定向逻辑
        return "Redirecting to URL for code: " . $shortCode;
    }
    private function runCli() {
        global $argv;
        $action = $argv[1] ?? null;
        $param = $argv[2] ?? null;

        // switch ($action) {
        //     case 'shorten':
        //         echo $this->sdk->shortenUrl($param) . "\n";
        //         break;
        //     case 'redirect':
        //         echo $this->sdk->redirect($param) . "\n";
        //         break;
        //     default:
        //         echo "Usage: php core.php [shorten|redirect] [url|code]\n";
        // }
    }

    private function runWeb() {  
        $request = new  Request();
        $response = new  Response(); 
        $routes = RouteConfig::getRoutes(); 

        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'); // 确保路径不包含查询参数和多余斜杠
 

        // 检查 POST 请求是否被正确捕获
        if ($requestMethod === 'POST') { 
            $input = file_get_contents('php://input'); 
        } 
 // echo " requestMethod  : $requestMethod";
// echo "<pre>";
   // print_r( $routes); exit;

        // 检查静态路由
        foreach ($routes[$requestMethod] as $route => $handler) {
            // echo "检查静态路由";

            $route = trim($route, '/');
            if ($route === $requestUri) {
                list($controller, $method) = explode('@', $handler);
                $controller = "Suxianjia\\xianjia_short_code\\Controller\\{$controller}";
                // echo "00000";
                try { 
                    $controller = new $controller( ); 
       
                    $controller->$method(     $request,  $response);
                     // echo "111111";
                    return;
                } catch (\Exception $e) {
                    echo "Controller instantiation failed: " . $e->getMessage() . "\n";
                    http_response_code(500);
                    echo '500 Internal Server Error';
                    return;
                }
            }
        }
// echo "xxx1";

         // echo "<p> requestUri:". $requestUri  .'</p>';

        // 检查动态路由（如 /{code}）
        foreach ($routes[$requestMethod] as $route => $handler) {
            $route = trim($route, '/');
            if (strpos($route, '{') !== false) {
                $pattern = str_replace('{code}', '([a-zA-Z0-9]+)', $route);
                if (preg_match("#^{$pattern}$#", $requestUri, $matches)) {
                    list($controller, $method) = explode('@', $handler);
                    $controller = "Suxianjia\\xianjia_short_code\\Controller\\{$controller}";

                     // echo "<p> method:". $method  .'</p>';
// var_dump($matches );


                    try { 
                        $controller = new $controller( ); 
                        $controller->$method( $request,  $response,  $matches[1]);
                       
 // $controller->$method($request,  $response, $matches[1]);
                                   // echo "<p> matches:". var_export($matches )   .'</p>';

                        return;
                    } catch (\Exception $e) {
                        echo "Controller instantiation failed: " . $e->getMessage() . "\n";
                        http_response_code(500);
                        echo '500 Internal Server Error';
                        return;
                    }
                }
            }
             // echo "xxx123";
        }

        // 未匹配到路由
        http_response_code(404);
        echo '404 Not Found';
    }
}

// // 启动应用
// (new core())->run();

// 启动应用
// (new MyApp())->run();