<?php
namespace Suxianjia\xianjia_short_code\Controller;

use Suxianjia\xianjia_short_code\Services\ShortUrlService;
// use Suxianjia\xianjia_short_code\Driver\MysqlDriver;
// use Suxianjia\xianjia_short_code\Driver\RedisDriver;

use Suxianjia\xianjia_short_code\Services\MysqlServices; //      MysqlServices::getInstance()
use Suxianjia\xianjia_short_code\Services\RedisServices; //        RedisServices::getInstance()

use Suxianjia\xianjia_short_code\Core\Request;
use Suxianjia\xianjia_short_code\Core\Response;

class ShortUrlController {

	public function index (Request $request, Response $response) {
        $SystemConfig = SystemConfig::getInstance();
        $ShortUrlService =   ShortUrlService::getInstance();
        $db = MysqlServices::getInstance();
        $redis = RedisServices::getInstance();
        $response->success([],"ShortUrlController index index!");
    }

    public function shorten(Request $request, Response $response) {
        // $url = $request->getPost('url', '');
        // if (empty($url)) {
        //     $response->setStatusCode(400);
        //     $response->setHeader('Content-Type', 'application/json');
        //     $response->setContent(json_encode(['error' => 'URL不能为空']));
        //     return $response;
        // }

        // $shortCode = $this->generateShortCode($url);
        // $response->setHeader('Content-Type', 'application/json');
        // $response->setContent(json_encode(['short_url' => "http://localhost:8000/{$shortCode}", 'status' => 'success']));
        // return $response;
    }
//   // echo "<p> matches:". var_export($matches )   .'</p>';
//使用。 use 行不行 ？ 
public function redirect(Request $request, Response $response, $code = '') {
    // 1. 获取原始URL
    $res = ShortUrlService::getInstance()::getOriginalUrl($code);
    
    // 2. 判断结果有效性
    if (!$res || empty($res['long_url'])) {
        $response->setStatusCode(404);
        return $response->json(['error' => 'Short URL not found']);
    }
    
    // 3. 安全校验（示例）
    $longUrl = $res['long_url'];
    if (!filter_var($longUrl, FILTER_VALIDATE_URL)) {
        $response->setStatusCode(400);
        return $response->json(['error' => 'Invalid target URL']);
    }
    
    // 4. 执行301永久重定向
    return $response->redirect($longUrl, 301);
    
    // 若使用原生PHP header：
    // header("Location: " . $longUrl, true, 301);
    // exit;
}


    // private function generateShortCode($url) {
    //     return substr(md5($url), 0, 6);
    // }
}