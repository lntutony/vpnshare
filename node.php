<?php
session_start();

// 检查用户是否已登录
if (!isset ($_SESSION['user_logged_in'])) {
    header('Location: /'); // 未登录则重定向到根目录
    exit();
}

// 检查userLevel是否存在并且不为空
if (!isset ($_SESSION['userLevel']) || empty ($_SESSION['userLevel'])) {
    header('Location: /welcome'); // userLevel为空也重定向到根目录
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




if (isset($_GET['action']) && $_GET['action'] == 'convertNodes' && isset($_GET['urls'])) {
    // 直接使用URL参数，但在构建API请求前进行编码
    $urls = $_GET['urls'];
    
    // 对URL进行编码
    $encodedUrls = urlencode($urls);

   // 接收用户选择的 API 接口
    $selectedApi = isset($_GET['apiSelect']) ? $_GET['apiSelect'] : 'defaultApi';
    
    // 根据选择的 API 构建请求 URL
    switch ($selectedApi) {
        case 'api1':
            $apiUrl = "https://api-suc.0z.gs/sub?target=clash&url={$encodedUrls}&insert=false";
            break;
        case 'api2':
            $apiUrl = "https://apiurl.v1.mk/sub?target=clash&url={$encodedUrls}&insert=false&config=https%3A%2F%2Fraw.githubusercontent.com%2FACL4SSR%2FACL4SSR%2Fmaster%2FClash%2Fconfig%2FACL4SSR_Online_Full.ini";
            break;
        default:
            $apiUrl = "https://api-suc.0z.gs/sub?target=clash&url={$encodedUrls}&insert=false";
            break;
    }
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
$query = "SELECT nodeID, nodeName, nodeInfo, NodeLevel, nodeDes, nodeReg, updated_at FROM nodes ORDER BY nodeLevel DESC";

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
<audio id="buttonSound" src="/media/anvil_use.wav"></audio>
<div id="pagination"></div>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>节点池</title>
<link rel="stylesheet" href="css/nodeCss.css">
</head>

<body>
            <div class="user-level-display">
   <h2> 您的用户等级为 <?php echo $userLevel; ?>，可以使用 <?php echo $userLevel; ?> 级及以下的节点。</h2>
</div>
    节点池为精选显示，例如在BitzNet的众多香港节点中，只呈现性能最优者。


    <form id="nodeForm">
        <table border="1">
            <tr>
                <th>选择</th>
                <th>ID</th>
                <th>名称</th>
               <!--<th>详细信息(省略)</th>--> 
                <th>等级</th>
                <th>备注</th>
                <th>Region
                <img src="/media/服务地球.png" alt="" style="vertical-align: middle; margin-right: 6px; height: 24px;">
                </th>
                <th>更新时间</th>
            </tr>
<?php foreach ($nodes as $node): ?>
<tr>
    <td>
<input type="checkbox" name="node[]" value="<?php echo htmlspecialchars($node['nodeID']); ?>"
       data-fullinfo="<?php echo htmlspecialchars($node['nodeInfo']); ?>"
       data-level="<?php echo htmlspecialchars($node['NodeLevel']); ?>"
       <?php echo $userLevel < $node['NodeLevel'] ? 'disabled' : ''; ?>>

    </td>
    <td>
        <?php echo htmlspecialchars($node['nodeID']); ?>
    </td>
    <td>
        <?php echo htmlspecialchars($node['nodeName']); ?>
    </td>
       <!--<td>
        <?php
        // 检查用户等级是否小于节点等级
        if ($userLevel < $node['NodeLevel']) {
            echo "您无权查看此节点"; // 用户等级小于节点等级时显示的信息
        } else {
            echo mb_strimwidth(htmlspecialchars($node['nodeInfo']), 0, 15, "..."); // 否则显示节点信息
        }
        ?>
    </td>-->
    <td>
        <?php echo htmlspecialchars($node['NodeLevel']); ?>
    </td>
    <td>
        <?php echo htmlspecialchars($node['nodeDes']); ?>
    </td>
    <td>
        <?php echo htmlspecialchars($node['nodeReg']); ?>
    </td>
    <td>
        <?php echo htmlspecialchars($node['updated_at']); ?>
    </td>
</tr>
<?php endforeach; ?>



        </table>
        <!--  <button type="button" id="copyButton">复制选中的节点信息</button> -->
        <button type="button" id="convertButton" class="button">
        
    Let's build it!
    <img src="/media/扳手1.png" alt="" style="vertical-align: middle; margin-right: 6px; height: 24px;">
    
    
</button>

<form id="nodeForm" class="api-selection-form">
    <label for="apiSelect" class="label-api-select">选择接口:</label>
    <select id="apiSelect" name="apiSelect" class="select-api-select">
        <option value="api1">默认接口</option>
        <option value="api2">全分组接口</option>
        <!-- 更多接口选项可以添加在这里 -->
    </select>
    <!-- 现有表单内容 -->
</form>



        <button type="button" id="selectAllButton" class="button">全选</button>
        <button type="button" id="backButton" class="button">返回</button>
    
    
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