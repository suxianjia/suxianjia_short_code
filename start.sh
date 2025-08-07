
#!/bin/bash

# 从 .env 文件加载变量
if [ -f .env ]; then
    source .env
fi

# 读取变量值或要求用户输入
if [ -z "$DEFAULT_PORT" ]; then
    read -p "请输入端口号（默认: 8000）: " input_port
    DEFAULT_PORT=${input_port:-8000}
fi

port=$DEFAULT_PORT

if [ -z "$LOG_DIR" ]; then
    read -p "请输入日志目录（默认: logs）: " input_log_dir
    LOG_DIR=${input_log_dir:-logs}
fi

if [ -z "$PUBLIC_DIR" ]; then
    read -p "请输入公共目录（默认: public）: " input_public_dir
    PUBLIC_DIR=${input_public_dir:-public}
fi

if [ -z "$MAX_LOG_DAYS" ]; then
    read -p "请输入日志保留天数（默认: 7）: " input_max_log_days
    MAX_LOG_DAYS=${input_max_log_days:-7}
fi

# 检查端口合法性
if ! [[ "$DEFAULT_PORT" =~ ^[0-9]+$ ]] || [ "$DEFAULT_PORT" -lt 1024 ] || [ "$DEFAULT_PORT" -gt 65535 ]; then
    echo "错误：端口号必须是1024-65535之间的数字"
    exit 1
fi

# 检查并终止占用端口的进程
if lsof -i :$DEFAULT_PORT > /dev/null; then
    echo "端口 $DEFAULT_PORT 已被占用，尝试关闭服务..."
    pkill -f "php -S localhost:$DEFAULT_PORT" || {
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

# 创建日志目录
mkdir -p "$LOG_DIR" || {
    echo "无法创建日志目录"
    exit 1
}

# 生成 start.ini 文件
echo "DEFAULT_PORT=$DEFAULT_PORT" > start.ini
echo "LOG_DIR=$LOG_DIR" >> start.ini
echo "PUBLIC_DIR=$PUBLIC_DIR" >> start.ini
echo "MAX_LOG_DAYS=$MAX_LOG_DAYS" >> start.ini

# 清理旧日志
find "$LOG_DIR" -name "*.log" -mtime +$MAX_LOG_DAYS -exec rm {} \;

# 准备日志文件
current_date=$(date +"%Y-%m-%d")
log_file="$LOG_DIR/server.log"

# 写入环境变量到 .env 文件
 





echo "配置已保存到 .env 文件"
dated_log_file="$LOG_DIR/server-$current_date.log"
error_log_file="$LOG_DIR/error-$current_date.log"
pid_file="$LOG_DIR/pid.log"

# 启动信息
echo -e "\n\033[32m启动信息:\033[0m"
echo "项目目录: $webpath"
echo "文档根目录: $PUBLIC_DIR"
echo "访问地址: http://localhost:$port/"
echo "标准日志: $log_file"
echo "日期日志: $dated_log_file"
echo "错误日志: $error_log_file"
echo "PID记录: $pid_file"

# 启动PHP服务器
nohup php -S localhost:$port -t "$PUBLIC_DIR" >> "$log_file" 2>> "$error_log_file" &
server_pid=$!

# 记录PID到文件
echo "$server_pid" > "$pid_file"

# 写入启动日志
echo -e "\n[$(date '+%Y-%m-%d %H:%M:%S')] 服务器启动 (PID: $server_pid)" | tee -a "$log_file" "$dated_log_file"


ps aux | grep php



# 显示实时日志
tail -f "$log_file" "$error_log_file" | while read line; do
    echo "$line" | tee -a "$dated_log_file"
done



# 捕获退出信号
trap "echo '停止服务器...'; kill $server_pid; rm -f \"$pid_file\"; exit 0" INT TERM EXIT
