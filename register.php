<?php if (isset($_GET['regcode'])): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementsByName("regcode")[0].value = "<?= htmlspecialchars($_GET['regcode']) ?>";
    });
</script>
<?php endif; ?>


<!doctype html>
<html>
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
<head>
    <meta charset="utf-8">
    <title>Register</title>
    <link rel="stylesheet" href="css/register.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>
<body>
    <div class="container">
        <h1>注册新用户</h1>
        <form action="save_registration.php" method="post">
            <input type="text" name="username" placeholder="用户名" required pattern="^[A-Za-z0-9]{6,16}$" title="用户名必须为6-16位的字母或数字">

            <input type="password" name="password" placeholder="密码" required pattern="^[A-Za-z0-9]{6,16}$" title="密码必须为6-16位的字母或数字">
            <input type="text" name="regcode" placeholder="考试通过后获得注册码" required>

            <input type="hidden" name="sublink" value="首次登录需重置令牌">
            <input type="hidden" name="token" value="">
            <div class="cf-turnstile" data-sitekey="0x4AAAAAAAVfsxuUQSSGVkc4"></div>
            <input type="submit" value="注册">
            <a href="/test" class="button-link">前往考试</a>
        </form>
         
    </div>
    <footer style="position: fixed; left: 50%; bottom: 0; transform: translateX(-50%); width: 100%; text-align: center; padding: 10px; font-size: 0.8em;">
        <p>
            Created by <a href="https://t.me/o1dbiden" target="_blank" style="color: #007bff; text-decoration: underline;">Tony</a>
            &amp;
            <a href="https://chat.openai.com/" target="_blank" style="color: #007bff; text-decoration: underline;">ChatGPT-4</a>
            <br>
            Powered by <a href="https://www.aliyun.com/" target="_blank" style="color: #007bff; text-decoration: underline;">Aliyun</a>
        </p>
    </footer>
</body>

<script>
window.onload = function() {
          // 获取同意按钮，并初始时将其禁用
  var agreeBtn = document.getElementById('agreeBtn');
  agreeBtn.disabled = true;
      // 设置30秒后启用按钮的定时器
     // 初始化计时器时间为30秒
  var timeLeft = 30;
    // 每秒更新一次按钮的文本和禁用状态
  var timer = setInterval(function() {
    if(timeLeft <= 0) {
      // 时间到了，更新按钮文本并启用它
      agreeBtn.textContent = '同意';
      agreeBtn.disabled = false;
      clearInterval(timer); // 停止计时器
    } else {
      // 更新按钮文本显示剩余时间
      agreeBtn.textContent = '阅读' + timeLeft + '秒即可继续';
      timeLeft--; // 减少剩余时间
    }
  }, 1000); // 每1000毫秒（即1秒）调用一次
  setTimeout(function() {
    agreeBtn.disabled = false;
  }, 30000); // 30000毫秒后执行，即30秒
  // 显示模态框
  document.getElementById('myModal').style.display = 'block';
  
  // 处理“同意”按钮点击事件
  document.getElementById('agreeBtn').onclick = function() {
    document.getElementById('myModal').style.display = 'none';
    // 用户同意条款，继续浏览网页
  };
  
  // 处理“不同意”按钮点击事件
  document.getElementById('disagreeBtn').onclick = function() {
    // 用户不同意条款，重定向到百度
    window.location.href = window.location.origin;

  };
};
</script>

</html>
