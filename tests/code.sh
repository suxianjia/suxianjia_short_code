#!/bin/bash
set -euo pipefail 
bash --version
# GNU bash, version 3.2.57(1)-release (arm64-apple-darwin24)
# Copyright (C) 2007 Free Software Foundation, Inc.

# 初始化路径和日志文件
path=$(dirname "$0")
report_path="docs/code.md"
test_date=$(date +%Y-%m-%d)
log_success="${path}/test_success_${test_date}.log"
log_error="${path}/test_error_${test_date}.log"


## 请求方式: GET
test_name='获取语言'
test_method='GET'
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

# 测试  get post put delete 
test_function() {
    local url="$1"
    local test_method="$2"  # 请求方式: GET/POST/PUT/DELETE 
    local test_datas=("${@:3}")  # 捕获第6个及之后的所有参数
    local response_code=0
    local curl_opts=("-s" "-o" "/dev/null" "-w" "%{http_code}")
 

    set +u
    # 根据请求方法设置curl选项
    case "$test_method" in
        GET)
            # 为GET请求添加查询参数
            if [ ${#test_datas[@]} -gt 0 ]; then
                local query_string="?"
                # 遍历所有键值对
                for key in "${!test_datas[@]}"; do
                    query_string+="${key}=${test_datas[$key]}&"
                done
                url="${url}${query_string%&}" # 去除最后一个&
            fi
            ;;
        POST|PUT)
            # 为POST/PUT请求添加数据
            if [ ${#test_datas[@]} -gt 0 ]; then
                local post_data=""
                # 遍历所有键值对
                for key in "${!test_datas[@]}"; do
                    post_data+="${key}=${test_datas[$key]}&"
                done
                curl_opts+=("-X" "$test_method" "--data" "${post_data%&}")
            else
                curl_opts+=("-X" "$test_method")
            fi
            ;;
        DELETE)
            curl_opts+=("-X" "DELETE")
            ;;
        *)
            echo "错误: 不支持的请求方法 $test_method" >&2
            return 1
            ;;
    esac

    # 执行curl请求
    response_code=$(curl "${curl_opts[@]}" "$url" 2>/dev/null)
    if [ $? -ne 0 ]; then
        echo "错误: curl 命令执行失败" >&2
        return 1
    fi
     # 恢复严格模式
    set -u
    echo "$response_code" 
} 

# ----------
load_env || exit 1 
short_url="$domain/abc1334"  
# 声明并初始化关联数组 输入 KEY=VALUE   declare -A test_data=(
# 声明关联数组

# 使用普通数组模拟关联数组
test_data=(
    "key1111=value111111"
    "key2=value2"
    "param=abc123"
)
 

 








# 生成标准化报告 
generate_report() { 
    local input_url="$1"  
    local test_response_code="$2" 
    local test_date="$3"
    local test_method="$4"
    local test_name="$5" 
    local test_array_data=("${@:6}")  # 捕获第6个及之后的所有参数
 
    printf '%s\n' "# API 测试报告"       > "$report_path"  
    printf '%s\n' "# 测试日期: $test_date"   >> "$report_path"  
    printf '%s\n' "## 测试用例"            >> "$report_path"  
    printf '%s\n' "### 测试用例方法: $test_name"     >> "$report_path"  
    printf '%s\n' "## 简要描述: [] "   >> "$report_path"  
    printf '%s\n' "## 接口状态: 已完成"       >> "$report_path"  
    printf '%s\n' "## 请求URL:  "     >> "$report_path"  
    printf '%s\n' "\`\`\`shell  "                     >> "$report_path"  
    printf '%s\n' "$input_url"     >> "$report_path"  
    printf '%s\n' "\`\`\`   "                     >> "$report_path"  
    printf '%s\n' "## 请求方式:  "          >> "$report_path"  
    printf '%s\n' "\`\`\`shell  "                     >> "$report_path"  
    printf '%s\n' "$test_method"     >> "$report_path"  
    printf '%s\n' "\`\`\`   "                     >> "$report_path"  
    printf '%s\n'  " "  



    printf '%s\n' "## 输入参数:"               >> "$report_path"  
    printf '%s\n' "| 参数名 | 值 |  必选 | 参数类型 | 说明 |"    >> "$report_path"  
    printf '%s\n' "|:--------:|:----:|:----:|:----:|:------:|"     >> "$report_path"  
    printf '%s\n' "| key    |  string  | 是 | string | -    |"    >> "$report_path"  
 
    for item in "${test_array_data[@]}"; do
        key="${item%%=*}"
        value="${item#*=}"
        printf '%s\n'  "| ${key} | ${value} | 否 | string | - | "  >> "$report_path"  
    done

    printf '%s\n' "|:--------:|:----:|:----:|:----:|:------:|"     >> "$report_path"  
    printf '%s\n'  " "  
    printf '%s\n'  " "  
    printf '%s\n'  " "  
    printf '%s\n'  " "  
    printf '%s\n'  "## 返回值: string"      >> "$report_path"  
        printf '%s\n' "## 请求结果:  "          >> "$report_path"  
    printf '%s\n' "\`\`\`shell  "                     >> "$report_path"  
    printf '%s\n' "$test_response_code" >> "$report_path"
    printf '%s\n' "\`\`\`   "                     >> "$report_path"  
    printf '%s\n'  " "  
    printf '%s\n' "## 成功返回示例:"       >> "$report_path"  
    printf '%s\n' "\`\`\`json  "                     >> "$report_path"  
    printf '%s\n' "{"                     >> "$report_path"   
    printf '%s\n' "    \"status\": \"$test_response_code\","     >> "$report_path"  
    printf '%s\n' "    \"msg\": \"redirect Success\","       >> "$report_path"  
    printf '%s\n' "    \"data\": {}"                       >> "$report_path"  
    printf '%s\n' "}"                     >> "$report_path"  
    printf '%s\n' "\`\`\`  "                     >> "$report_path"  

    printf '%s\n'  " "  
    printf '%s\n'  " "  
    printf '%s\n'  " "  
    printf '%s\n'  " "  
    printf '%s\n'  " "  
    printf '%s\n'  " "  
    printf '%s\n' "## 错误返回示例:"          >> "$report_path"  
    printf '%s\n' "\`\`\`json  "                     >> "$report_path"  
    printf '%s\n' "{"                        >> "$report_path"  
    printf '%s\n' "    \"error\": \"请求失败\","       >> "$report_path"  
    printf '%s\n' "    \"status\": \"$test_response_code\","       >> "$report_path"  
    printf '%s\n' "    \"msg\": \"redirect error\","        >> "$report_path"  
    printf '%s\n' "    \"data\": {}"            >> "$report_path"   
    printf '%s\n' "}"                              >> "$report_path"  
    printf '%s\n' "\`\`\`  "                     >> "$report_path"  
}  

response_code=$(test_function "$short_url"  "${test_method}"   "${test_data[@]}"   ) || exit 1
# local input_url="$1" 
# local response_code="$2"
# local test_date="$3"
# local test_method="$4"
# local test_name="$5"
generate_report "${short_url}" "${response_code}" "${test_date}" "${test_method}" "${test_name}"  "${test_data[@]}" 
