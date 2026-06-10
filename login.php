<?php
require_once __DIR__ . '/config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = '请输入用户名和密码';
    } else {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            header('Location: index.php');
            exit;
        } else {
            $error = '用户名或密码错误';
        }
    }
}

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录 - 企业信息展示平台</title>
    <style>
        :root {
            --text-white: rgba(255, 255, 255, 1);
            --text-secondary: rgba(107, 114, 128, 1);
            --text-primary: rgba(17, 24, 39, 1);
            --primary: rgba(15, 98, 254, 1);
            --display-bg: rgba(7, 19, 36, 1);
            --bg-white: rgba(255, 255, 255, 1);
            --border: rgba(229, 231, 235, 1);
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, var(--display-bg) 0%, rgba(13, 27, 42, 0.95) 100%);
            color: var(--text-white);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-container {
            background: var(--bg-white);
            border-radius: 20px;
            padding: 40px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 16px;
            background: linear-gradient(135deg, var(--primary) 0%, #0e5ce6 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-logo svg {
            width: 32px;
            height: 32px;
            color: var(--text-white);
        }
        .login-title {
            font-size: 24px;
            font-weight: bold;
            color: var(--text-primary);
            margin-bottom: 8px;
        }
        .login-subtitle {
            font-size: 14px;
            color: var(--text-secondary);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 8px;
        }
        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--primary);
        }
        .btn {
            width: 100%;
            padding: 14px 24px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-primary {
            background: var(--primary);
            color: var(--text-white);
        }
        .btn-primary:hover {
            background: #0e5ce6;
            transform: translateY(-1px);
        }
        .error-message {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .login-tip {
            text-align: center;
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 20px;
        }
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 24px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="login-logo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            </div>
            <div class="login-title">企业信息展示平台</div>
            <div class="login-subtitle">请登录以访问后台管理</div>
        </div>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label class="form-label">用户名</label>
                <input type="text" class="form-input" name="username" placeholder="请输入用户名" required>
            </div>
            <div class="form-group">
                <label class="form-label">密码</label>
                <input type="password" class="form-input" name="password" placeholder="请输入密码" required>
            </div>
            <button type="submit" class="btn btn-primary">登录</button>
        </form>
        
        <div class="login-tip">
            默认账号: admin / 123456</div>
    </div>
</body>
</html>
