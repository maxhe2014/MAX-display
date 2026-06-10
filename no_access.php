<?php
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>无访问权限</title>
    <style>
        :root {
            --text-white: rgba(255, 255, 255, 1);
            --text-secondary: rgba(107, 114, 128, 1);
            --display-bg: rgba(7, 19, 36, 1);
            --primary: rgba(15, 98, 254, 1);
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
        }
        .no-access-container {
            text-align: center;
            padding: 60px 40px;
            max-width: 500px;
        }
        .no-access-icon {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(239, 68, 68, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
        }
        .no-access-icon svg {
            width: 60px;
            height: 60px;
            color: #ef4444;
        }
        .no-access-title {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 16px;
        }
        .no-access-desc {
            font-size: 16px;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .no-access-ip {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 20px;
            font-size: 14px;
        }
        .no-access-ip-label {
            color: var(--text-secondary);
            margin-bottom: 8px;
        }
        .no-access-ip-value {
            font-weight: bold;
            color: #ef4444;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="no-access-container">
        <div class="no-access-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18.364 18.364A9 9 0 115.636 5.636a9 9 0 0112.728 12.728z"/>
                <path d="M12 9v4M12 17h.01"/>
            </svg>
        </div>
        <h1 class="no-access-title">无访问权限</h1>
        <p class="no-access-desc">您的IP地址不在允许访问的白名单中。如需访问，请联系管理员将您的IP添加到白名单。</p>
        <div class="no-access-ip">
            <div class="no-access-ip-label">当前IP地址：</div>
            <div class="no-access-ip-value"><?php echo htmlspecialchars(getClientIp()); ?></div>
        </div>
    </div>
</body>
</html>
