<?php
session_start();

// 检查用户是否已登录
if (!isset($_SESSION['user_logged_in'])) {
    header('Location: /'); // 未登录则重定向到根目录
    exit();
}

$video = basename($_GET['video']); // 获取请求的视频文件名，并防止目录遍历
$videoPath = __DIR__ . '/video/' . $video; // 定义视频文件的完整路径

// 检查文件是否存在并限定文件类型为MP4
if (file_exists($videoPath) && substr($videoPath, -4) === '.mp4') {
    header('Content-Type: video/mp4');
    readfile($videoPath); // 输出视频内容
    exit();
} else {
    header("HTTP/1.0 404 Not Found");
    echo "文件不存在。";
    exit();
}
?>
