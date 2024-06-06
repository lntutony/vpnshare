<?php
session_start();

if (!isset($_SESSION['user_logged_in'])) {
    header('Location: /');
    exit();
}

$host = 'localhost';
$dbUsername = 'sub';
$dbPassword = 'ACaxib3r3haeT72n';
$dbName = 'sub';

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    die('连接失败: ' . $conn->connect_error);
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$userPoints = 0;
$expiryDate = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['signin'])) {
        processSignIn($conn, $username);
    } elseif (isset($_POST['redeem'])) {
        processRedeem($conn, $username);
    } elseif (isset($_POST['lottery'])) {
        processLottery($conn, $username);
    }
    updateSessionData($conn, $username);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

updateSessionData($conn, $username);
$conn->close();

function updateSessionData($conn, $username) {
    $stmt = $conn->prepare("SELECT points, expiry_date FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    global $userPoints, $expiryDate;
    if ($row = $result->fetch_assoc()) {
        $userPoints = $row['points'];
        $expiryDate = $row['expiry_date'];
    }
    $stmt->close();
}

function processSignIn($conn, $username) {
    $stmt = $conn->prepare("SELECT points, last_signin_date FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $lastSigninDate = $row['last_signin_date'];
        $today = date('Y-m-d');
        if ($lastSigninDate != $today) {
            $updatedPoints = $row['points'] + 1;
            $stmt = $conn->prepare("UPDATE users SET points = ?, last_signin_date = ? WHERE username = ?");
            $stmt->bind_param("iss", $updatedPoints, $today, $username);
            $stmt->execute();
            $_SESSION['message'] = '签到成功！您的积分已增加。';
        } else {
            $_SESSION['message'] = '您今天已经签到过了！';
        }
    }
    $stmt->close();
}

function processRedeem($conn, $username) {
    $stmt = $conn->prepare("SELECT points, expiry_date FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if ($row['points'] >= 10) {
            $updatedPoints = $row['points'] - 10;
            $currentExpiryDate = new DateTime($row['expiry_date'] ?: 'now');
            if ($currentExpiryDate < new DateTime()) {
                $currentExpiryDate = new DateTime(); // Reset to today if expired or not set
            }
            $currentExpiryDate->modify('+3 days');
            $newExpiryDate = $currentExpiryDate->format('Y-m-d');
            $stmt = $conn->prepare("UPDATE users SET points = ?, expiry_date = ? WHERE username = ?");
            $stmt->bind_param("iss", $updatedPoints, $newExpiryDate, $username);
            $stmt->execute();
            $_SESSION['message'] = '兑换成功！您的服务时间已延长。';
        } else {
            $_SESSION['message'] = '积分不足以兑换服务时间！';
        }
    }
    $stmt->close();
}

function processLottery($conn, $username) {
    $stmt = $conn->prepare("SELECT points, expiry_date FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if ($row['points'] >= 3) {
            $updatedPoints = $row['points'] - 3;  // 扣除3积分
            $currentExpiryDate = new DateTime($row['expiry_date'] ?: 'now');
            if ($currentExpiryDate < new DateTime()) {
                $currentExpiryDate = new DateTime(); // 从今天开始计算，如果之前没有有效日期
            }

            // 随机决定奖励或惩罚
            $rand = rand(1, 4); // 生成一个1到4的随机数
            $changeDays = 0;
            if ($rand == 1) {
                $changeDays = 1; // 增加1天
            } elseif ($rand == 2 || $rand == 3) {
                $changeDays = -3; // 扣除3天
            } else {
                $changeDays = 3; // 增加3天
            }
            $currentExpiryDate->modify("{$changeDays} days");

            $newExpiryDate = $currentExpiryDate->format('Y-m-d');
            $stmt = $conn->prepare("UPDATE users SET points = ?, expiry_date = ? WHERE username = ?");
            $stmt->bind_param("iss", $updatedPoints, $newExpiryDate, $username);
            $stmt->execute();

            $actionResult = $changeDays > 0 ? '增加了' : '减少了';
            $_SESSION['message'] = "抽奖完成！您的积分现在是 {$updatedPoints}，服务到期时间{$actionResult} " . abs($changeDays) . " 天。";
        } else {
            $_SESSION['message'] = '积分不足以参加抽奖！';
        }
    }
    $stmt->close();
}


?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>签到页面</title>
    <link rel="stylesheet" type="text/css" href="css/signin.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9357972628734144"
     crossorigin="anonymous"></script>
    <script>
        window.onload = function() {
            <?php if (isset($_SESSION['message'])) {
                echo "alert('" . $_SESSION['message'] . "');";
                unset($_SESSION['message']); // 清除消息以防止再次显示
            } ?>
        }
        function goBack() {
    window.location.href = "/welcome";
}
    </script>
</head>
<body>
    <h1>欢迎签到, <?php echo htmlspecialchars($username); ?>!</h1>
    <p>您当前的积分是: <?php echo htmlspecialchars($userPoints); ?></p>
    <p>您的服务到期时间是: <?php echo htmlspecialchars($expiryDate); ?></p>  <!-- 显示过期日期 -->
    <div class="container">
    
    <h2>抽奖规则：</h2>
    <ul>
        <li><strong>消耗积分：</strong>每次抽奖需要消耗 <span class="highlight">3个积分</span>。</li>
        <li><strong>奖项设置：</strong>
            <ul>
                <li><strong>50%</strong> 的概率获得 <strong>1天</strong> 使用时间</li>
                <li><strong>25%</strong> 的概率获得 <strong>3天</strong> 使用时间</li>
                <li><strong>25%</strong> 的概率减少 <strong>3天</strong> 使用时间</Each day, a poster reminding me of a project, a birthday, or something fun></li>
            </ul>
        </li>
    </ul>
</div>
    <!-- 签到逻辑处理的表单 -->
    <form method="post" action="">
        <input type="hidden" name="signin" value="true">
        <button type="submit">点击签到</button>
    </form>
    <!-- 积分兑换服务时间的表单 -->
    <form method="post" action="">
        <input type="hidden" name="redeem" value="true">
        <button type="submit">使用10积分兑换3天服务时间</button>
    </form>
    <form method="post" action="">
    <input type="hidden" name="lottery" value="true">
    <button type="submit">参加抽奖</button>
    
</form>
<button onclick="goBack()" class="back-button">返回主页</button>
</body>
</html>
