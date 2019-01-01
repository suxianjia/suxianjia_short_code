#!/bin/bash

# 测试 create_code 方法

# 模拟请求数据
long_url="https://example.com"

# 发送 POST 请求
response=$(curl -s -X POST "http://localhost/short-url/create_code" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "long_url=${long_url}")

# 解析响应
code=$(echo "$response" | jq -r '.code')
url=$(echo "$response" | jq -r '.url')
message=$(echo "$response" | jq -r '.message')

# 验证结果
if [[ "$code" != "" && "$url" == "$long_url" && "$message" == "create code Success" ]]; then
  echo "Test passed: Code generated successfully"
  exit 0
else
  echo "Test failed: Invalid response"
  echo "Response: $response"
  exit 1
fi