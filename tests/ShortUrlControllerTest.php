<?php

namespace Tests;

define('ROOT_PATH', dirname(__DIR__));
defined('ROOT_PATH') or die('Missing constant ROOT_PATH.' . PHP_EOL);
require_once ROOT_PATH . '/vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
// composer require phpunit/phpunit guzzlehttp/guzzle

// ./vendor/bin/phpunit tests/ShortUrlControllerTest.php
// ./vendor/bin/phpunit tests/ShortUrlControllerTest.php
// ./vendor/bin/phpunit tests/ShortUrlControllerTest.php

define('DateTime', date('Y-m-d H:i:s' ,time()  ) ); 

class ShortUrlControllerTest extends TestCase
{
    private $domain = 'http://localhost'; // 测试域名
    private $port = 8000;  // 端口号。
    private $testData = []; // 输入数据

    private $reportFile = ROOT_PATH."/tests/reports/ShortUrlControllerTest.".DateTime.".md"; // 测试报告文件
    private $ApiFile = ROOT_PATH."/tests/apidocs/ShortUrlControllerTestApi.".DateTime.".md"; // 接口文档文件
    private $Routes = ROOT_PATH."/config/Routes.php"; // 路由文件 


    private $testName =  ''; // 输入数据
    private $response = null; // 输出数据 ，测试结果 
    private  $reportContent = "";  // 测试报告内容。

    /**
     * 读取路由文件并生成测试方法
     */
    protected function setUp(): void
    {
        parent::setUp();
        
    }

    
 
 
   

    private function generateReport(  $testResults ): void
    {
        $this->reportContent .= "# 测试报告 - " . date('Y-m-d H:i:s') . "\n\n";
        $this->reportContent .= "## 测试概况\n";
        $this->reportContent.= "- **测试类**: `" . get_class($this) . "`\n";
        $this->reportContent.= "- **测试方法**: `" . $this->getName() . "`\n";
        $this->reportContent.= "- **执行时间**: " . date('Y-m-d H:i:s') . "\n\n";

        $this->reportContent .= "## 测试环境\n";
        $this->reportContent.= "- **Base URI**: `" . $this->getBaseUri() . "`\n";
        $this->reportContent .= "- **测试数据**:\n```json\n" . json_encode($this->testData, JSON_PRETTY_PRINT) . "\n```\n\n";

        $this->reportContent .= "## 测试结果\n";
        $this->reportContent .= $this->formatTestResults($testResults);

        // 确保目录存在
        if (!file_exists(dirname($this->reportFile))) {
            mkdir(dirname($this->reportFile), 0777, true);
        }

        // 写入文件
        file_put_contents($this->reportFile,         $this->reportContent , FILE_APPEND);

        // 输出日志以便调试
        error_log("测试报告已生成: " . $this->reportFile);
    }

    protected function tearDown(): void
    {
        // 收集测试结果
        // $testResults =  $this->response;

        // // 生成测试报告
        // $this->generateReport($testResults);
    }
    private function setName(string $name = ''): void
    {  
        $this->testName =$name  ;
    }

 

    private function getName(): string
    {
         return $this->testName   ;
    }
// 生成测试报告方法。
    

    //测试结果 $result = []

private function formatTestResults(  $result  ): string  
    {
        if (empty($result)) {
            return "暂无测试数据";
        } 

        $table = "| 测试项 | 状态 | 响应时间 | 断言结果 |\n";
        $table .= "|--------|------|----------|----------|\n";

        foreach ($result as $testName => $testResult) { 
            // $table .= "| {$testName} | {$status} | {$responseTime}ms | {$assertion} |\n";
            $table .= "| {$testName} | {$testResult['status']} | {$testResult['responseTime']}ms | {$testResult['assertion']} |\n";
        }
        //  $table .= "|--------|------|----------|----------|\n";
        return $table;
    }
// 测试地址：

    private function getBaseUri(): string
    {
        return $this->domain.':'.$this->port;
    }

// 输入测试数据/
    private function getInputData(): array
    {
        return [
            'domain' => $this->domain,
            'port' => $this->port,
             'data' => $this->testData,
        ];
    }
 

    private function getResponseTime(): float
    {
        return microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
    }

 
    /**
     * 测试首页是否能正常访问
     */
  
    public function testHomeIndex()
    {
        $this->setName('测试首页是否能正常访问->'.__FUNCTION__);
        $client = new Client(['base_uri' => $this->getBaseUri(), 'http_errors' => false]);
        $response = $client->get('/');
        $this->assertEquals(200, $response->getStatusCode(), '首页访问失败');
        $this->testData['home'] = $response->getHeaders();
        $this->response = $response;
    }

    /**
     * 测试短链接重定向功能
     */
    public function testShortUrlRedirect()
    {
        $this->setName('短链接重定向->'.__FUNCTION__);
        $client = new Client(['base_uri' => $this->getBaseUri(), 'http_errors' => false]);
        $code = 'abc123';
        $this->testData['code'] = $code;
        
        $response = $client->get('/' . $code);
        $this->assertEquals(200, $response->getStatusCode(), '短链接重定向失败');
        $this->testData['redirect'] = $response->getHeaders();
        $this->response = $response;
        $this->generateReport($response);
    }

    /**
     * 测试短链接创建功能
     */
 

    public function testShortUrlCreate()
    {
        $this->setName('测试短链接创建功能->'.__FUNCTION__);
        $client = new Client(['base_uri' => $this->getBaseUri(), 'http_errors' => false]);
        $testUrl = 'https://www.example.com/' . uniqid();
        
        $response = $client->post('/short-url/create', [
            'form_params' => ['url' => $testUrl]
        ]);
        $this->assertEquals(200, $response->getStatusCode(), '短链接创建失败');
        $this->testData['create'] = json_decode($response->getBody(), true);
        $this->response = $response;
           $this->generateReport($response);
    }


    /**
     * 测试短链接查询功能
     */
    public function testShortUrlFind()
    {
        $this->setName('测试短链接查询功能->'.__FUNCTION__);
        $client = new Client(['base_uri' => $this->getBaseUri(), 'http_errors' => false]);
        $code = 'abc123';
        $this->testData['code'] = $code;
        
        $response = $client->get('/short-url/find', [
            'query' => ['code' => $code]
        ]);
        $this->assertEquals(200, $response->getStatusCode(), '短链接查询失败');
        $this->testData['find'] = json_decode($response->getBody(), true);
        $this->response = $response;
           $this->generateReport($response);
    }
} 