<?php
session_start();

// 检查用户是否已登录
if (!isset ($_SESSION['user_logged_in'])) {
    header('Location: /'); // 未登录则重定向到根目录
    exit();
}

// 检查userLevel是否存在并且不为空
if (!isset ($_SESSION['userLevel']) || empty ($_SESSION['userLevel'])) {
    header('Location: /welcome.php'); // userLevel为空也重定向到根目录
    exit();
}
// 从session获取userLevel
$userLevel = isset ($_SESSION['userLevel']) ? (int) $_SESSION['userLevel'] : 0;



$host = "localhost"; // 数据库服务器地址
$username = "sub"; // 数据库用户名
$password = "ACaxib3r3haeT72n"; // 数据库密码
$dbname = "sub"; // 数据库名

// 创建数据库连接
$conn = new mysqli($host, $username, $password, $dbname);
// 检查数据库连接

// 设置数据库连接字符集为utf8mb4
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die ("连接失败: " . $conn->connect_error);
}




// 检查是否是转换节点的请求
if (isset ($_GET['action']) && $_GET['action'] == 'convertNodes' && isset ($_GET['urls'])) {
    // 解码URL参数以获取节点信息
// 直接使用URL参数构建API请求的URL，不进行解码
    $encodedUrls = $_GET['urls'];

    // 构建API请求的URL
    $apiUrl = "https://api-suc.0z.gs/sub?target=clash&url={$encodedUrls}&insert=false";

    // 使用cURL进行API请求
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $apiResponse = curl_exec($ch);
    curl_close($ch);

    if ($apiResponse === false) {
        die ("API调用失败");
    }
    $linkDir = __DIR__ . '/link'; // 文件保存路径
    $filename = 'conver_temp.txt';
    $filePath = $linkDir . '/' . $filename;

    // 将API响应的内容保存到服务器的link目录下
    if (file_put_contents($filePath, $apiResponse)) {
        // 获取当前登录的用户名
        $username = $_SESSION['username']; // 确保session中有username
        $userFilePath = $linkDir . '/' . $username; // 用户名命名的文件路径

        // 复制conver_temp.txt的内容到用户名命名的文件
        if (copy($filePath, $userFilePath)) {
            echo "获取成功，请更新订阅！";
        } else {
            die ("复制文件失败");
        }
    } else {
        die ("保存文件失败");
    }
    exit;
}



// 基于用户等级构建查询条件
$queryCondition = "WHERE NodeLevel <= $userLevel";

// 构建SQL查询
$query = "SELECT nodeID, nodeName, nodeInfo, NodeLevel, nodeDes,nodeReg FROM nodes $queryCondition";
$result = $conn->query($query);

if ($result === false) {
    // 查询失败，输出错误信息
    die ("查询失败: " . $conn->error);
}

$nodes = array();
while ($row = $result->fetch_assoc()) {
    $nodes[] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>节点池</title>
<link rel="stylesheet" href="css/nodeCss.css">
</head>

<body>
    
    <h2>节点池为精选显示，例如在BitzNet的众多香港节点中，只呈现性能最优者。</h2>
        <div class="user-level-display">
    您的用户等级为 <?php echo $userLevel; ?>，仅展示等级为 <?php echo $userLevel; ?> 级及以下的节点。
</div>

    <form id="nodeForm">
        <table border="1">
            <tr>
                <th>选择</th>
                <th>ID</th>
                <th>名称</th>
                <th>nodeInfo</th>
                <th>等级</th>
                <th>备注</th>
                <th>Region
                <img src="/media/服务地球.png" alt="" style="vertical-align: middle; margin-right: 6px; height: 24px;">
                </th>
            </tr>
            <?php foreach ($nodes as $node): ?>
                <tr>
                    <td><input type="checkbox" name="node[]" value="<?php echo htmlspecialchars($node['nodeID']); ?>"
                            data-fullinfo="<?php echo htmlspecialchars($node['nodeInfo']); ?>"></td>
                    <td>
                        <?php echo htmlspecialchars($node['nodeID']); ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($node['nodeName']); ?>
                    </td>
                    <td>
                        <?php echo mb_strimwidth(htmlspecialchars($node['nodeInfo']), 0, 30, "..."); // 显示前30个字符加省略号  ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($node['NodeLevel']); ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($node['nodeDes']); ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($node['nodeReg']); ?>
                    </td>
                </tr>
            <?php endforeach; ?>

        </table>
        <!--  <button type="button" id="copyButton">复制选中的节点信息</button> -->
        <button type="button" id="convertButton" class="button">
    
    Let's build it!
    <img src="/media/扳手1.png" alt="" style="vertical-align: middle; margin-right: 6px; height: 24px;">
</button>

        <button type="button" id="backButton" class="button">返回</button>
    </form>
    
    <!-- 模态框（Modal） -->
<div id="myModal" class="modal">
    <!-- 模态框内容 -->
    <div class="modal-content">
        <div class="modal-header">
            <span class="close">&times;</span>
            <h2>构建进度</h2>
        </div>
<div class="modal-body">
    <p><img src="media/齿轮.png" alt="Building..." class="gear" /><span id="progressText">请稍候，正在处理您的请求...</span></p>
    <div class="progress-container">
        <div class="progress-bar" id="myBar"></div>
    </div>
</div>

    </div>
</div>
    
<script src="js/node.js"></script>
</body>
</html>