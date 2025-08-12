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


inputData=""
url="$domain$inputData"
method='GET'
user_agent="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:66.0) Gecko/20100101 Firefox/66.0"

cookies=""
# 模拟 GET 请求
response=$(curl -s -o /dev/null -w "%{http_code}" "$url")

echo  "-------- "

echo "response 值： $response"
date=$(date +"%Y-%m-%d")

# 验证返回状态码是否为 301 或 302（重定向）
if [[ "$response" == "301" || "$response" == "302" ]]; then
        echo "测试通过：短链接重定向成功，状态码 $response"
        echo -e "|--- domain  : $domain   inputData : $inputData , method : $method , user_agent: $user_agent , response : $response  " >> ${path}/test_sueccs_${date}.log 
        echo "|--- 测试通过：短链接重定向成功，状态码 $response \n" >> ${path}/test_sueccs_${date}.log
        echo "|---   \n" >> ${path}/test_error_${date}.log
        exit 0
elif  [[ "200" == "$response" ]]; then
        echo "200 "

        echo "测试通过：短链接重定向成功，状态码 $response"
        echo -e "|--- domain  : $domain   inputData : $inputData , method : $method , user_agent: $user_agent , response : $response  " >> ${path}/test_sueccs_${date}.log 
        echo "|--- 测试通过：短链接重定向成功，状态码 $response \n" >> ${path}/test_sueccs_${date}.log
        echo "|---   \n" >> ${path}/test_error_${date}.log

        echo  "通过" 
else
        echo "测试失败：短链接重定向失败，状态码 $response"
        echo -e "|--- domain  : $domain   inputData : $inputData , method : $method , user_agent: $user_agent , response : $response " >> ${path}/test_error_${date}.log
        echo "|--- 测试失败：短链接重定向失败，状态码 $response. \n" >> ${path}/test_error_${date}.log
        echo "|---   \n" >> ${path}/test_error_${date}.log
        exit 1
fi
