<?php
session_start();

// 检查用户是否已登录
if (!isset($_SESSION['user_logged_in'])) {
    header('Location: /'); // 未登录则重定向到根目录
    exit();
}

include 'db.php';
function generateNewToken($length = 10) {
    $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// 检查是否有更新Token的请求
if(isset($_POST['updateToken']) && $_POST['updateToken'] == 'true') {
    $newToken = generateNewToken(); // 调用函数生成新的token
    $username = $_SESSION['username']; // 从会话获取用户名

    // 更新数据库中的token
    $stmt = $db->prepare("UPDATE users SET token = ? WHERE username = ?");
    $stmt->bind_param("ss", $newToken, $username);
    $result = $stmt->execute();

    if ($result) {
        $_SESSION['token'] = $newToken; // 更新会话中的token
        echo "令牌重置成功，请刷新页面";
    } else {
        echo "Error updating token.";
    }
    exit; // 终止脚本运行，因为这是一个AJAX请求的响应
}

// 获取当前登录用户的username
$username = $_SESSION['username'];

// 查询数据库以获取最新的token
$sql = "SELECT token FROM users WHERE username = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $_SESSION['token'] = $row['token']; // 更新会话中的token
}

include 'pay1.php';
?>


<!DOCTYPE html>
<html lang="zh-CN">
<meta charset="UTF-8">
<!-- 模态框 -->
<div id="myModal"
    style="display:none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgb(0,0,0); background-color: rgba(0,0,0,0.4);">
    <div
        style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px;">
        <h2>服务条款</h2>
        <div style="height: 150px; overflow-y: auto; margin-bottom: 20px; border: 1px solid #ccc; padding: 10px;">
            为确保沟通的顺畅与遵守相关法律法规，本服务条款特别强调以下几点：
            <br><br>&nbsp;&nbsp;&nbsp;&nbsp;
            1.用户必须使用<b>境外/指定邮箱</b>通讯工具与Tony进行联络。任何通过<b>境内</b>通讯工具发起的联系，我们将不保证响应或服务
            <br>&nbsp;&nbsp;&nbsp;&nbsp;

            2. 若用户选择使用<b>境内</b>通讯工具与Tony进行沟通，Tony有权根据情况决定是否回应，并保留<b>在不事先通知的情况下终止向相关用户提供服务的权利。</b>
            <br>&nbsp;&nbsp;&nbsp;&nbsp;
            3.根据统计数据分析，用户在使用我们服务过程中遇到的问题，约有90%是由<b>用户端</b>的设置或配置错误引起。在遭遇使用障碍时，用户首先应尝试<b>更新订阅</b>来解决问题。此项动作在大多数情况下能够有效地恢复服务正常使用。
            <br>&nbsp;&nbsp;&nbsp;&nbsp;
            4.<b>禁止共享账户。</b>为维持服务的安全性与公平性，Tony执行严格的日志分析监控程序，旨在保证账户的个别使用。若经调查确认有账户共享行为，Tony将依据服务条款采取相应措施，包括但不限于暂停或终止向涉及用户提供服务，以保护所有用户的合法权利。我们同时要求所有用户<b>遵守当地的法律法规。</b>

            <br><br>
            我们诚挚地希望每位用户能理解并遵守这些规定，以帮助我们提供更安全、高效的服务。
        </div>
        <button id="agreeBtn"
            style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; cursor: pointer; font-weight: bold;">同意</button>
        <button id="disagreeBtn"
            style="background-color: #f44336; color: white; padding: 10px 20px; border: none; cursor: pointer; font-weight: bold; margin-left: 10px;">不同意</button>

    </div>
</div>
 <!-- 充值模态框 -->
    <div id="rechargeModal" style="display:none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
        <div style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 300px;">
            <h2>请输入充值码</h2>
            <form method="post">
                <input type="text" name="rechargeCode" required>
                <button type="submit">提交充值</button>
            </form>
            <button onclick="closeRechargeModal()">关闭</button>
        </div>
    </div>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tonyの共同賃貸システム</title>
     <link rel="stylesheet" href="css/welcome.css">
</head>

<body>
        <div class="blink-notice">
        <p class="blink-text">注:订阅链接每月1日自动重置</p>
    </div>
    <div class="container">
        <!--  <h1>Tonyの共同賃貸システム</h1>-->
        <h1>欢迎回来,
            <?php echo htmlspecialchars($_SESSION['username']); ?>!
        </h1>
        <p>您的订阅链接是：</p>
        <div class="sublink-container" style="display: flex; align-items: center; justify-content: center; gap: 10px;">
            <div class="sublink" id="sublinkText">
                <?php
                // 检查token是否为空
                if (empty ($_SESSION['token'])) {
                    // 如果token为空，直接显示sublink字段
                    echo htmlspecialchars($_SESSION['sublink']);
                } else {
                    // 如果token不为空，构建并显示新的URL
                    $baseUrl = "https://sub.tonyun.net/link/download.php";
                    $params = "name=" . urlencode($_SESSION['username']) . "&token=" . urlencode($_SESSION['token']);
                    $url = $baseUrl . "?" . $params;

                    // 输出拼接的URL
                    echo htmlspecialchars($url);
                }
                ?>

            </div>
