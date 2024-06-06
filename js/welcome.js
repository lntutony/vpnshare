

document.getElementById('signInButton').addEventListener('click', function() {
    window.location.href = 'signin'; // 跳转到签到页面
});

        function showRechargeModal() {
            document.getElementById('rechargeModal').style.display = 'block';
        }

        function closeRechargeModal() {
            document.getElementById('rechargeModal').style.display = 'none';
        }




    document.getElementById('updateTokenButton').addEventListener('click', function() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "welcome.php?action=updateToken", true);

    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if (this.status == 200) {
            alert(this.responseText); // 显示服务器响应
        }
    };
    xhr.send("updateToken=true");
});

        // 弹窗逻辑
        var modal = document.getElementById('contactModal');
        var btn = document.getElementById('contactTonyBtn');
        var span = document.getElementsByClassName('close')[0];

        btn.onclick = function () {
            modal.style.display = "block";
        }

        span.onclick = function () {
            modal.style.display = "none";
        }

        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }


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

        // 获取返回按钮
        var backButton = document.getElementById('backButton');

        // 点击返回按钮返回根目录
        backButton.addEventListener('click', function () {
            window.location.href = '/'; // 重定向到根目录
        });


        // 修改订阅链接显示
        var sublinkElement = document.getElementById('sublinkText');
        var originalSublink = sublinkElement.textContent || sublinkElement.innerText; // 获取原始订阅链接
        var maskedSublink = originalSublink.replace(/token=[^&]+/, 'token=********'); // 将token后的字符替换为星号
        sublinkElement.textContent = maskedSublink; // 更新显示的订阅链接为带星号的版本
// 定义一个变量来跟踪Token是否显示
var isTokenShown = false;

        function showToken() {
    var sublinkElement = document.getElementById('sublinkText');
    var showTokenButton = document.getElementById('showTokenButton');
    var imgElement = showTokenButton.getElementsByTagName('img')[0]; // 获取按钮中的<img>元素

    if (!isTokenShown) {
        // 显示完整的Token
        sublinkElement.textContent = originalSublink;
        imgElement.src = "https://sub.tonyun.net/media/eyexianshi.png"; // 更改为“显示”图标
        imgElement.alt = "显示Token"; // 更新alt属性
        isTokenShown = true;
    } else {
        // 隐藏Token
        sublinkElement.textContent = maskedSublink;
        imgElement.src = "https://sub.tonyun.net/media/eyeyincang.png"; // 更改为“隐藏”图标
        imgElement.alt = "隐藏Token"; // 更新alt属性
        isTokenShown = false;
    }
}

// 给按钮添加点击事件监听
document.getElementById('showTokenButton').addEventListener('click', showToken);



        // 修改复制按钮的点击事件
        var copyButton = document.getElementById('copyButton');
        copyButton.addEventListener('click', function () {
            // 创建一个临时的input元素来存放原始订阅链接
            var tempInput = document.createElement('input');
            tempInput.value = originalSublink; // 使用原始订阅链接
            document.body.appendChild(tempInput);
            tempInput.select(); // 选择文本
            document.execCommand('copy'); // 执行复制命令
            document.body.removeChild(tempInput); // 删除临时input元素
            alert('订阅链接已复制到剪贴板✅✅'); // ：显示提示信息
        });
