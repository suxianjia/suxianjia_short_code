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
 /**
  *  
 * http://localhost:8000/short-url/create_code 
 *    生成短码
 * 
 * method :" post"
 *    接收 长链接 
 *    返回 短码 和 长链接
 *     1. 接收长链接
 *     2. 生成短码
 * */ 
    public function create  (Request $request, Response $response) { 

        //         var_dump(    [$request->post('long_url', '') , $request->post('user_id', 0) ]);
        // exit;


        // $long_url = $request->get('long_url', ''); 
        $long_url = $request->post('long_url', ''); 
        if ( $long_url =='') {
            return $response->error(  'long_url   is empty' ); 
        } 

         $user_id = $request->post('user_id', 0); 
                 if ( $user_id ==0) {
            return $response->error(  'user_id   is empty' ); 
        } 



        $res = ShortUrlService::getInstance()::create_code($long_url, $user_id); 
        if ( 200 != $res['code']  ) { 
            return $response->error( $res['message']);  
        }
        $response->success(  $res['data'] ,$res['message'] ); 
    }
 /**
  *  redirect
 * */
public function redirect(Request $request, Response $response, $code = '') { 
        if ( $code =='') {
                 return $response->error(  'shortCode   is empty' ); 
        } 
    $res = ShortUrlService::getInstance()::getOriginalUrl($code); 
    if ($res['code'] != 200  ) { 
        return $response->error(  'Short URL not found' );
    }
     $longUrl =$res['data']['long_url']; 
    $longUrl =$res['data']['long_url'];
    if (!filter_var($longUrl, FILTER_VALIDATE_URL)) { 
        return $response->error( 'Invalid target URL' );
    }
    
    // 4. 执行301永久重定向
    return $response->redirect($longUrl, 301);
    
    // 若使用原生PHP header：
    // header("Location: " . $longUrl, true, 301);
    // exit;
}

/**
 * find
 * http://localhost:8000/short-url/find?code=abc1334
 * http://localhost:8000/short-url/find?code=abc1334
 * 
 * */

public function find(Request $request, Response $response){ 
    $code =  $request->get('code','')  ;
      if ( $code =='') {
                 return $response->error(  'shortCode   is empty' ); 
        } 
    // 1. 获取原始URL
    $res = ShortUrlService::getInstance()::getOriginalUrl($code);  
    if ($res['code'] != 200  ) { 
        return $response->error(     $res ['message'] );
    }  
    return $response->success( $res['data'], $res ['message']); 
}

    // private function generateShortCode($url) {
    //     return substr(md5($url), 0, 6);
    // }
}