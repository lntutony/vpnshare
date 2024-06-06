<?php
session_start();

// 设定一个静态的密码哈希值，实际应用中应该是从数据库中获取
$hash = password_hash("123456", PASSWORD_DEFAULT);

function isLoggedIn() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if (password_verify($_POST['password'], $hash)) {
        $_SESSION['is_admin'] = true;
    } else {
        echo '<script>alert("密码错误!");</script>';
    }
}

if (!isLoggedIn()) {
    // 显示登录表单
    echo '<!DOCTYPE html><html><head><title>管理员登录</title></head><body>
          <form method="post" action="admin.php">
              <label for="password">密码:</label>
              <input type="password" name="password" id="password">
              <input type="submit" value="登录">
          </form>
          </body></html>';
    exit(); // 防止在未登录时显示管理界面
}
$host = '127.0.0.1';
$db_user = 'sub';
$db_password = 'ACaxib3r3haeT72n';
$db_name = 'sub';

// 创建数据库连接
$conn = new mysqli($host, $db_user, $db_password, $db_name);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 添加新用户
if (isset($_POST['add'])) {
    // 这里应有输入验证（略）
    $username = $_POST['username'];
    $p_name = $_POST['p_name'];
    $p_access = $_POST['p_access'];
    // ...处理其他字段...
    $stmt = $conn->prepare("INSERT INTO users (username) VALUES (?)");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->close();
    header('Location: admin.php'); // 重定向避免重复提交
    exit();
}

// 更新用户信息
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $username = $_POST['username'];
    $p_name = $_POST['p_name'];
    $p_access = $_POST['p_access'];
    $sublink = $_POST['sublink'];
    $userLevel = $_POST['userLevel'];
    $stmt = $conn->prepare("UPDATE users SET  sublink = ?,userLevel = ? WHERE username = ?");
    $stmt->bind_param('sss',  $sublink, $userLevel,$username);
    if (!$stmt->execute()) {
        echo "更新失败: " . $conn->error;
    }
    $stmt->close();
}


// 删除用户信息
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $username = $_POST['username'];
    $stmt = $conn->prepare("DELETE FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    if (!$stmt->execute()) {
        echo "删除失败: " . $conn->error;
    }
    $stmt->close();
}
// 获取用户列表
$result = $conn->query("SELECT username, sublink,userLevel FROM users");


?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>管理员控制台</title>
    
     <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        h1 {
            color: #333;
            text-align: center;
            margin: 20px 0;
        }
        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        input[type="text"] {
            width: 100%;
            padding: 6px;
            margin: 4px 0;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
    
    
</head>
<body>
    <h1>管理员控制台</h1>
    <!-- 添加新用户表单略 -->
    <table>
        <thead>
            <tr>
                <th>用户名</th>
                
                <th>Sublink</th>
                <th>用户等级</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <form action="admin.php" method="post">
                    <td><input type="text" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" readonly></td>
                  
                    <td><input type="text" name="sublink" value="<?php echo htmlspecialchars($row['sublink']); ?>"></td>
                    <td><input type="text" name="userLevel" value="<?php echo htmlspecialchars($row['userLevel']); ?>"></td>
                    <td>
                        <input type="submit" name="update" value="更新">
                        <input type="submit" name="delete" value="删除" onclick="return confirm('确定删除?');">
                    </td>
                </form>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>