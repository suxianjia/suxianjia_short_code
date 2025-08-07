#!/bin/bash

# 停止服务的脚本

# 1. 从 logs/pid.log 读取 PID 并终止进程
echo "正在停止服务..."
PID_FILE="logs/pid.log"

if [ -f "$PID_FILE" ]; then
  PID=$(cat "$PID_FILE")
  if [ -n "$PID" ] && ps -p "$PID" > /dev/null; then
    kill -9 "$PID"
    echo "已终止进程: $PID"
    rm "$PID_FILE"
  else
    echo "未找到有效的进程 ID 或进程已终止。"
    rm "$PID_FILE"
  fi
else
  echo "未找到 PID 文件: $PID_FILE"
fi

echo "服务已停止。"