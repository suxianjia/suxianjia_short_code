<?php

// 启动 PHP 内置服务器
$host = '0.0.0.0';
$port = 8000;

// 业务逻辑示例
// 数据库连接配置
$dbConfig = [
    'host' => 'localhost',
    'dbname' => 'short_url_db',
    'username' => 'root',
    'password' => 'password'
];

// 获取长链接函数
function getLongUrlFromDatabase($shortCode) {
    global $dbConfig;
    try {
        $pdo = new PDO("mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']}", $dbConfig['username'], $dbConfig['password']);
        $stmt = $pdo->prepare("SELECT long_url FROM short_urls WHERE short_code = :short_code");
        $stmt->bindParam(':short_code', $shortCode);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['long_url'] : null;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return null;
    }
}

$server = function ($request, $response) {
    // 解析请求路径
    $path = parse_url($request, PHP_URL_PATH);

    // 示例路由
    $path = rtrim($path, '/'); // 标准化路径
    $logFile = './server.log';
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
    } elseif (preg_match('/^\/s\/([a-zA-Z0-9]+)$/', $path, $matches)) {
        // 短链接转长链接逻辑
        $shortCode = $matches[1];
        $longUrl = getLongUrlFromDatabase($shortCode); // 假设从数据库获取长链接
        if ($longUrl) {
            $response->writeHead(200, ['Content-Type' => 'application/json']);
            $response->end(json_encode(['status' => 'success', 'long_url' => $longUrl]));
        } else {
            $response->writeHead(404, ['Content-Type' => 'application/json']);
            $response->end(json_encode(['status' => 'error', 'message' => 'Short URL not found']));
        }
    } else {
        $response->writeHead(404, ['Content-Type' => 'application/json']);
        $response->end(json_encode(['status' => 'error', 'message' => 'Not Found']));
    }
};

// 启动服务
echo "Starting PHP server on http://$host:$port\n";
$logFile = './server.log';
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

// 设置非阻塞模式
stream_set_blocking($socket, false);

while (true) {
    $conn = @stream_socket_accept($socket, 5); // 5秒超时
    if ($conn === false) {
        continue; // 超时后继续等待
    }
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