#!/bin/bash

# 测试  get post put delete 
# 返回请求结果 API 格式返回 code 和 body 

#!/bin/bash

test_function() {
    local url="$1"
    # local test_method="${2:-GET}"  # 默认GET方法
    local test_method="$2"  # 默认GET方法
    local test_data="${3:-}"  # 捕获请求数据     local test_datas=("${@:3}")
    local response_file=$(mktemp)
    local response_code=0
    local response_body=""
    local curl_opts=(
        "-s" 
        "-o" "$response_file" 
        "-w" "%{http_code}" 
        "-H" "User-Agent: Mozilla/5.0"
        "-H" "Accept: application/json"
    )
 echo " url :: $url"
 echo " test_method :: $test_method"

    # 根据请求方法处理参数
    case "$test_method" in
        GET)
            if [ -n "${test_datas:-}" ] && [ ${#test_datas[@]} -gt 0 ]; then
                local query_string="?"
                for item in "${test_datas[@]}"; do
                    IFS='=' read -r key value <<< "$item"
                    query_string+="${key}=${value}&"
                done
                url="${url}${query_string%&}"
            fi
            ;;
        POST|PUT|PATCH)
            if [ -n "${test_datas:-}" ] && [ ${#test_datas[@]} -gt 0 ]; then
                local json_data="{"
                for item in "${test_datas[@]}"; do
                    IFS='=' read -r key value <<< "$item"
                    json_data+="\"$key\":\"$value\","
                done
                json_data="${json_data%,}}"
                curl_opts+=(
                    "-X" "$test_method"
                    "-H" "Content-Type: application/json"
                    "--data" "$json_data"
                )
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
 echo " url :: $url"
 echo " test_method :: $test_method"
#     exit 1
echo -e    $url 
exit 1
    # 执行请求并捕获输出
    response_code=$(curl "${curl_opts[@]}" "$url")
    response_body=$(cat "$response_file")
    rm -f "$response_file"

    # 返回JSON格式结果
    # printf '%s'  "$(jq -aRs . <<< "$response_body")"
echo -e  $response_body 
    #  printf  '%s'  $response_body  
}

#  格式如：
# {
#   "code": 400,
#   "data": {},
#   "message": "Short URL not found"
# }

# 生成标准化报告 
generate_report() { 
    local input_url="$1"  
    local test_response_body="$2" # json {"code":400,"message":"Short URL not found"}
    local test_date="$3"
    local test_method="$4"
    local test_name="$5" 
    local test_array_data=("${@:6}")  # 捕获第6个及之后的所有参数

 # 使用jq解析JSON响应体
    local code=$(jq -r '.code' <<< "$test_response_body" 2>/dev/null || echo "null")
    local message=$(jq -r '.message // empty' <<< "$test_response_body" 2>/dev/null)
    local data=$(jq -r '.data // empty' <<< "$test_response_body" 2>/dev/null)
     # 生成参数表格  json {"code":400,"message":"Short URL not found"}
   local table_content="| 参数名 | 值 | 必选 | 类型 | 说明 |\n"
   table_content+="|:------:|:--:|:----:|:----:|:----:|\n"
   # 使用jq解析JSON并生成表格行
    while IFS="=" read -r key value; do
        table_content+="| ${key} | ${value} | 否 | string | - |\n"
    done < <(jq -r 'to_entries[] | "\(.key)=\(.value)"' <<< "$test_response_body")
    # echo -e "$table_content"

    # docs/short-url/create.md: No such file or directory
    if [ ! -e "$report_path" ]; then
        echo "Error: Path $report_path does not exist" >&2
        echo "" > $report_path
    fi
    printf '%s\n' "# API 测试报告"          > "$report_path"  
    printf '%s\n' "# 测试日期: $test_date"   >> "$report_path"  
    printf '%s\n' "## 测试用例"            >> "$report_path"  
    printf '%s\n' "### 测试用例方法: $test_name"     >> "$report_path"  
    printf '%s\n' "## 简要描述: [] "   >> "$report_path"  
    printf '%s\n' "## 接口状态: 已完成"       >> "$report_path"  
    printf '%s\n' "## 请求URL:  "     >> "$report_path"  
    printf '%s\n' "\`\`\`shell  "                     >> "$report_path"  
    printf '%s\n' "$input_url"              >> "$report_path"  
    printf '%s\n' "\`\`\`   "                     >> "$report_path"  
    printf '%s\n' "## 请求方式:  "          >> "$report_path"  
    printf '%s\n' "\`\`\`shell  "                     >> "$report_path"  
    printf '%s\n' "$test_method"     >> "$report_path"  
    printf '%s\n' "\`\`\`   "                     >> "$report_path"  
    printf '%s\n'  " "   >> "$report_path" 



    printf '%s\n' "## 输入参数:"               >> "$report_path"  
    printf '%s\n' "| 参数名 | 值 |  必选 | 参数类型 | 说明 |"    >> "$report_path"  
    printf '%s\n' "|:--------:|:----:|:----:|:----:|:------:|"     >> "$report_path"  
    # printf '%s\n' "| key    |  string  | 是 | string | -    |"    >> "$report_path"  
 
    for item in "${test_array_data[@]}"; do
        key="${item%%=*}"
        value="${item#*=}"
        # 数据类型检测逻辑
        if [[ "$value" =~ -9]+$ ]]; then
            gettype="integer"
        elif [[ "$value" =~ -9]+\.[0-9]+$ ]]; then
            gettype="float"
        elif [[ "$value" =~ ^(true|false)$ ]]; then
            gettype="boolean"
        elif [[ "$value" =~ ^\".*\"$ ]] || [[ "$value" =~ ^\'.*\'$ ]]; then
            gettype="string"
        else
            gettype="string"  # 默认类型
        fi
        printf '%s\n'  "| ${key} | ${value} | 否 | ${gettype} | - | "  >> "$report_path"  
    done

    # printf '%s\n' "|:--------:|:----:|:----:|:----:|:------:|"     >> "$report_path"  
    printf '%s\n'  " "    >> "$report_path" 
    printf '%s\n'  " "    >> "$report_path" 
    printf '%s\n'  " "    >> "$report_path" 
    printf '%s\n'  " "    >> "$report_path" 
    printf '%s\n'  "## 返回值: string"      >> "$report_path"  
        printf '%s\n' "## 请求结果:  "          >> "$report_path"  
    printf '%s\n' "\`\`\`shell  "                     >> "$report_path"  
    printf '%s\n' "$code" >> "$report_path"
    printf '%s\n' "\`\`\`   "                     >> "$report_path"  
    printf '%s\n'  " "             >> "$report_path"  
    printf '%s\n' "## 成功返回示例:"       >> "$report_path"  
    printf '%s\n' "\`\`\`json  "                     >> "$report_path"  
    printf '%s\n' "{"                     >> "$report_path"   
    printf '%s\n' "    \"status\": \"$code\","     >> "$report_path"  
    printf '%s\n' "    \"msg\": \"$message\","       >> "$report_path"  
    printf '%s\n' "    \"data\": $data"                       >> "$report_path"  
    printf '%s\n' "}"                     >> "$report_path"  
    printf '%s\n' "\`\`\`  "                     >> "$report_path"  

    printf '%s\n'  " "   >> "$report_path" 
    printf '%s\n'  " "   >> "$report_path" 
    printf '%s\n'  " "   >> "$report_path" 
    printf '%s\n'  " "   >> "$report_path" 
    printf '%s\n'  " "   >> "$report_path" 
    printf '%s\n'  " "   >> "$report_path" 
    printf '%s\n' "## 错误返回示例:"          >> "$report_path"  
    printf '%s\n' "\`\`\`json  "                     >> "$report_path"  
    printf '%s\n' "{"                        >> "$report_path"  
    # printf '%s\n' "    \"error\": \"请求失败\","       >> "$report_path"  
    printf '%s\n' "    \"status\": \"$code\","       >> "$report_path"  
    printf '%s\n' "    \"msg\": \"$message\","        >> "$report_path"  
    printf '%s\n' "    \"data\":  $data"            >> "$report_path"   
    printf '%s\n' "}"                              >> "$report_path"  
    printf '%s\n' "\`\`\`  "                     >> "$report_path"  
    printf '%s\n'  " "   >> "$report_path" 
    printf '%s\n'  " "   >> "$report_path" 
    printf '%s\n' "## 返回参数:"               >> "$report_path"  
    # printf '%s\n' "| 参数名 | 值 |  必选 | 参数类型 | 说明 |"    >> "$report_path"  
    # printf '%s\n' "|:--------:|:----:|:----:|:----:|:------:|"     >> "$report_path"  
    printf  "$table_content"    >> "$report_path"    
    # printf '%s\n' "|:--------:|:----:|:----:|:----:|:------:|"     >> "$report_path"  

}  