<?php

// 启动 PHP 内置服务器
$host = '0.0.0.0';
$port = 8000;

// 业务逻辑示例
$server = function ($request, $response) {
    // 解析请求路径
    $path = parse_url($request, PHP_URL_PATH);

    // 示例路由
    $path = rtrim($path, '/'); // 标准化路径
    $logFile = '/Users/yx-dev/work_directory/wwwroot/composer_xianjia_root/xianjia_short_code/demo/server.log';
if (!file_exists($logFile)) {
    touch($logFile);
    chmod($logFile, 0644);
}
if (file_put_contents($logFile, "Request Path: $path\n", FILE_APPEND | LOCK_EX) === false) {
    echo "Failed to write to server.log\n";
}
else {
    echo "Log written successfully to $logFile\n";
}
    if ($path === '/api/test') {
        $response->writeHead(200, ['Content-Type' => 'application/json']);
        $response->end(json_encode(['status' => 'success', 'message' => 'Hello from server.php']));
    } else {
        $response->writeHead(404, ['Content-Type' => 'application/json']);
        $response->end(json_encode(['status' => 'error', 'message' => 'Not Found']));
    }
};

// 启动服务
echo "Starting PHP server on http://$host:$port\n";
$logFile = '/Users/yx-dev/work_directory/wwwroot/composer_xianjia_root/xianjia_short_code/demo/server.log';
if (!file_exists($logFile)) {
    touch($logFile);
    chmod($logFile, 0644);
}
if (file_put_contents($logFile, "Starting PHP server on http://$host:$port\n", FILE_APPEND | LOCK_EX) === false) {
    echo "Failed to write to server.log\n";
}
else {
    echo "Log written successfully to $logFile\n";
}
$socket = stream_socket_server("tcp://$host:$port", $errno, $errstr);

if (!$socket) {
    die("Failed to start server: $errstr ($errno)\n");
}

while ($conn = stream_socket_accept($socket)) {
    $request = fread($conn, 1024);
    $response = new class {
        public function writeHead($status, $headers) {
            $this->status = $status;
            $this->headers = $headers;
        }

        public function end($data) {
            $this->data = $data;
        }
    };

    file_put_contents('server.log', "Request: $request\n", FILE_APPEND);
$server($request, $response);

    $responseData = "HTTP/1.1 {$response->status}\r\n";
    foreach ($response->headers as $key => $value) {
        $responseData .= "$key: $value\r\n";
    }
    $responseData .= "\r\n{$response->data}";

    fwrite($conn, $responseData);
    fclose($conn);
}

file_put_contents('server.log', "Server stopped\n", FILE_APPEND);
fclose($socket);