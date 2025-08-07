<?php

// 客户端调用示例 http://0.0.0.0:8000/s/abc123
$url = 'http://localhost:8000/s/abc123';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// 输出结果
echo "客户端调用结果：\n";
print_r(json_decode($response, true));