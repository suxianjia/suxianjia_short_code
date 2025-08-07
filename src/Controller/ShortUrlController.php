<?php
namespace Suxianjia\xianjia_short_code\Controller;

use Suxianjia\xianjia_short_code\Services\ShortUrlService;
use Suxianjia\xianjia_short_code\Driver\MysqlDriver;
use Suxianjia\xianjia_short_code\Driver\RedisDriver;
use Suxianjia\xianjia_getui_sdk\Core\Request;
use Suxianjia\xianjia_getui_sdk\Core\Response;

class ShortUrlController {

	public function index (Request $request, Response $response) {
        $SystemConfig = SystemConfig::getInstance();
        $ShortUrlService =   ShortUrlService::getInstance();
        $db = MysqlDriver::getInstance();
        $redis = RedisDriver::getInstance(); 
        $response->success([],"ShortUrlController index index!");
    }

    public function shorten(Request $request, Response $response) {
        $url = $request->getPost('url', '');
        if (empty($url)) {
            $response->setStatusCode(400);
            $response->setHeader('Content-Type', 'application/json');
            $response->setContent(json_encode(['error' => 'URL不能为空']));
            return $response;
        }

        $shortCode = $this->generateShortCode($url);
        $response->setHeader('Content-Type', 'application/json');
        $response->setContent(json_encode(['short_url' => "http://localhost:8000/{$shortCode}", 'status' => 'success']));
        return $response;
    }

    public function redirect(Request $request, Response $response, $code) {
        $url = $this->generateShortCode($code);
        if ($url) {
            $response->setHeader('Location', $url);
            $response->setContent('');
            return $response;
        }

        $response->setStatusCode(404);
        $response->setContent('短链接不存在或已过期');
        return $response;
    }

    private function generateShortCode($url) {
        return substr(md5($url), 0, 6);
    }
}