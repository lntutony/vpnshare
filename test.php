<?php
session_start(); // 启用会话

// 初始化消息变量，用于给用户反馈信息
$message = "";
$displayCode = false;  // 控制是否显示注册码

// 处理提交的答案
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $answers = [
        'question1' => 'C',
        'question2' => 'B',
        'question3' => 'C',
        'question4' => 'D',
        'question5' => 'A'
    ];

    $correct = true;
    $incorrect_questions = [];  // 收集错误题号
    foreach ($answers as $question => $answer) {
        if (!isset($_POST[$question]) || $_POST[$question] !== $answer) {
            $correct = false;
            $incorrect_questions[] = $question;  // 仅记录错误的题号
        }
    }

    if ($correct && !isset($_SESSION['regCodeShown'])) {
        $displayCode = true;  // 用户答题正确，准备显示注册码
        $_SESSION['regCodeShown'] = true;  // 设置会话变量
    } elseif (!$correct) {
        $message = "回答错误，请重试。错误的题目: " . implode(", ", $incorrect_questions);  // 显示错误的题号
    }
}

// 显示 regCode，只有在答案全部正确的情况下且未显示过注册码
if ($displayCode) {
    $conn = new mysqli('localhost', 'sub', 'ACaxib3r3haeT72n', 'sub');
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }

    $query = "SELECT * FROM reg WHERE isShow = 'false' LIMIT 1";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $message = "回答正确！您的注册码是: " . $row['regCode'];

        $updateQuery = $conn->prepare("UPDATE reg SET isShow = 'true', showTime = NOW() WHERE regCode = ?");
        $updateQuery->bind_param("s", $row['regCode']);
        $updateQuery->execute();
        $updateQuery->close();
    } else {
        $message = "抱歉，注册码没有了。";
    }

    $conn->close();
} elseif (isset($_SESSION['regCodeShown'])) {
    $message = "您已经获取过一次注册码。";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test Questions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form {
            background: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
        }
        div.message {
            background: #ccc;
            padding: 10px;
            margin-bottom: 20px;
            color: #333;
        }
        button {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <?php if ($message): ?>
    <div class="message">
        <?= $message ?>
        <?php if ($displayCode && isset($row['regCode'])): ?>
            <!-- 显示返回注册界面的按钮，并自动填充注册码 -->
            <a href="register.php?regcode=<?= urlencode($row['regCode']) ?>" class="register-link">返回注册</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <form action="test.php" method="post">
        <p>问题 1: 您可以通过以下哪些方式联系Tony?</p>
        <input type="radio" name="question1" value="A" required>微信
        <input type="radio" name="question1" value="B">QQ
        <input type="radio" name="question1" value="C">指定邮件(admin@tonyun.net)

        <p>问题 2: 当你通过境内联系方式联系Tony时</p>
        <input type="radio" name="question2" value="A" required>Tony会帮助你解决问题
        <input type="radio" name="question2" value="B">Tony不会回复
        <input type="radio" name="question2" value="C">Tony会报警

        <p>问题 3: 当你分享你的账户时会？</p>
        <input type="radio" name="question3" value="A" required>收到警告邮件
        <input type="radio" name="question3" value="B">什么也不会发生
        <input type="radio" name="question3" value="C">账户被封禁

        <p>问题 4: 当您不能正常使用时(如连接超时)</p>
        <input type="radio" name="question4" value="A" required>通过境内聊天软件联系Tony
        <input type="radio" name="question4" value="B">更新订阅
        <input type="radio" name="question4" value="C">什么也不做,等待修复
        <input type="radio" name="question4" value="D" required>前往官网(sub.tonyun.net)重新构建并导入

        <p>问题 5: 非用户原因导致连接异常</p>
        <input type="radio" name="question5" value="A" required>您会获得相应补偿
        <input type="radio" name="question5" value="B">您什么也不会获得
        <input type="radio" name="question5" value="C">您只能等待，不会获得临时订阅

        <button type="submit">提交答案</button>
    </form>
</body>
</html>
