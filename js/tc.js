window.onload = function () {
    // 检查localStorage中是否已经有'hasAgreed'这个键
    if (!localStorage.getItem('hasAgreed')) {
        // 显示模态框
        document.getElementById('myModal').style.display = 'block';

        // 处理“同意”按钮点击事件
        document.getElementById('agreeBtn').onclick = function () {
            document.getElementById('myModal').style.display = 'none';
            // 用户同意条款，继续浏览网页
            localStorage.setItem('hasAgreed', true); // 设置'hasAgreed'在localStorage中标记为true
        };

        // 处理“不同意”按钮点击事件
        document.getElementById('disagreeBtn').onclick = function () {
            // 用户不同意条款，重定向到百度
            window.location.href = 'https://www.baidu.com';
        };
    }
};
