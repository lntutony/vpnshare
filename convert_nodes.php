<?php
if (isset($_GET['urls'])) {
    // 解码URL参数以获取节点信息
    $encodedUrls = $_GET['urls'];
    $decodedUrls = urldecode($encodedUrls);

    // 构建API请求的URL
    $apiUrl = "https://api-suc.0z.gs/sub?target=clash&url={$decodedUrls}&insert=false";

    // 使用cURL进行API请求
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $apiResponse = curl_exec($ch);
    curl_close($ch);

    if ($apiResponse === false) {
        die("API调用失败");
    }

    // 将API响应的内容保存为临时文件
    $tempFilePath = tempnam(sys_get_temp_dir(), 'nodes_') . '.txt';
    file_put_contents($tempFilePath, $apiResponse);

    // 设置适当的HTTP头部以下载文件
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($tempFilePath) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($tempFilePath));

    // 输出文件内容以下载
    readfile($tempFilePath);

    // 删除临时文件
    unlink($tempFilePath);

    exit;
} else {
    die("缺少必要的URL参数");
}
?>
