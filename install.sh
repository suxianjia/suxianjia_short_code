#!/bin/bash
#  .env.test
# .env.prod
# .env.dev
# ======================================================
# // cd /Users/yx-dev/work_directory/wwwroot/composer_xianjia_root/xianjia_short_code && composer dump-autoload
# ======================================================
composer_dump() {
    echo "正在自动载入"
    composer dump-autoload
    echo "自动载入 end"
}
# ======================================================
# 错误处理函数
# ======================================================
fatal() {
    echo "错误: $1" >&2
    exit 1
}

# ======================================================
# 检查或创建 .env 文件
# ======================================================
check_or_create_env() {
    if [ ! -f ".env" ]; then
        echo "未找到 .env 文件，正在创建空文件..."
        touch .env
        echo "空 .env 文件已创建。"
    else
        echo ".env 文件已存在。"
    fi
}

# ======================================================
# 数据库连接检查
# ======================================================
check_db_connection() {
    mysql -h "$1" -P "$2" -u "$3" -p"$4" -e "USE $5;" 2>/dev/null || fatal "无法连接数据库 $1:$2"
    echo "数据库连接成功: $1:$2"
    echo "DB_HOST=$1" >> .env
    echo "DB_PORT=$2" >> .env
    echo "DB_USER=$3" >> .env
    echo "DB_PASS=$4" >> .env
    echo "DB_NAME=$5" >> .env

}
# ======================================================
# Redis连接检查
# ======================================================
check_redis_connection() {
    redis-cli -h "$1" -p "$2" ${3:+-a "$3"} ping 2>/dev/null | grep -q "PONG" || fatal "无法连接Redis $1:$2"
    echo "Redis连接成功: $1:$2"
}
# ======================================================
# 确保目录存在且可写
# ======================================================
ensure_dir() {
    echo "=== 确保目录存在且可写 ==="
    echo "=== $1"
    ls $1  
    if   [ -d "$1" ]; then 
        echo "$1  目录已经存在"
    else 
         echo "$1  目录不存在"
        #  跳过
        return 1
    fi 

    echo "==="
    if [ -z "$1" ]; then 
        echo "目录不能为空,创建目录 $1"
        mkdir -p "$1" || fatal "无法创建目录 $1"
        chmod 755 "$1" || fatal "无法设置目录权限 $1"
        [ -w "$1" ] || fatal "目录不可写 $1"
    else
        chmod 755 "$1" || fatal "无法设置目录权限 $1"
        [ -w "$1" ] || fatal "目录不可写 $1"  
    fi 
}
# ======================================================
# 获取非空输入
# ======================================================
get_input() {
    local prompt="$1" default="$2"
    read -p "$prompt" value
    echo "${value:-$default}"
}
# ======================================================
# 配置数据库主库
# ======================================================
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



        # 数据库配置
    echo "# ===数据库主库配置" >> .env
    echo "DB_HOST=$db_host" >> .env
    echo "DB_PORT=$db_port" >> .env
    echo "DB_NAME=$db_name" >> .env
    echo "DB_USER=$db_user" >> .env
    echo "DB_PASS=$db_pass" >> .env
}
# ======================================================
# 配置Redis主库
# ======================================================
config_redis_master() {
    echo "#  === Redis主库配置 ==="
    redis_host=$(get_input "Redis主机（默认：127.0.0.1）: " "127.0.0.1")
    redis_port=$(get_input "Redis端口（默认：6379）: " "6379")
    [[ "$redis_port" =~ ^[0-9]+$ ]] || fatal "端口必须是数字"
    
    read -p "Redis密码（可选）: " redis_pass
echo
    
    check_redis_connection "$redis_host" "$redis_port" "$redis_pass"

        # Redis配置
    echo "# ===Redis主库配置" >> .env
    echo "REDIS_HOST=$redis_host" >> .env
    echo "REDIS_PORT=$redis_port" >> .env
    [ -n "$redis_pass" ] && echo "REDIS_PASS=$redis_pass" >> .env
 # Redis配置
}
  
