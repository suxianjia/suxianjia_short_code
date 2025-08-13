#!/bin/bash
set -euo pipefail 
# bash --version
# GNU bash, version 3.2.57(1)-release (arm64-apple-darwin24)
# Copyright (C) 2007 Free Software Foundation, Inc.
## 请求方式: GET
test_name='short_url_create' 
#     ['POST', '/short-url/create', 'ShortUrlController@create'],
# http://localhost:8000/short-url/find?code=abc1334
test_file_name='short_url_create'
test_method='POST'
# 初始化路径和日志文件
path=$(dirname "$0")
report_path="docs/${test_file_name}.md"
test_date=$(date +%Y-%m-%d)
log_success="${path}/test_success_${test_date}.log"
log_error="${path}/test_error_${test_date}.log"

source "${path}/function/function.sh"

echo "start test ...1"

# 加载环境变量
load_env() {
    if [ -f "${path}/test.env.ini" ]; then
        source "${path}/test.env.ini" || {
            echo "错误: 加载环境变量失败" | tee -a "$log_error"
            exit 1
        }
    else
        echo "错误: ${path}/test.env.ini 文件不存在" | tee -a "$log_error"
        exit 1
    fi
} 
# ----------
load_env || exit 1 
short_url="$domain/short-url/create"   
# 使用普通数组模拟关联数组
test_data=( 
    "long_url=https://www.upetrol.net/new_product_detail/id/1601/user_id/234567"
    "user_id=1601"
)  
test_curl() {
# 添加超时和重试机制
local curl_cmd="curl -m 10 -s --retry 2 --retry-delay 1"

# 添加请求日志
# echo "Making request to: $short_url"
# echo "Method: $test_method"
# echo "Data: ${test_data[*]}"

# 执行请求并捕获响应和状态码
local response_body
local http_code

response_body=$($curl_cmd -X "$test_method" -d "${test_data[*]}" "$short_url")
http_code=$?

# 检查curl执行状态
if [ $http_code -ne 0 ]; then
    echo "Error: curl failed with code $http_code" >&2
    return 1
fi

# 返回响应体
echo "$response_body"

}
echo $report_path
response_body=$(test_curl "$short_url"  "${test_method}"   "${test_data[@]}"   ) || exit 1
echo "$response_body"
echo "start test ...2"
echo $report_path
echo "start test ...3"
printf   $response_body 
generate_report "${short_url}" "${response_body}" "${test_date}" "${test_method}" "${test_name}"  "${test_data[@]}" 
echo $report_path
echo "end ..."