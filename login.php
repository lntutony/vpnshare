<?php
session_start(); // 开启session

// 连接数据库配置
$host = 'localhost';
$db_user = 'sub';
$db_password = 'ACaxib3r3haeT72n';
$db_name = 'sub';

// 创建数据库连接
$conn = new mysqli($host, $db_user, $db_password, $db_name);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 从表单获取用户名和密码
$username = $_POST['username'];
$password = $_POST['password']; // 用户输入的密码

// 防止SQL注入
$username = $conn->real_escape_string($username);

// 更新SQL查询，同时获取到期日期
$sql = "SELECT password, sublink, expiry_date,token,userLevel FROM users WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // 使用password_verify()验证密码
    if (password_verify($password, $row['password'])) {
        // 验证成功，保存用户信息到session
        $_SESSION['user_logged_in'] = true;
        $_SESSION['username'] = $username; // 保存用户名到session
        $_SESSION['sublink'] = $row['sublink']; // 保存sublink到session
        $_SESSION['expiry_date'] = $row['expiry_date']; // 保存到期日期到session

        $_SESSION['token'] = $row['token']; // 保存token到session
        $_SESSION['userLevel'] = $row['userLevel']; // userLevel
        // 跳转到另一个页面
        header('Location: welcome');
        exit(); // 确保脚本停止执行
    } else {
        echo "密码错误";
    }
} else {
    echo "用户名不存在";
}
$conn->close();
?>
