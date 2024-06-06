<?php
$host = '127.0.0.1'; // 本机地址
$dbname = 'sub'; // 数据库名
$user = 'sub'; // 数据库用户名
$password = 'ACaxib3r3haeT72n'; // 数据库密码
$port = '3306'; // 端口

// 尝试连接数据库
try {
    $db = new mysqli($host, $user, $password, $dbname, $port);
    if ($db->connect_error) {
        die("连接失败: " . $db->connect_error);
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    exit('数据库连接失败');
}
?>
