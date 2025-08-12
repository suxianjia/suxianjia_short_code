#!/bin/bash
#  chmod +x tests/create_code.sh
apipath=docs/create_code.md
# 测试 create_code 方法
# 测试短链接重定向功能
# domain="http://localhost:8000/"
path=$(dirname "$0") # 获取当前文件所在目录
# path=
# 从 tests/test.env.ini 文件加载变量
if [ -f ${path}/test.env.ini ]; then
    # echo "加载 ${path}/test.env.ini 文件..."
    source ${path}/test.env.ini
    echo "domain 变量值: $domain"
else
    echo "错误: ${path}/test.env.ini 文件不存在"
    exit 1
fi
# 模拟请求数据
data_long_url="https://example.com"

inputData="short-url/create_code"
url="$domain$inputData"
method='GET'
user_agent="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:66.0) Gecko/20100101 Firefox/66.0"



# 发送 POST 请求 http://localhost:8000/
response=$(curl -s -X POST "$url" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "long_url=${data_long_url}")

# 解析响应
code=$(echo "$response" | jq -r '.code')
url=$(echo "$response" | jq -r '.url')
message=$(echo "$response" | jq -r '.message')

# 验证结果
if [[ "$code" != "" && "$url" == "$data_long_url" && "$message" == "create code Success" ]]; then
  echo "Test passed: Code generated successfully"
  exit 0
else
  echo "Test failed: Invalid response"
  echo "Response: $response"
  exit 1
fi