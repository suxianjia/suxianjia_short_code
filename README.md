# 仙家短链系统

## 项目概述
仙家短链系统是一个高性能的URL缩短服务，支持：
- 长链接转短链接
- 短链接访问统计
- 分布式部署

## 项目结构
```
├── config/                  # 配置文件
├── database/                # 数据库脚本
│   └── migrations/         # 数据库迁移文件
├── demo/                    # 示例代码
├── doc/                     # 文档
├── examples/                # 示例配置
├── log/                     # 日志目录
├── src/                     # 核心源代码
│   ├── Cache/               # 缓存组件
│   ├── Config/              # 配置管理
│   ├── Controller/          # 控制器
│   ├── Core/                # 核心组件
│   ├── Driver/              # 数据库驱动
│   ├── Factory/             # 工厂类
│   ├── Interface/           # 接口定义
│   ├── Middleware/          # 中间件
│   ├── Model/               # 数据模型
│   ├── Server/              # 服务端实现
│   ├── Services/            # 业务服务
│   ├── Util/                # 工具类
│   └── Validator/           # 验证器
├── tests/                   # 测试代码
├── .env                     # 环境配置
├── composer.json            # 依赖配置
├── nginx.conf               # Nginx配置
└── server.php               # 服务入口
```

## 快速开始
1. 安装依赖：`composer install`
2. 配置数据库：修改`.env`文件
3. 启动服务：`php server.php`

## 开发指南
- 代码规范：遵循PSR-12标准
- 提交前请运行：`./pre-commit`


```
所有 Controller 中 ，所有 public 的方法 全部都返回  Response ， 并更新相关的类与方法。 



```

## 许可证
MIT License