<button id="showTokenButton" style="margin-right: 5px; margin-top: 20px;background-color: transparent; border: none; cursor: pointer;">
    <img src="https://sub.tonyun.net/media/eyeyincang.png" alt="隐藏Token" style="vertical-align: middle; margin-right: 1px; width: 30px;">
</button>
            <button id="copyButton" style="background-color: transparent; border: none; cursor: pointer;">
                <img src="https://sub.tonyun.net/media/copy.png" alt="Logo"
                    style="vertical-align: middle; margin-right: 14px; width: 30px;">
            </button>
            
        </div>
        <p>到期时间:
            <?php echo htmlspecialchars($_SESSION['expiry_date']); ?>
        </p>
        <?php
        $baseUrl = "https://sub.tonyun.net/link/download.php";
        $params = "name=" . urlencode($_SESSION['username']) . "&token=" . urlencode($_SESSION['token']);
        $newSublink = $baseUrl . "?" . $params;
        $encodedNewSublink = urlencode($newSublink); // 对整个$newSublink进行URL编码
        ?>

        <a href="clash://install-config?url=<?php echo $encodedNewSublink; ?>" class="contact-button"
            style="background-color: #28a745;">
            一键导入到
            <img src="https://sub.tonyun.net/media/Clash_Logo.png" alt="Logo"
                style="vertical-align: middle; margin-right: 8px; width: 20px;">
            <img src="https://sub.tonyun.net/media/ss.png" alt="Logo"
                style="vertical-align: middle; margin-right: 8px; width: 20px;">
        </a>

        <a href="https://sub.tonyun.net/log" class="contact-button" target="_blank">日志查看</a>
        <a href="serve_video.php?video=Untitled.mp4" class="contact-button" target="_blank">使用教程</a>

      <button id="updateTokenButton" style="background-color: #343a40; padding: 10px 20px; color: white; border: none; border-radius: 5px; cursor: pointer;">重置令牌</button>
       <button id="rechargeButton" onclick="showRechargeModal()" class="contact-button">充值</button>
        <?php if (!empty($_SESSION['userLevel'])): ?>
    <a href="node" id="convertButton" class="contact-button">DIY节点
    <img src="/media/扳手1.png" alt="" style="vertical-align: middle; margin-right: 6px; height: 24px;">
    
    </a>
    <button id="signInButton" class="contact-button">签到</button>
<?php else: ?>
    <button class="contact-button" disabled>DIY节点</button>
<?php endif; ?>
        <button id="contactTonyBtn" class="contact-button">联系Tony</button>
        <button id="backButton">退出</button>
    </div>
    <div id="contactModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <a href="https://t.me/o1dbiden"><img src="https://sub.tonyun.net/media/tg.png" alt="Logo"
                    style="vertical-align: middle; margin-right: 16px; width: 40px;"></a>
            <a href="mailto:admin@tonyun.net"><img src="https://sub.tonyun.net/media/mail.png" alt="Logo"
                    style="vertical-align: middle; margin-right: 16px; width: 40px;"></a> <br>
            <a href="sms:+85261381416"><img src="https://sub.tonyun.net/media/sms.jpg" alt="Logo"
                    style="vertical-align: middle; margin-right: 16px; width: 40px;"></a>
        </div>
    </div>

 <script src="js/welcome.js"></script>
    <footer
        style="position: fixed; left: 45%; bottom: 0; transform: translateX(-50%); width: auto; text-align: center; padding: 10px; font-size: 0.8em;">
        
        </p>
        <p>
            Created by <a href="https://t.me/o1dbiden" target="_blank"
                style="color: #007bff; text-decoration: underline;">Tony</a>
            &amp;
            <a href="https://chat.openai.com/" target="_blank"
                style="color: #007bff; text-decoration: underline;">ChatGPT-4</a>
            <br>
            Powered by <a href="https://www.aliyun.com/" target="_blank"
                style="color: #007bff; text-decoration: underline;">Aliyun</a>
            <br>
            <span id="daysRunning">网站运行天数：加载中...</span>
            <br>
            &copy; 2024 Tonyun. 版权所有.
    </footer>
   <script src="js/tc.js"></script>
</body>
</html>