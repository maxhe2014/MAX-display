<?php
define('DB_TYPE', 'sqlite');
define('DB_PATH', __DIR__ . '/db/maxdisplay.db');
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', 'uploads/');
define('SESSION_NAME', 'maxdisplay_session');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

date_default_timezone_set('Asia/Shanghai');
session_name(SESSION_NAME);
session_start();

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $dbDir = dirname(DB_PATH);
        if (!file_exists($dbDir)) {
            mkdir($dbDir, 0755, true);
        }
        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
    return $pdo;
}

function initDB() {
    if (file_exists(DB_PATH) && filesize(DB_PATH) > 0) {
        return;
    }
    
    $pdo = getDB();
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            role TEXT DEFAULT 'admin',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS settings (
            key TEXT PRIMARY KEY,
            value TEXT
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS carousel (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            image TEXT NOT NULL,
            title TEXT,
            link TEXT DEFAULT '#',
            start_date TEXT DEFAULT '2020-01-01',
            end_date TEXT DEFAULT '2099-12-31',
            time_type TEXT DEFAULT 'all_day',
            custom_start_time TEXT DEFAULT '',
            custom_end_time TEXT DEFAULT '',
            sort_order INTEGER DEFAULT 0
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS scheduled_content (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            main_title TEXT,
            sub_title TEXT,
            welcome TEXT,
            start_date TEXT,
            end_date TEXT,
            time_type TEXT DEFAULT 'all_day',
            custom_start_time TEXT DEFAULT '',
            custom_end_time TEXT DEFAULT ''
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS festivals (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            message TEXT,
            start_date TEXT,
            end_date TEXT
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS external_urls (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            url TEXT NOT NULL,
            sort_order INTEGER DEFAULT 0
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS ip_whitelist (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ip TEXT UNIQUE NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $defaultPassword = password_hash('123456', PASSWORD_DEFAULT);
    $pdo->exec("INSERT OR IGNORE INTO users (username, password, role) VALUES ('admin', '$defaultPassword', 'admin')");
    
    $defaultSettings = [
        'welcome' => '欢迎光临',
        'main_title' => '企业信息展示平台',
        'sub_title' => 'ENTERPRISE INFORMATION DISPLAY',
        'default_greeting' => '欢迎访问企业信息展示平台',
        'carousel_interval' => '3',
        'scheduled_carousel_interval' => '10',
        'background_image' => '',
        'weather_city' => '北京',
        'weather_enabled' => '1',
        'weather_api_key' => '',
        'left_logo' => '',
        'left_title' => 'MAX DISPLAY'
    ];
    
    foreach ($defaultSettings as $key => $value) {
        $stmt = $pdo->prepare("INSERT OR IGNORE INTO settings (key, value) VALUES (?, ?)");
        $stmt->execute([$key, $value]);
    }
    
    $defaultCarousel = [
        ['image' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1920&q=80', 'title' => '现代化办公环境', 'link' => '#', 'start_date' => '2020-01-01', 'end_date' => '2099-12-31', 'time_type' => 'all_day', 'sort_order' => 1],
        ['image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1920&q=80', 'title' => '团队协作', 'link' => '#', 'start_date' => '2020-01-01', 'end_date' => '2099-12-31', 'time_type' => 'all_day', 'sort_order' => 2],
        ['image' => 'https://images.unsplash.com/photo-1553877522-43269d4ea984?w=1920&q=80', 'title' => '创新技术', 'link' => '#', 'start_date' => '2020-01-01', 'end_date' => '2099-12-31', 'time_type' => 'all_day', 'sort_order' => 3]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO carousel (image, title, link, start_date, end_date, time_type, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($defaultCarousel as $item) {
        $stmt->execute([$item['image'], $item['title'], $item['link'], $item['start_date'], $item['end_date'], $item['time_type'], $item['sort_order']]);
    }
    
    $defaultFestivals = [
        ['title' => '端午节快乐', 'message' => '祝您端午节安康，阖家幸福！', 'start_date' => '2026-06-08', 'end_date' => '2026-06-12'],
        ['title' => '公司周年庆', 'message' => '感谢一路有你，共筑美好未来！', 'start_date' => '2026-06-10', 'end_date' => '2026-06-15'],
        ['title' => '国庆节快乐', 'message' => '祝全体员工节日快乐，阖家幸福！', 'start_date' => '2026-10-01', 'end_date' => '2026-10-07'],
        ['title' => '元旦快乐', 'message' => '新年快乐，万事如意！', 'start_date' => '2027-01-01', 'end_date' => '2027-01-03']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO festivals (title, message, start_date, end_date) VALUES (?, ?, ?, ?)");
    foreach ($defaultFestivals as $item) {
        $stmt->execute([$item['title'], $item['message'], $item['start_date'], $item['end_date']]);
    }
    
    $defaultScheduled = [
        ['name' => '端午节活动（全天）', 'main_title' => '端午佳节 粽香四溢', 'sub_title' => 'DRAGON BOAT FESTIVAL', 'welcome' => '端午安康，幸福美满！', 'start_date' => '2026-06-08', 'end_date' => '2026-06-12', 'time_type' => 'all_day'],
        ['name' => '上班时间问候', 'main_title' => '开工大吉', 'sub_title' => 'WORK HARD', 'welcome' => '祝大家工作愉快，业绩长虹！', 'start_date' => '2026-06-01', 'end_date' => '2026-12-31', 'time_type' => 'work_hours'],
        ['name' => '下班时间问候', 'main_title' => '辛苦了一天', 'sub_title' => 'RELAX NOW', 'welcome' => '下班快乐，回家路上注意安全！', 'start_date' => '2026-06-01', 'end_date' => '2026-12-31', 'time_type' => 'off_hours']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO scheduled_content (name, main_title, sub_title, welcome, start_date, end_date, time_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($defaultScheduled as $item) {
        $stmt->execute([$item['name'], $item['main_title'], $item['sub_title'], $item['welcome'], $item['start_date'], $item['end_date'], $item['time_type']]);
    }
    
    $defaultIps = ['127.0.0.1', '::1'];
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO ip_whitelist (ip) VALUES (?)");
    foreach ($defaultIps as $ip) {
        $stmt->execute([$ip]);
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function getClientIp() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    }
    return $_SERVER['REMOTE_ADDR'];
}

function isIpAllowed($ip) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM ip_whitelist WHERE ip = ?");
    $stmt->execute([$ip]);
    return $stmt->fetchColumn() > 0;
}

function checkIpAccess() {
    $ip = getClientIp();
    if (!isIpAllowed($ip)) {
        include __DIR__ . '/no_access.php';
        exit;
    }
    return $ip;
}

function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function jsonError($message, $status = 400) {
    jsonResponse(['success' => false, 'message' => $message], $status);
}

function jsonSuccess($data = []) {
    jsonResponse(array_merge(['success' => true], $data));
}

initDB();
