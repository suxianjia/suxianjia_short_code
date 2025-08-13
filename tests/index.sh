#!/bin/bash

apipath=docs/index.md # docs/index.md
#  chmod +x tests/index.sh
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

#  http://localhost:8000/
# get
#  {"code":200,"message":"hello index!","data":[]}


test_name='短链接重定向测试'
test_method='GET'
test_data=(
    "key1=value1"
    "key2=value2"
)

short_url="$domain"
response_body=$(test_curl "$short_url" "${test_method}" "${test_data[@]}") || exit 1

date=$(date +"%Y-%m-%d")
log_success="${path}/test_success_${date}.log"
log_error="${path}/test_error_${date}.log"

if [[ "$response_body" == "200" ]]; then
    echo "测试通过：短链接重定向成功，状态码 200"
    echo -e "|--- domain: $domain, method: $test_method, response: 200" >> "$log_success"
    exit 0
else
    echo "测试失败：状态码 $response_body"
    echo -e "|--- domain: $domain, method: $test_method, response: $response_body" >> "$log_error"
    exit 1
fi
        echo "|---   \n" >> ${path}/test_error_${date}.log

        echo  "通过" 
else
        echo "测试失败：短链接重定向失败，状态码 $response"
        echo -e "|--- domain  : $domain   inputData : $inputData , method : $method , user_agent: $user_agent , response : $response " >> ${path}/test_error_${date}.log
        echo "|--- 测试失败：短链接重定向失败，状态码 $response. \n" >> ${path}/test_error_${date}.log
        echo "|---   \n" >> ${path}/test_error_${date}.log
        exit 1
fi
