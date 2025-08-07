# API 文档

## HomeController

### 1. `index`
- **描述**: 默认首页接口
- **请求方法**: GET
- **请求地址**: `http://localhost:8000/`
- **响应示例**:
  ```json
  {
    "data": [],
    "message": "hello index!"
  }
  ```

## ShortUrlController

### 1. `index`
- **描述**: 默认首页接口
- **请求方法**: GET
- **请求地址**: `http://localhost:8000/`
- **响应示例**:
  ```json
  {
    "data": [],
    "message": "hello index!"
  }
  ```

### 2. `all`
- **描述**: 获取所有短链接列表
- **请求方法**: GET
- **请求地址**: `http://localhost:8000/all?page=1&per_page=15`
- **响应示例**:
  ```json
  {
    "data": [
      {
        "id": 1,
        "long_url": "https://example.com",
        "short_code": "abc123"
      }
    ]
  }
  ```

### 3. `find`
- **描述**: 根据 ID 查询短链接
- **请求方法**: GET
- **请求地址**: `http://localhost:8000/find?id=1`
- **响应示例**:
  ```json
  {
    "data": {
      "id": 1,
      "long_url": "https://example.com",
      "short_code": "abc123"
    }
  }
  ```

### 4. `create`
- **描述**: 创建短链接
- **请求方法**: POST
- **请求地址**: `http://localhost:8000/create`
- **请求体**:
  ```json
  {
    "long_url": "https://example.com",
    "short_code": "abc123"
  }
  ```
- **响应示例**:
  ```json
  {
    "data": {
      "id": 1
    },
    "message": "创建成功",
    "status": 201
  }
  ```

### 5. `update`
- **描述**: 更新短链接
- **请求方法**: PUT
- **请求地址**: `http://localhost:8000/update?id=1`
- **请求体**:
  ```json
  {
    "long_url": "https://updated.com",
    "short_code": "def456"
  }
  ```
- **响应示例**:
  ```json
  {
    "message": "更新成功"
  }
  ```

### 6. `delete`
- **描述**: 删除短链接
- **请求方法**: DELETE
- **请求地址**: `http://localhost:8000/delete?id=1`
- **响应示例**:
  ```json
  {
    "message": "删除成功"
  }
  ```

### 7. `redirect`
- **描述**: 短链接跳转
- **请求方法**: GET
- **请求地址**: `http://localhost:8000/redirect?code=abc123`
- **响应示例**:
  - 成功: 302 跳转到目标 URL
  - 失败: 404 错误

### 8. `shorten`
- **描述**: 生成短链接
- **请求方法**: POST
- **请求地址**: `http://localhost:8000/shorten`
- **请求体**:
  ```json
  {
    "url": "https://example.com"
  }
  ```
- **响应示例**:
  ```json
  {
    "short_url": "http://localhost:8000/abc123",
    "status": "success"
  }
  ```