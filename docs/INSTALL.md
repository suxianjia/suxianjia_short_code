# 项目安装说明

## 环境要求
- PHP 7.4+
- Composer 2.0+
- MySQL 5.7+

## 安装步骤
1. 克隆仓库
   ```bash
   git clone <repo-url>
   ```
2. 安装依赖
   ```bash
   composer install
   ```
3. 配置环境
   ```bash
   cp config/app.conf.example config/app.conf
   ```
4. 初始化数据库
   ```bash
   php bin/install
   ```

## 启动服务
```bash
php bin/start
```