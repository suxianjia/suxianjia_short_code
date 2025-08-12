# API 文档
# docs/api.md
## 测试用例模板

### 控制器方法 :  获取语言
## 简要描述 : test
##  接口状态 :   已完成
##  请求URL :   {{domain}}/abc123
## 请求方式 :   get
## 输入参数:
| 参数名     | 必选 | 类型 | 参数类型 | 说明 |
| -------- |:--------: |:------:|:------:|:------:|
| key    | 是 | string  | body | -   |  
| abc123 | 是 | string  | body | -   |  
## 返 回 值 :   string
## 成功返回示例 : 
``` json
{
    "cmd_id": -100,
    "status": 200,
    "code": 0,
    "msg": "success",
    "data": {
        "data": [
            
            {
                "language_id": 1032,
                "currency_id": 886,
                "language_status": 1,
                "language_name": "繁中(TW)",
                "language_code": "zh_TW",
                "language_directory": "zh_TW"
            }
        ]
    }
}
```
## 返回参数
|参数|类型|说明|
|---|---|---|
|cmd_id|int|接口操作码 0表示成功，其他表示具体错误码（如-100表示无接口操作码）|
|code|int|请求接口的返回编码（0表示成功，非0表示接口出现错误，错误码）|
|status|int|请求接口是否正确返回（200表示正确返回，其他表示未正确返回）|
|message|string|接口返回的错误信息|
|data|JSON|接口返回的数据信息，可能不存在|  
## 创建短链