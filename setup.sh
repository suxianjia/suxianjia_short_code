#!/bin/bash
#  .env.test
# .env.prod
# .env.dev

# // cd /Users/yx-dev/work_directory/wwwroot/composer_xianjia_root/xianjia_short_code && composer dump-autoload

# 错误处理函数
fatal() {
    echo "错误: $1" >&2
    exit 1
}

# 启动服务
start_server() {
    echo "=== 启动服务 ==="
    ./start.sh
}

# 数据库初始化
init_db_sql() {
    if [ -f "database/short-url.sql" ]; then
        echo "=== 数据库初始化 ==="
        mysql -h"$db_host" -P"$db_port" -u"$db_user" -p"$db_pass" "$db_name" < database/short-url.sql
        if [ $? -ne 0 ]; then
            echo "数据库初始化失败，请检查数据库配置和SQL文件内容。"
            while true; do
                read -p "是否重试？(y/n): " retry
                case $retry in
                    [Yy]* ) mysql -h"$db_host" -P"$db_port" -u"$db_user" -p"$db_pass" "$db_name" < database/short-url.sql; break;;
                    [Nn]* ) exit 1;;
                    * ) echo "请输入 y 或 n";;
                esac
            done
        else
            echo "数据库初始化成功。"
        fi
    else
        echo "未找到 database/short-url.sql 文件，跳过数据库初始化。"
    fi
}

# 数据库连接检查
check_db_connection() {
    mysql -h "$1" -P "$2" -u "$3" -p"$4" -e "USE $5;" 2>/dev/null || fatal "无法连接数据库 $1:$2"
    echo "数据库连接成功: $1:$2"
    echo "DB_HOST=$1" >> .env
    echo "DB_PORT=$2" >> .env
    echo "DB_USER=$3" >> .env
    echo "DB_PASS=$4" >> .env
    echo "DB_NAME=$5" >> .env
}

# Redis连接检查
check_redis_connection() {
    redis-cli -h "$1" -p "$2" ${3:+-a "$3"} ping 2>/dev/null | grep -q "PONG" || fatal "无法连接Redis $1:$2"
    echo "Redis连接成功: $1:$2"
}

# 确保目录存在且可写
ensure_dir() {
    mkdir -p "$1" || fatal "无法创建目录 $1"
    chmod 755 "$1" || fatal "无法设置目录权限 $1"
    [ -w "$1" ] || fatal "目录不可写 $1"
}

# 获取非空输入
get_input() {
    local prompt="$1" default="$2"
    read -p "$prompt" value
    echo "${value:-$default}"
}

# 配置数据库主库
config_db_master() {
    echo "#  === 数据库主库配置 ==="
    db_host=$(get_input "数据库主机（默认：localhost）: " "localhost")
    db_port=$(get_input "数据库端口（默认：3306）: " "3306")
    [[ "$db_port" =~ ^[0-9]+$ ]] || fatal "端口必须是数字"
    
    db_name=$(get_input "数据库名称: " "")
    [ -z "$db_name" ] && fatal "数据库名称不能为空"
    
    db_user=$(get_input "数据库用户名: " "")
    [ -z "$db_user" ] && fatal "用户名不能为空"
    
    read -s -p "数据库密码: " db_pass
echo
    
    check_db_connection "$db_host" "$db_port" "$db_user" "$db_pass" "$db_name"
}

# 配置Redis主库
config_redis_master() {
    echo "#  === Redis主库配置 ==="
    redis_host=$(get_input "Redis主机（默认：127.0.0.1）: " "127.0.0.1")
    redis_port=$(get_input "Redis端口（默认：6379）: " "6379")
    [[ "$redis_port" =~ ^[0-9]+$ ]] || fatal "端口必须是数字"
    
    read -p "Redis密码（可选）: " redis_pass
echo
    
    check_redis_connection "$redis_host" "$redis_port" "$redis_pass"
}

# 生成.env文件
generate_env() {
    echo "# ===环境配置" > .env
    echo "APP_ENV=$APP_ENV" >> .env
    echo "# ===域名配置" >> .env
    echo "DOMAIN=$domain" >> .env
    
    # 数据库配置
    echo "# ===数据库主库配置" >> .env
    echo "DB_HOST=$db_host" >> .env
    echo "DB_PORT=$db_port" >> .env
    echo "DB_NAME=$db_name" >> .env
    echo "DB_USER=$db_user" >> .env
    echo "DB_PASS=$db_pass" >> .env
    
    # Redis配置
    echo "# ===Redis主库配置" >> .env
    echo "REDIS_HOST=$redis_host" >> .env
    echo "REDIS_PORT=$redis_port" >> .env
    [ -n "$redis_pass" ] && echo "REDIS_PASS=$redis_pass" >> .env
    
    # 日志配置
    echo "# === 日志配置" >> .env
    echo "LOG_DIR=$log_dir" >> .env
}

# 生成nginx配置
generate_nginx_conf() {
    cat > nginx.conf <<EOL
# nginx.conf
server {
    listen 80;
    server_name $domain;
    root $PWD;

    access_log $log_dir/access.log;
    error_log $log_dir/error.log;

    index index.php index.html;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php\$ {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
    }
}
EOL
}






# 启动服务
webstart() {
     echo "   === 服务启动 ==="
     echo "\n"
    echo " === 服务启动 ==="
    if [ -f "bin/server.php" ]; then
       php bin/server.php && echo "启动成功" || fatal "启动失败"
    else
         echo "\n"
        fatal "未找到 bin/server.php 文件，请确保项目已正确安装"
    fi
}


# client
clientstart() {
    echo "\n"
    echo " === 启动客户端测试 ==="
    echo " === php demo/client.php ===" 
    php demo/client.php
    echo "\n"
}


# 主流程
main() {

    php -v 
    # 环境选择
    echo "===请选择环境："
    echo "1. 开发环境 (dev)"
    echo "2. 测试环境 (test)"
    echo "3. 生产环境 (prod)"
    read -p "输入选项（1-3）: " env_option
    
    case $env_option in
        1) APP_ENV="dev" ;;
        2) APP_ENV="test" ;;
        3) APP_ENV="prod" ;;
        *) APP_ENV="dev"; echo "使用默认开发环境" ;;
    esac
    
      echo "=== 域名输入 ==="
    # 域名输入
    domain=$(get_input "项目域名（例如：example.com）: " "")
    [ -z "$domain" ] && fatal "域名不能为空"
    

          echo "=== 目录配置 ==="
    # 目录配置
    log_dir=$(get_input "日志目录（默认：logs）: " "logs")
    ensure_dir "$log_dir"
    ensure_dir "config"
    
    # 服务配置信息
        echo "=== 服务配置信息 ==="
    config_db_master
    config_redis_master
    
    # 生成配置文件
        echo "=== 生成配置文件 ==="
    generate_env
    generate_nginx_conf

    # 数据库初始化
        echo "=== 数据库初始化 ==="
    init_db_sql
    
    # 启动服务
       echo "=== 启动web服务 ==="
    webstart
      

     # 启动客户端测试 
       echo "=== 启动客户端测试 ==="
     clientstart



}

# 执行入口
main