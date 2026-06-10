<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

function getSetting($key, $default = '') {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT value FROM settings WHERE key = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetchColumn();
    return $result !== false ? $result : $default;
}

function setSetting($key, $value) {
    $pdo = getDB();
    $stmt = $pdo->prepare("INSERT INTO settings (key, value) VALUES (?, ?) ON CONFLICT(key) DO UPDATE SET value = excluded.value");
    return $stmt->execute([$key, $value]);
}

function getAllSettings() {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT key, value FROM settings");
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['key']] = $row['value'];
    }
    return $settings;
}

function getAllCarousel() {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT id, image, title, link, start_date as startDate, end_date as endDate, time_type as timeType, custom_start_time as customStartTime, custom_end_time as customEndTime FROM carousel ORDER BY sort_order ASC, id ASC");
    return $stmt->fetchAll();
}

function getAllScheduledContent() {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT id, name, main_title as mainTitle, sub_title as subTitle, welcome, start_date as startDate, end_date as endDate, time_type as timeType, custom_start_time as customStartTime, custom_end_time as customEndTime FROM scheduled_content ORDER BY id ASC");
    return $stmt->fetchAll();
}

function getAllFestivals() {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT id, title, message, start_date as startDate, end_date as endDate FROM festivals ORDER BY id ASC");
    return $stmt->fetchAll();
}

function getAllExternalUrls() {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT id, name, url FROM external_urls ORDER BY sort_order ASC, id ASC");
    return $stmt->fetchAll();
}

function getIpWhitelist() {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT ip FROM ip_whitelist ORDER BY id ASC");
    $ips = [];
    while ($row = $stmt->fetch()) {
        $ips[] = $row['ip'];
    }
    return $ips;
}

function handleUpload() {
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => '文件上传失败'];
    }
    
    $file = $_FILES['file'];
    
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'message' => '文件大小不能超过5MB'];
    }
    
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
        return ['success' => false, 'message' => '只支持图片文件 (JPG, PNG, GIF, WebP)'];
    }
    
    if (!file_exists(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = date('YmdHis') . '_' . uniqid() . '.' . $ext;
    $filepath = UPLOAD_DIR . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'url' => UPLOAD_URL . $filename];
    }
    
    return ['success' => false, 'message' => '文件保存失败'];
}

