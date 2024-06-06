<?php
session_start(); // 确保已经开启了session

// 数据库连接配置
$host = '127.0.0.1'; // 数据库服务器地址
$db_user = 'sub'; // 数据库用户名
$db_password = 'ACaxib3r3haeT72n'; // 数据库密码
$db_name = 'sub'; // 数据库名

// 创建数据库连接
$conn = new mysqli($host, $db_user, $db_password, $db_name);

// 检查连接是否成功
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 获取Cloudflare Turnstile响应
$turnstile_secret = '0x4AAAAAAAVfs5Kl9L4oot7PmMpA2XSU0KA'; // 替换为您的Cloudflare Turnstile私钥
$turnstile_response = $_POST['cf-turnstile-response'];

// 使用cURL验证Turnstile响应
$verify_url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
$data = http_build_query([
    'secret' => $turnstile_secret,
    'response' => $turnstile_response,
]);
$ch = curl_init($verify_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$response = curl_exec($ch);
curl_close($ch);
$response_data = json_decode($response);

if (!$response_data->success) {
    die('Turnstile验证失败。请返回重新验证');
}

// 获取用户输入
$username = $_POST['username'];
$password = $_POST['password'];
$regcode = $_POST['regcode']; // 获取注册码
$sublink = $_POST['sublink'];
$token = $_POST['token'];

// 检查注册码是否有效
$regCodeQuery = $conn->prepare("SELECT * FROM reg WHERE regCode = ? AND isUsed = 0");
$regCodeQuery->bind_param("s", $regcode);
$regCodeQuery->execute();
$regCodeResult = $regCodeQuery->get_result();
if ($regCodeResult->num_rows == 0) {
    echo "注册码无效或已被使用。";
    $regCodeQuery->close();
    $conn->close();
    exit;
}
$regCodeQuery->close();

// 使用password_hash()函数对密码进行加密
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// 计算到期日期，假设从当前时间开始加30天
$expiry_date = date('Y-m-d H:i:s', strtotime("+30 days"));

// 检查用户名是否已存在
$query = $conn->prepare("SELECT * FROM users WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();
if ($result->num_rows > 0) {
    echo "用户名已存在，请选择其他用户名。";
    $query->close();
    $conn->close();
    exit;
}
$query->close();

// 插入新用户，同时设置到期日期
$stmt = $conn->prepare("INSERT INTO users (username, password, sublink, expiry_date, token) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $username, $hashed_password, $sublink, $expiry_date, $token);
if ($stmt->execute()) {
    // 更新注册码状态
$updateRegCode = $conn->prepare("UPDATE reg SET isUsed = '1', usedBy = ? WHERE regCode = ?");
    $updateRegCode->bind_param("ss", $username, $regcode);
    $updateRegCode->execute();
    $updateRegCode->close();
    
    // 读取start文件的内容
    $startFilePath = 'link/start'; // 假设start文件位于link目录
    $startFileContent = file_get_contents($startFilePath);
    if ($startFileContent === false) {
        echo "读取start文件失败";
    } else {
        // 创建新文件的路径
        $newFilePath = 'link/' . $username; // 使用用户名作为文件名
        if (file_put_contents($newFilePath, $startFileContent) === false) {
            echo "创建用户文件失败";
        } else {
            echo "<script>alert('注册成功！即将跳转到首页。'); window.location.href='/';</script>";
        }
    }
} else {
    echo "注册失败：" . $stmt->error;
}
$stmt->close();
$conn->close();
?>
