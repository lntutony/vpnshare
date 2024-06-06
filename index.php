<?php
// 获取用户IP地址
$userIP = $_SERVER['REMOTE_ADDR'];

// 您的ipinfo.io API令牌
$token = '9df700e0128897';

// 构建请求URL
$url = "https://ipinfo.io/{$userIP}?token={$token}";

// 使用file_get_contents调用API（确保allow_url_fopen在php.ini中启用）
$response = file_get_contents($url);

// 解析返回的JSON数据
$data = json_decode($response, true);

// 检查地区是否等于Liaoning, Hebei, 或Shanghai
// 当地区为Shanghai时，org必须为AS56044 China Mobile communications corporation
if (
    $data['region'] !== 'Liaoning' &&
    $data['region'] !== 'Hebei' &&
    ($data['region'] !== 'Shanghai' || $data['org'] !== 'AS56044 China Mobile communications corporation') &&
    $data['org'] !== 'AS60068 Datacamp Limited' &&
    $data['org'] !== 'AS24547 Hebei Mobile Communication Company Limited' &&
    $data['org'] !== 'AS4760 HKT Limited'
) {
    // 如果不符合条件，重定向到block.php，并传递用户IP和ASN信息
    header("Location: /block.php?ip={$userIP}&asn=" . urlencode($data['org']) . "&region=" . urlencode($data['region']));
    exit;
}
?>

<!doctype html>
<html>
    
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- 添加视口元标签 -->
    <title>Tonyの共同賃貸システム</title>
     <link rel="stylesheet" href="css/index.css">
 <script>
 
    function checkBrowser() {
        var ua = navigator.userAgent;
        var isWeixin = /MicroMessenger/i.test(ua);
        var isQQ = /QQ/i.test(ua);
        // 如果在微信或QQ内置浏览器中打开，则显示警告
        if (isWeixin || isQQ) {
            alert("禁止用微信内置浏览器打开此页面");
            // 重定向到一个新的页面，这个页面可以告诉用户为什么需要在浏览器中打开
            window.location.href = 'weixin.html'; // 'browser_advice.html'是一个假设的页面
        }
    }
    
  // 设置网站启动的日期（例如：2023年1月1日）
  const startDate = new Date('2024-01-01T00:00:00');

  function updateDaysRunning() {
    // 获取当前日期和时间
    const now = new Date();

    // 计算当前时间与网站开始时间之间的差异（毫秒）
    const diff = now - startDate;

    // 将差异转换为天数
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

    // 更新页面上的显示
    document.getElementById('daysRunning').textContent = `已稳定运行：${days}天 ${hours}小时 ${minutes}分钟 ${seconds}秒`;
  }

  // 每秒更新一次运行时间
  setInterval(updateDaysRunning, 1000);
    
</script>
</head>
<body onload="checkBrowser()">
    
    <div class="container">
        <h1>Tonyの合租系统</h1>
        <form action="login.php" method="post">
            <input type="text" name="username" placeholder="用户名" required>
            <input type="password" name="password" placeholder="密码" required>
            <div class="button-container">
                 <input type="submit" value="登录">
                <!-- 注册按钮 -->
                <button type="button" onclick="location.href='register.php'">注册</button>
                <!-- 登录按钮 -->
               
            </div>
        </form>
    </div>
    

<footer style="position: fixed; left: 45%; bottom: 0; transform: translateX(-50%); width: auto; text-align: center; padding: 10px; font-size: 0.8em;">
    <p>
        Created by <a href="https://t.me/o1dbiden" target="_blank" style="color: #007bff; text-decoration: underline;">Tony</a>
        &amp;
        <a href="https://chat.openai.com/" target="_blank" style="color: #007bff; text-decoration: underline;">ChatGPT-4</a>
        <br>
        Powered by <a href="https://www.aliyun.com/" target="_blank" style="color: #007bff; text-decoration: underline;">Aliyun</a>
        <br>
        <span id="daysRunning">网站运行天数：加载中...</span>
        <br>
     
    </p>
</footer>
</body>
</html>