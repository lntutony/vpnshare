<?php
// 启动会话，如果你需要使用session来存取其他信息的话
// session_start();

// 设置数据库连接信息
$dbName = 'sub';
$dbUser = 'sub';
$dbPassword = 'ACaxib3r3haeT72n';
$host = 'localhost';

try {
    // 创建PDO实例来连接数据库
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $dbUser, $dbPassword);
    // 设置错误模式为异常
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}

// 获取User-Agent
$userAgent = $_SERVER['HTTP_USER_AGENT'];
// 获取请求的文件名和token
$file = $_GET['name'];
$token = $_GET['token'];

// 验证用户和令牌，同时获取expiry_date
$sql = "SELECT expiry_date FROM users WHERE username = :name AND token = :token LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([':name' => $file, ':token' => $token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "无效的用户名或令牌。";
    exit;
}

$expiryDate = $user['expiry_date'];
$expiryTimestamp = strtotime($expiryDate);

// 检查User-Agent是否为微信或QQ
if (strpos($userAgent, 'MicroMessenger') !== false || strpos($userAgent, 'QQ/') !== false) {
    // 如果是微信或QQ，通过JavaScript弹窗提示并退出
    echo "<script>alert('请不要通过微信或QQ打开此链接。'); window.close();</script>";
    exit;
}

// 检查User-Agent是否为Clash或Shadowrocket
if (strpos($userAgent, 'Clash') !== false || strpos($userAgent, 'Shadowrocket') !== false) {
    // 如果是Clash或Shadowrocket，提供文件下载
    $filePath = '/www/wwwroot/sub.tonyun.net/link/' . $file;

    if (file_exists($filePath)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        // 将过期时间设置到响应头中
        header("Subscription-Userinfo: total=107374182400; expire=$expiryTimestamp");
        readfile($filePath);
        exit;
    } else {
        echo '文件不存在。';
        exit;
    }
} else {
    // 如果不是Clash或Shadowrocket，构造并编码重定向URL
    $baseUrl = "https://sub.tonyun.net/link/download.php";
    $fullUrl = $baseUrl . "?name=" . $file . "&token=" . $token;
    $encodedUrl = urlencode($fullUrl);
    $redirectUrl = "clash://install-config?url=$encodedUrl";

    // 使用JavaScript弹窗提示，点击确定后自动重定向到Clash协议的链接
    echo "<!DOCTYPE html>
<html>
<head>
    <title>确认操作</title>
    <script type='text/javascript'>
        alert('请勿在浏览器中打开订阅链接！！');
        window.location.href = '$redirectUrl';
    </script>
</head>
<body>
</body>
</html>";
    exit;
}
?>
