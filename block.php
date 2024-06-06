<!doctype html>
<html lang="zh">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>访问被阻止</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f4f4f4;
            color: #333;
        }
        h1 {
            color: #d9534f;
        }
        p {
            line-height: 1.6;
        }
        a, .notice {
            color: #d9534f;
        }
        .notice {
            font-size: 16px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>访问被限制！🤬</h1>
    <p>您的 IP 地址（<strong><?php echo htmlspecialchars($_GET['ip']); ?></strong>）所在的地理位置（<strong><?php echo htmlspecialchars($_GET['region']); ?></strong>）或者所属的网络服务提供商（<strong><?php echo htmlspecialchars(urldecode($_GET['asn'])); ?></strong>）未被允许访问此网站。</p>
        <div class="notice">
        如果您正在使用VPN，请关闭后重试。
        </div>
    
    <p>如果您认为这是一个错误，请及时通过以下方式联系管理员：<a href="mailto:admin@tonyun.net?subject=问题反馈&body=我的IP地址是：<?php echo htmlspecialchars($_GET['ip']); ?>">联系管理员</a></p>
</body>
</html>
