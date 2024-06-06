#!/bin/bash

# 数据库配置
DB_USER="sub"
DB_PASS="ACaxib3r3haeT72n"
DB_NAME="sub"
DB_HOST="localhost"

# 生成一个包含大小写字母的随机字符串
generate_random_string() {
    cat /dev/urandom | tr -dc 'a-zA-Z' | fold -w 10 | head -n 1
}

# 直接在数据库中为所有用户更新token
mysql -u"$DB_USER" -p"$DB_PASS" -h"$DB_HOST" "$DB_NAME" -se "UPDATE users SET token=(SELECT SUBSTRING(MD5(RAND()) FROM 1 FOR 10));"

echo "所有用户的token已更新。"
