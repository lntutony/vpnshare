<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>日志查看器</title>
    <style>
        .log-container {
            width: 80%;
            height: 600px;
            overflow-y: scroll;
            margin: 20px auto;
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
            font-family: monospace;
            font-size: 26px;
        }
    </style>
</head>
<body>

<div class="log-container">
<?php
$logFilePath = '/www/wwwroot/sub.tonyun.net/logs/sub.tonyun.net.log'; // 调整为实际的日志文件路径

if (file_exists($logFilePath)) {
    $lines = file($logFilePath);
    $last400Lines = array_slice($lines, -400000);
    $last400Lines = array_reverse($last400Lines);
    foreach ($last400Lines as $line) {
        if (strpos($line, '/link/') !== false) {
            // 解析IP地址
            preg_match('/(\d+\.\d+\.\d+\.\d+)/', $line, $matches);
            $ip = $matches[1];
            
            // 解析用户名称、时间和响应大小
            preg_match('/\[(.*?)\].*?\/link\/download\.php\?name=([^&]+)&token=([^ ]+).*" \d+ (\d+) /', $line, $matches);
            $dateTimeStr = $matches[1];
            $userName = $matches[2];
            $responseSize = $matches[4];

            $dateTime = DateTime::createFromFormat('d/M/Y:H:i:s O', $dateTimeStr);
            if ($dateTime === false) {
                echo "时间格式解析失败，请检查时间字符串格式。";
            } else {
                $formattedDate = $dateTime->format('d/M H:i:s');
            }

            $userAgent = strtolower($line);
            if (strpos($userAgent, 'clash') !== false) {
                $userAgentInfo = "使用<strong>Clash</strong>下载了配置文件";
            } elseif (strpos($userAgent, 'shadowrocket') !== false) {
                $userAgentInfo = "使用了<strong>小火箭</strong>下载了配置文件";
            } elseif (preg_match('/mozilla|chrome|safari|firefox/', $userAgent)) {
                $userAgentInfo = "使用浏览器尝试下载配置文件并拦截";
            } else {
                $userAgentInfo = "使用非白名单UA尝试下载配置文件并拦截";
            }

            // 增加更新成功或失败的信息
            $updateStatus = intval($responseSize) > 1024 ? "更新成功" : "更新失败";

            echo htmlspecialchars($userName) . "在" . htmlspecialchars($formattedDate) . "，IP地址：" . htmlspecialchars($ip) . " " . $userAgentInfo . " — " . $updateStatus . "<br>";
        }
    }
} else {
    echo "日志文件不存在。";
}
?>

</div>

</body>
</html>