# ======================================================
# 生成nginx配置
# ======================================================
generate_nginx_conf() {
    cat > nginx.conf <<EOL
# nginx.conf
server {
    listen 80;
    server_name $domain;
    root $PUBLIC_DIR;

    access_log $LOG_DIR/access.log;
    error_log $LOG_DIR/error.log;

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
echo "# === 生成nginx配置 nginx.conf"
echo "# === 生成nginx配置 nginx.conf" >> .env

}

# ======================================================
# 启动服务
# ======================================================
start_server() {
    echo "=== 启动web服务 ==="
    ./start.sh
}

# ======================================================
# 关闭服务
# ======================================================
stop_server() {
    echo "=== 关闭web服务 ==="
    ./stop.sh
}
 

# ======================================================
# client
# ======================================================
clientstart() {
    # echo "\n"
    echo " === 启动客户端测试 ==="
    echo " === php demo/client.php ===" 
    php demo/client.php
    # echo "\n"
}

# ======================================================
# 数据库初始化
# ======================================================
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

# ======================================================
# 安装主方法 main()   执行完比后 增加 install.lock，下次检测install.lock 如果存在则提示并停止
# ======================================================
main() {

        php -v

    # 检测 install.lock 文件是否存在
    if [ -f "install.lock" ]; then
        echo "检测到 install.lock 文件，系统已安装。"
        read -p "是否继续安装？如需重新安装，请先删除 install.lock 并清空 .env 文件 (y/n): " choice
        case "$choice" in
            y|Y )
                echo "正在删除 install.lock 文件并清空 .env 文件..."
                rm -f install.lock
                > .env
                echo "已删除 install.lock 并清空 .env 文件，继续安装流程。"
                ;;
            * )
                echo "安装已取消。"
                exit 0
                ;;
        esac
    fi
    # 获取当前目录
    webpath=$(pwd)

    file -I ${webpath}/install.sh 
    # 配置参数
    DEFAULT_PORT=8000
    DEFAULT_DOMAIN=example.com
    LOG_DIR="${webpath}/logs"
    PUBLIC_DIR="${webpath}/public"
    CONFIG_DIR="${webpath}/config" 
    MAX_LOG_DAYS=7
    echo "=== 目录权限 === $PUBLIC_DIR ====="
    ensure_dir $PUBLIC_DIR


    # 检查或创建 .env 文件
    echo "=== 检查或创建 .env 文件 ==="
    check_or_create_env
         # 日志配置
    echo "# === 日志配置" >> .env
    echo "LOG_DIR=$LOG_DIR" >> .env
    echo "=== 目录权限 === $LOG_DIR ====="
    ensure_dir $LOG_DIR 



    # 用户输入部分
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

    echo "# ===环境配置" >> .env
    echo "APP_ENV=$APP_ENV" >> .env

    # 用户输入端口
    read -p "请输入端口号（默认: 8000）: " input_port
    port=${input_port:-$DEFAULT_PORT}

    # 检查端口合法性
    if ! [[ "$port" =~ ^[0-9]+$ ]] || [ "$port" -lt 1024 ] || [ "$port" -gt 65535 ]; then
        echo "错误：端口号必须是1024-65535之间的数字"
        exit 1
    fi

    # 检查并终止占用端口的进程
    if lsof -i :$port > /dev/null; then
        echo "端口 $port 已被占用，尝试关闭服务..."
        pkill -f "php -S localhost:$port" || {
            echo "无法自动关闭占用端口的进程，请手动处理"
            exit 1
        }
        sleep 1
    fi

    # 检查public目录
    if [ ! -d "$PUBLIC_DIR" ]; then
        echo "警告：未找到$PUBLIC_DIR目录，将使用项目根目录"
        PUBLIC_DIR="."
    fi

    echo "=== 域名输入 ==="
    domain=$(get_input "项目域名（例如：example.com）: " "")
    [ -z "$domain" ] && fatal "域名不能为空"



    echo "# === web配置 ======" >> .env
    echo "PUBLIC_DIR=$PUBLIC_DIR" >> .env
    echo "PORT=$port" >> .env 
    echo "DOMAIN=$domain" >> .env
    echo "CONFIG_DIR=$CONFIG_DIR" >> .env 
    



    
    # 服务配置信息
    echo "=== 服务 db /redis  配置信息 ==="
    config_db_master
    config_redis_master
    
    # 生成配置文件
    echo "=== 生成配置文件 _nginx_conf ===" 
    generate_nginx_conf

    # 数据库初始化
    echo "=== 数据库初始化 ==="
    init_db_sql
    

    # 创建 install.lock 文件
    touch install.lock
    echo "安装完成，锁文件已创建：install.lock"


    # composer_dump
    echo "=== composer_dump ==="
    echo "Composer Dump Autoloader"
    composer_dump


    # 启动服务
    echo "=== 启动web服务 ==="
    start_server
    
    # 启动客户端测试
    echo "=== 启动客户端测试 ==="
    clientstart


}

# 执行入口
main