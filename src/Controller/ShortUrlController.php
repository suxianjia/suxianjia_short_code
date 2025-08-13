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
//shorten
//http://localhost:8000/short-url/create_code 
//     生成短码
//     接收 长链接 
//     返回 短码 和 长链接
//     1. 接收长链接
//     2. 生成短码
//    public function  create_code  (Request $request, Response $response) {
    public function create  (Request $request, Response $response) {
        
         $long_url = $request->get('long_url', '');
        //  $long_url = $request->post('long_url', '');

 echo   $long_url ;
        // 1. 短码 
    $res = ShortUrlService::getInstance()->create_code($long_url);
    var_dump(    $res );
    // 2. 判断结果有效性
    if (!$res ) { 
        return $response->error( 'create code failed' );  
    }
    $response->success([
        "code"=> $res['code'],
        "url"=>$res['long_url']
    ]," create code   Success");

        
    }
//   // echo "<p> matches:". var_export($matches )   .'</p>';
//使用。 use 行不行 ？ 
public function redirect(Request $request, Response $response, $code = '') {
    // 1. 获取原始URL
    $res = ShortUrlService::getInstance()::getOriginalUrl($code);
    
    // 2. 判断结果有效性
    if (!$res || empty($res['long_url'])) { 
        return $response->error(  'Short URL not found' );
    }
    
    // 3. 安全校验（示例）
    $longUrl = $res['long_url'];
    if (!filter_var($longUrl, FILTER_VALIDATE_URL)) { 
        return $response->error( 'Invalid target URL' );
    }
    
    // 4. 执行301永久重定向
    return $response->redirect($longUrl, 301);
    
    // 若使用原生PHP header：
    // header("Location: " . $longUrl, true, 301);
    // exit;
}
// http://localhost:8000/short-url/find?code=abc1334
// http://localhost:8000/short-url/find?code=abc1334
public function find(Request $request, Response $response){

    $code =  $request->get('code')  ;
     // 1. 获取原始URL
    $res = ShortUrlService::getInstance()::getOriginalUrl($code);
    
    if (!$res || empty($res['long_url'])) { 
        return $response->error(  'Short URL not found' );
    }
    $response->success( $res," Success");


}

    // private function generateShortCode($url) {
    //     return substr(md5($url), 0, 6);
    // }
}