<?php
ob_end_clean(); // 如果之前有启动输出缓冲，则结束它
// 设置数据库连接信息
$dbName = 'sub';
$dbUser = 'sub';
$dbPassword = 'ACaxib3r3haeT72n';
$host = 'localhost'; // 如果数据库服务器不在本地，请修改此处

try {
    // 创建PDO实例来连接数据库
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $dbUser, $dbPassword);
    // 设置错误模式为异常
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}

// 验证是否接收到了'name'和'token'参数
if (isset($_GET['name']) && isset($_GET['token'])) {
    $name = $_GET['name'];
    $token = $_GET['token'];

    // 准备SQL查询，验证用户名和令牌
    $sql = "SELECT * FROM users WHERE username = :name AND token = :token LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':name' => $name, ':token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // 如果用户验证失败
    if (!$user) {
        echo "无效的用户名或令牌。";
        exit;
    }

    // 用户验证成功后，进行用户代理检查
    // 假定文件存储在当前目录下，并以用户名命名
    $filePath = '/www/wwwroot/sub.tonyun.net/link/' . $name;


    // 检查文件是否存在
    if (!file_exists($filePath)) {
        echo "文件不存在。";
        exit;
    }

    // 在此处包含用户代理检查逻辑
   include('../check_user_agent.php'); // 确保这个路径是正确的

    // 文件存在，且用户代理检查通过后（如果check_user_agent.php允许执行到此处的话），设置HTTP头部以发送文件
    
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($name) . '"'); // 使用用户名作为文件名
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));
    flush(); // 清空输出缓冲
    // 在发送文件之前添加自定义响应头
error_log("Before setting Subscription-Userinfo header\n", 3, "/www/wwwroot/sub.tonyun.net/logs/my_custom_log.log");
header('Subscription-Userinfo: upload=50947544; download=5769113572; total=107374182400; expire=1717143968');
error_log("After setting Subscription-Userinfo header\n", 3, "/www/wwwroot/sub.tonyun.net/logs/my_custom_log.log");


    readfile($filePath);
    exit;
} else {
    echo "缺少必要的参数。";
}
?>