switch ($action) {
    case 'get_config':
        $config = [
            'carousel' => getAllCarousel(),
            'scheduledContent' => getAllScheduledContent(),
            'festivals' => getAllFestivals(),
            'externalUrls' => getAllExternalUrls(),
            'ipWhitelist' => getIpWhitelist(),
            'settings' => getAllSettings()
        ];
        jsonSuccess(['data' => $config]);
        break;

    case 'get_ip_info':
        $ip = getClientIp();
        jsonSuccess([
            'ip' => $ip,
            'whitelist' => getIpWhitelist()
        ]);
        break;

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonError('请使用POST请求', 405);
        }
        
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            jsonError('请输入用户名和密码');
        }
        
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($password, $user['password'])) {
            jsonError('用户名或密码错误');
        }
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        jsonSuccess(['username' => $user['username'], 'role' => $user['role']]);
        break;

    case 'logout':
        session_destroy();
        jsonSuccess();
        break;

    case 'check_login':
        jsonSuccess([
            'loggedIn' => isLoggedIn(),
            'username' => $_SESSION['username'] ?? null
        ]);
        break;

    case 'upload_image':
        if (!isLoggedIn()) {
            jsonError('未登录', 401);
        }
        
        $result = handleUpload();
        if ($result['success']) {
            jsonSuccess(['url' => $result['url']]);
        } else {
            jsonError($result['message']);
        }
        break;

    case 'update_carousel':
        if (!isLoggedIn()) {
            jsonError('未登录', 401);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonError('请使用POST请求', 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $items = $input['items'] ?? [];
        
        $pdo = getDB();
        $pdo->beginTransaction();
        
        try {
            $pdo->exec("DELETE FROM carousel");
            
            $stmt = $pdo->prepare("INSERT INTO carousel (id, image, title, link, start_date, end_date, time_type, custom_start_time, custom_end_time, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            foreach ($items as $index => $item) {
                $stmt->execute([
                    null,
                    $item['image'] ?? '',
                    $item['title'] ?? '',
                    $item['link'] ?? '#',
                    $item['startDate'] ?? '2020-01-01',
                    $item['endDate'] ?? '2099-12-31',
                    $item['timeType'] ?? 'all_day',
                    $item['customStartTime'] ?? '',
                    $item['customEndTime'] ?? '',
                    $index
                ]);
            }
            
            $pdo->commit();
            jsonSuccess();
        } catch (Exception $e) {
            $pdo->rollBack();
            jsonError('保存失败: ' . $e->getMessage());
        }
        break;

    case 'update_scheduled':
        if (!isLoggedIn()) {
            jsonError('未登录', 401);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonError('请使用POST请求', 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $items = $input['items'] ?? [];
        
        $pdo = getDB();
        $pdo->beginTransaction();
        
        try {
            $pdo->exec("DELETE FROM scheduled_content");
            
            $stmt = $pdo->prepare("INSERT INTO scheduled_content (id, name, main_title, sub_title, welcome, start_date, end_date, time_type, custom_start_time, custom_end_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            foreach ($items as $item) {
                $stmt->execute([
                    null,
                    $item['name'] ?? '未命名',
                    $item['mainTitle'] ?? '',
                    $item['subTitle'] ?? '',
                    $item['welcome'] ?? '',
                    $item['startDate'] ?? date('Y-m-d'),
                    $item['endDate'] ?? date('Y-m-d', strtotime('+7 days')),
                    $item['timeType'] ?? 'all_day',
                    $item['customStartTime'] ?? '',
                    $item['customEndTime'] ?? ''
                ]);
            }
            
            $pdo->commit();
            jsonSuccess();
        } catch (Exception $e) {
            $pdo->rollBack();
            jsonError('保存失败: ' . $e->getMessage());
        }
        break;

    case 'update_festivals':
        if (!isLoggedIn()) {
            jsonError('未登录', 401);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonError('请使用POST请求', 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $items = $input['items'] ?? [];
        
        $pdo = getDB();
        $pdo->beginTransaction();
        
        try {
            $pdo->exec("DELETE FROM festivals");
            
            $stmt = $pdo->prepare("INSERT INTO festivals (id, title, message, start_date, end_date) VALUES (?, ?, ?, ?, ?)");
            
            foreach ($items as $item) {
                $stmt->execute([
                    null,
                    $item['title'] ?? '未命名',
                    $item['message'] ?? '',
                    $item['startDate'] ?? date('Y-m-d'),
                    $item['endDate'] ?? date('Y-m-d', strtotime('+7 days'))
                ]);
            }
            
            $pdo->commit();
            jsonSuccess();
        } catch (Exception $e) {
            $pdo->rollBack();
            jsonError('保存失败: ' . $e->getMessage());
        }
        break;

    case 'update_external':
        if (!isLoggedIn()) {
            jsonError('未登录', 401);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonError('请使用POST请求', 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $items = $input['items'] ?? [];
        
        $pdo = getDB();
        $pdo->beginTransaction();
        
        try {
            $pdo->exec("DELETE FROM external_urls");
            
            $stmt = $pdo->prepare("INSERT INTO external_urls (id, name, url, sort_order) VALUES (?, ?, ?, ?)");
            
            foreach ($items as $index => $item) {
                $stmt->execute([
                    null,
                    $item['name'] ?? '未命名',
                    $item['url'] ?? '',
                    $index
                ]);
            }
            
            $pdo->commit();
            jsonSuccess();
        } catch (Exception $e) {
            $pdo->rollBack();
            jsonError('保存失败: ' . $e->getMessage());
        }
        break;

    case 'update_whitelist':
        if (!isLoggedIn()) {
            jsonError('未登录', 401);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonError('请使用POST请求', 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $ips = $input['ips'] ?? [];
        
        $pdo = getDB();
        $pdo->beginTransaction();
        
        try {
            $pdo->exec("DELETE FROM ip_whitelist");
            
            $stmt = $pdo->prepare("INSERT INTO ip_whitelist (ip) VALUES (?)");
            
            foreach ($ips as $ip) {
                $stmt->execute([$ip]);
            }
            
            $pdo->commit();
            jsonSuccess();
        } catch (Exception $e) {
            $pdo->rollBack();
            jsonError('保存失败: ' . $e->getMessage());
        }
        break;

    case 'update_settings':
        if (!isLoggedIn()) {
            jsonError('未登录', 401);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonError('请使用POST请求', 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $allowedKeys = [
            'carousel_interval', 'scheduled_carousel_interval', 'weather_city',
            'weather_enabled', 'weather_api_key', 'default_greeting'
        ];
        
        foreach ($input as $key => $value) {
            if (in_array($key, $allowedKeys)) {
                setSetting($key, (string)$value);
            }
        }
        
        jsonSuccess();
        break;

    case 'update_appearance':
        if (!isLoggedIn()) {
            jsonError('未登录', 401);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonError('请使用POST请求', 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $allowedKeys = [
            'welcome', 'main_title', 'sub_title', 'background_image',
            'left_logo', 'left_title'
        ];
        
        foreach ($input as $key => $value) {
            if (in_array($key, $allowedKeys)) {
                setSetting($key, (string)$value);
            }
        }
        
        jsonSuccess();
        break;

    case 'get_users':
        if (!isLoggedIn()) {
            jsonError('未登录', 401);
        }
        
        $pdo = getDB();
        $stmt = $pdo->query("SELECT id, username, role, created_at FROM users ORDER BY id ASC");
        $users = $stmt->fetchAll();
        jsonSuccess(['users' => $users]);
        break;

    case 'add_user':
        if (!isLoggedIn()) {
            jsonError('未登录', 401);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonError('请使用POST请求', 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';
        $role = $input['role'] ?? 'admin';
        
        if (empty($username) || empty($password)) {
            jsonError('用户名和密码不能为空');
        }
        
        if (strlen($password) < 6) {
            jsonError('密码长度至少6位');
        }
        
        $pdo = getDB();
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            jsonError('用户名已存在');
        }
        
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $hashed, $role]);
        
        jsonSuccess();
        break;

    case 'delete_user':
        if (!isLoggedIn()) {
            jsonError('未登录', 401);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonError('请使用POST请求', 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $userId = $input['id'] ?? 0;
        
        if ($userId == $_SESSION['user_id']) {
            jsonError('不能删除当前登录的用户');
        }
        
        $pdo = getDB();
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        
        jsonSuccess();
        break;

    case 'change_password':
        if (!isLoggedIn()) {
            jsonError('未登录', 401);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonError('请使用POST请求', 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $oldPassword = $input['oldPassword'] ?? '';
        $newPassword = $input['newPassword'] ?? '';
        
        if (empty($oldPassword) || empty($newPassword)) {
            jsonError('旧密码和新密码不能为空');
        }
        
        if (strlen($newPassword) < 6) {
            jsonError('新密码长度至少6位');
        }
        
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($oldPassword, $user['password'])) {
            jsonError('旧密码错误');
        }
        
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed, $_SESSION['user_id']]);
        
        jsonSuccess();
        break;

    default:
        jsonError('未知的操作: ' . $action, 400);
        break;
}
