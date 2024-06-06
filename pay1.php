<?php
session_start();

// 数据库连接配置
$host = 'localhost';
$port = '3306';
$dbname = 'sub';
$username = 'sub';
$password = 'ACaxib3r3haeT72n';

// 创建数据库连接
$conn = new mysqli($host, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 检查充值表单是否提交
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['rechargeCode'])) {
    $rechargeCode = $_POST['rechargeCode'];

    // 防止SQL注入
    $rechargeCode = $conn->real_escape_string($rechargeCode);

    // 检查充值码是否存在且未使用
    $query = "SELECT * FROM `key` WHERE `key` = '$rechargeCode' AND isUsed = false";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $keyLevel = $row['keyLevel'];

        // 根据keyLevel决定userLevel和expiry_date的增加
        if ($keyLevel == 1) {
            $expiryDays = 30;
            $userLevel = 2;
            $message = "月卡充值成功！等级升级为2！";
        } elseif ($keyLevel == 2) {
            $expiryDays = 90;
            $userLevel = 3;
            $message = "季卡充值成功！等级升级为3！";
        } else {
            echo "<script>alert('未知的充值卡类型！');</script>";
            exit;
        }

        // 更新key表
        $updateKey = "UPDATE `key` SET usedTime = NOW(), isUsed = true, usedBy = '" . $_SESSION['username'] . "' WHERE `key` = '$rechargeCode'";
        $conn->query($updateKey);

        // 获取当前用户名
        $username = $_SESSION['username'];

        // 更新users表
        $updateUser = "UPDATE users SET expiry_date = DATE_ADD(expiry_date, INTERVAL $expiryDays DAY), userLevel = $userLevel WHERE username = '$username'";
        $conn->query($updateUser);

        echo "<script>alert('$message');</script>";
    } else {
        echo "<script>alert('无效的充值码或已被使用！');</script>";
    }
}

// 这里可以添加其他的PHP逻辑或HTML输出
?>

