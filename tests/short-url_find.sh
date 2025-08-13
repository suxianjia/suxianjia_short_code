#!/bin/bash
set -euo pipefail 
# bash --version
# GNU bash, version 3.2.57(1)-release (arm64-apple-darwin24)
# Copyright (C) 2007 Free Software Foundation, Inc.
## 请求方式: GET
test_name='short-url_find' 
# http://localhost:8000/short-url/find?code=abc1334
test_file_name='short-url_find'
test_method='GET'
# 初始化路径和日志文件
path=$(dirname "$0")
report_path="docs/${test_file_name}.md"
test_date=$(date +%Y-%m-%d)
log_success="${path}/test_success_${test_date}.log"
log_error="${path}/test_error_${test_date}.log"

source "${path}/function/function.sh"



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
short_url="$domain/short-url/find"   
# 使用普通数组模拟关联数组
test_data=(
    "code=abc1334" 
) 
echo "end ...1"
response_body=$(test_curl "$short_url"  "${test_method}"   "${test_data[@]}"   ) || exit 1
echo "end ...2"
printf   $response_body 
generate_report "${short_url}" "${response_body}" "${test_date}" "${test_method}" "${test_name}"  "${test_data[@]}" 
echo $report_path
echo "end ..."