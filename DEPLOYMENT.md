# MAX DISPLAY 企业信息展示平台 部署文档

## 目录

- [项目概述](#项目概述)
- [环境要求](#环境要求)
- [文件结构](#文件结构)
- [Windows 部署](#windows-部署)
  - [方式一：XAMPP（推荐新手）](#方式一xampp推荐新手)
  - [方式二：WAMP](#方式二wamp)
  - [方式三：IIS + PHP](#方式三iis--php)
- [Linux 部署](#linux-部署)
  - [方式一：Apache + PHP](#方式一apache--php)
  - [方式二：Nginx + PHP-FPM](#方式二nginx--php-fpm)
  - [方式三：宝塔面板（推荐）](#方式三宝塔面板推荐)
  - [方式四：1Panel 面板（强烈推荐）](#方式四1panel-面板强烈推荐)
- [macOS 部署](#macos-部署)
  - [方式一：MAMP](#方式一mamp)
  - [方式二：内置 Apache + PHP](#方式二内置-apache--php)
- [Docker 部署](#docker-部署)
- [数据库切换](#数据库切换)
  - [切换到 MySQL](#切换到-mysql)
  - [切换到 PostgreSQL](#切换到-postgresql)
- [常见问题排查](#常见问题排查)
- [安全加固建议](#安全加固建议)

---

## 项目概述

MAX DISPLAY 是一个企业信息展示平台，支持：

- 动态轮播图展示（支持日期范围及时段控制）
- 时段文案自动切换
- 节日祝福自动显示
- 外部链接 iframe 集成
- 天气预报显示
- IP 白名单访问控制
- 多管理员用户管理

**默认管理员账号**: `admin` / `123456`

---

## 环境要求

| 组件 | 版本要求 | 说明 |
|------|----------|------|
| PHP | 7.4 或更高版本 | 推荐 PHP 8.0+ |
| PDO 扩展 | 必须启用 | 用于数据库连接 |
| SQLite 扩展 | 必须启用 | 默认数据库 |
| GD 扩展 | 可选 | 用于图片处理 |
| Fileinfo 扩展 | 必须启用 | 用于文件类型检测 |
| Session | 必须启用 | 用于用户登录 |
| Web 服务器 | Apache 2.4+ / Nginx 1.18+ / IIS 10+ | |

### PHP 扩展检查

创建 `phpinfo.php` 文件并访问，确认以下扩展已启用：

```
- PDO
- pdo_sqlite
- session
- fileinfo
- gd (可选)
- json
- mbstring
```

---

## 文件结构

```
max-display/
├── config.php           # 数据库配置和初始化脚本
├── api.php              # API 接口入口
├── index.php            # 主页面（前台展示 + 后台管理）
├── login.php            # 登录页面
├── admin.php            # 后台管理入口（重定向）
├── no_access.php        # IP 白名单拒绝页面
├── db/                  # SQLite 数据库目录（自动创建）
│   └── maxdisplay.db    # 数据库文件（自动创建）
├── uploads/             # 图片上传目录
├── css/
│   └── font.css         # 字体样式
├── font/                # 字体文件
│   ├── Inter-Bold_1.otf
│   ├── Inter-Light_1.otf
│   ├── Inter-Medium_1.otf
│   ├── Inter-Regular_1.otf
│   └── Inter-SemiBold_1.otf
├── image/               # 静态图片资源
│   ├── *.svg
│   └── *.png
├── js/
│   └── animation.js     # 动画脚本
└── DEPLOYMENT.md        # 本文档
```

---

## Windows 部署

### 方式一：XAMPP（推荐新手）

XAMPP 是最流行的 PHP 开发环境，包含 Apache、MySQL、PHP 等。

#### 1. 下载并安装 XAMPP

- 访问 [https://www.apachefriends.org/zh_cn/index.html](https://www.apachefriends.org/zh_cn/index.html)
- 下载 XAMPP for Windows（选择 PHP 7.4 或 8.x 版本）
- 运行安装程序，建议安装到 `C:\xampp`

#### 2. 启动服务

1. 打开 XAMPP Control Panel
2. 点击 **Apache** 模块的 **Start** 按钮
3. 等待 Apache 启动成功（状态变为绿色）

#### 3. 部署项目

```powershell
# 方式一：复制项目到 htdocs 目录
Copy-Item -Recurse "E:\project\MAX-display\max-display" "C:\xampp\htdocs\"

# 方式二：创建符号链接（推荐开发环境）
New-Item -ItemType SymbolicLink -Path "C:\xampp\htdocs\max-display" -Target "E:\project\MAX-display\max-display"
```

#### 4. 设置目录权限

确保以下目录对 Apache 有写入权限：

- `C:\xampp\htdocs\max-display\db\`
- `C:\xampp\htdocs\max-display\uploads\`

在 Windows 上，通常需要给 `IUSR` 或 `IIS_IUSRS` 用户组写入权限：

```powershell
# 设置 db 目录权限
$acl = Get-Acl "C:\xampp\htdocs\max-display\db"
$accessRule = New-Object System.Security.AccessControl.FileSystemAccessRule("Everyone", "FullControl", "ContainerInherit,ObjectInherit", "None", "Allow")
$acl.SetAccessRule($accessRule)
Set-Acl "C:\xampp\htdocs\max-display\db" $acl

# 设置 uploads 目录权限
$acl = Get-Acl "C:\xampp\htdocs\max-display\uploads"
$accessRule = New-Object System.Security.AccessControl.FileSystemAccessRule("Everyone", "FullControl", "ContainerInherit,ObjectInherit", "None", "Allow")
$acl.SetAccessRule($accessRule)
Set-Acl "C:\xampp\htdocs\max-display\uploads" $acl
```

#### 5. 验证安装

打开浏览器访问：

```
http://localhost/max-display/
```

**注意**：首次访问会自动创建数据库并插入默认数据。

#### 6. 登录后台

1. 点击页面右下角的 **"后台管理"** 按钮
2. 输入默认账号：`admin` / `123456`
3. 首次登录后请立即修改密码

---

### 方式二：WAMP

#### 1. 下载并安装 WAMP

- 访问 [https://www.wampserver.com/](https://www.wampserver.com/)
- 下载并安装 WampServer

#### 2. 启动服务

- 安装完成后启动 WampServer
- 等待系统托盘图标变为绿色

#### 3. 部署项目

```powershell
Copy-Item -Recurse "E:\project\MAX-display\max-display" "C:\wamp64\www\"
```

#### 4. 设置目录权限

右键点击 `db` 和 `uploads` 目录 → **属性** → **安全** → 添加 `Everyone` 用户并给予 **完全控制** 权限。

#### 5. 访问

```
http://localhost/max-display/
```

---

### 方式三：IIS + PHP

适用于 Windows Server 环境。

#### 1. 安装 IIS

1. 打开 **服务器管理器** → **添加角色和功能**
2. 选择 **Web 服务器 (IIS)**
3. 安装完成

#### 2. 安装 PHP

1. 下载 PHP for Windows: [https://windows.php.net/download/](https://windows.php.net/download/)
   - 选择 **Non-Thread Safe (NTS)** 版本
   - 选择 **x64** 架构

2. 解压到 `C:\php`

3. 配置 PHP：

```powershell
# 复制配置文件
Copy-Item "C:\php\php.ini-development" "C:\php\php.ini"

# 编辑 php.ini，取消注释以下扩展
# extension_dir = "ext"
# extension=pdo_sqlite
# extension=fileinfo
# extension=gd
# extension=mbstring
# extension=openssl
```

#### 3. 配置 IIS PHP 支持

1. 下载并安装 **PHP Manager for IIS**
2. 打开 **IIS 管理器**
3. 双击 **PHP Manager**
4. 点击 **Register new PHP version**
5. 选择 `C:\php\php-cgi.exe`

#### 4. 部署项目

```powershell
# 复制到网站根目录
Copy-Item -Recurse "E:\project\MAX-display\max-display" "C:\inetpub\wwwroot\"
```

#### 5. 设置目录权限

```powershell
# 给 IIS_IUSRS 用户组写入权限
$paths = @(
    "C:\inetpub\wwwroot\max-display\db",
    "C:\inetpub\wwwroot\max-display\uploads"
)

foreach ($path in $paths) {
    $acl = Get-Acl $path
    $accessRule = New-Object System.Security.AccessControl.FileSystemAccessRule(
        "IIS_IUSRS", 
        "Write, ReadAndExecute, Modify", 
        "ContainerInherit,ObjectInherit", 
        "None", 
        "Allow"
    )
    $acl.SetAccessRule($accessRule)
    Set-Acl $path $acl
}
```

#### 6. 配置 web.config（可选）

在项目根目录创建 `web.config`：

```xml
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
        <directoryBrowse enabled="false" />
        <security>
            <requestFiltering>
                <hiddenSegments>
                    <add segment="db" />
                    <add segment="config.php" />
                </hiddenSegments>
            </requestFiltering>
        </security>
    </system.webServer>
</configuration>
```

#### 7. 访问

```
http://localhost/max-display/
```

---

## Linux 部署

### 方式一：Apache + PHP

适用于 CentOS / RHEL / Ubuntu / Debian 等发行版。

#### Ubuntu / Debian

##### 1. 安装 Apache 和 PHP

```bash
# 更新软件源
sudo apt update
sudo apt upgrade -y

# 安装 Apache 和 PHP
sudo apt install -y apache2 php libapache2-mod-php php-sqlite3 php-mbstring php-gd php-fileinfo php-json

# 启用 mod_rewrite
sudo a2enmod rewrite

# 重启 Apache
sudo systemctl restart apache2
```

##### 2. 部署项目

```bash
# 复制到 Apache 网站根目录
sudo cp -r /path/to/max-display /var/www/html/

# 或创建符号链接
sudo ln -s /path/to/max-display /var/www/html/max-display
```

##### 3. 设置目录权限

```bash
# 设置所有者为 Apache 用户
sudo chown -R www-data:www-data /var/www/html/max-display

# 设置目录权限
sudo chmod -R 755 /var/www/html/max-display
sudo chmod -R 775 /var/www/html/max-display/db
sudo chmod -R 775 /var/www/html/max-display/uploads
```

##### 4. 配置 Apache（可选，子目录访问）

创建 `/etc/apache2/conf-available/max-display.conf`：

```apache
<Directory /var/www/html/max-display>
    Options -Indexes +FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

启用配置：

```bash
sudo a2enconf max-display
sudo systemctl reload apache2
```

##### 5. 访问

```
http://your-server-ip/max-display/
```

---

#### CentOS / RHEL

##### 1. 安装 Apache 和 PHP

```bash
# CentOS 7
sudo yum install -y httpd php php-pdo php-sqlite3 php-mbstring php-gd php-fileinfo

# CentOS 8 / Rocky Linux / AlmaLinux
sudo dnf install -y httpd php php-pdo php-sqlite3 php-mbstring php-gd php-fileinfo

# 启动并设置开机自启
sudo systemctl start httpd
sudo systemctl enable httpd
```

##### 2. 配置防火墙

```bash
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --reload
```

##### 3. 关闭 SELinux（或配置正确策略）

```bash
# 临时关闭
sudo setenforce 0

# 永久关闭（生产环境不推荐，建议配置正确策略）
sudo sed -i 's/SELINUX=enforcing/SELINUX=disabled/' /etc/selinux/config
```

##### 4. 部署项目

```bash
sudo cp -r /path/to/max-display /var/www/html/
```

##### 5. 设置目录权限

```bash
sudo chown -R apache:apache /var/www/html/max-display
sudo chmod -R 755 /var/www/html/max-display
sudo chmod -R 775 /var/www/html/max-display/db
sudo chmod -R 775 /var/www/html/max-display/uploads
```

##### 6. 访问

```
http://your-server-ip/max-display/
```

---

### 方式二：Nginx + PHP-FPM

##### Ubuntu / Debian

```bash
# 安装 Nginx 和 PHP-FPM
sudo apt install -y nginx php-fpm php-sqlite3 php-mbstring php-gd php-fileinfo

# 启动服务
sudo systemctl start nginx
sudo systemctl start php8.1-fpm  # 根据实际 PHP 版本调整
sudo systemctl enable nginx
sudo systemctl enable php8.1-fpm
```

##### CentOS / RHEL

```bash
sudo dnf install -y nginx php-fpm php-sqlite3 php-mbstring php-gd php-fileinfo

sudo systemctl start nginx
sudo systemctl start php-fpm
sudo systemctl enable nginx
sudo systemctl enable php-fpm
```

##### 配置 Nginx

创建 `/etc/nginx/sites-available/max-display.conf`：

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/html/max-display;
    index index.php index.html;

    # 禁止访问敏感文件
    location ~* /config\.php$ {
        deny all;
    }

    location ~* /db/ {
        deny all;
    }

    # PHP 处理
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;  # 根据 PHP 版本调整
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # 静态文件缓存
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|otf)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
```

启用站点：

```bash
sudo ln -s /etc/nginx/sites-available/max-display.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

### 方式三：宝塔面板（推荐）

宝塔面板是国内最流行的服务器管理面板，适合新手。

#### 1. 安装宝塔面板

```bash
# CentOS
yum install -y wget && wget -O install.sh https://download.bt.cn/install/install_6.0.sh && sh install.sh

# Ubuntu
wget -O install.sh https://download.bt.cn/install/install-ubuntu_6.0.sh && sudo bash install.sh
```

#### 2. 登录面板

安装完成后，根据提示获取登录地址、用户名和密码。

#### 3. 安装环境

1. 登录宝塔面板
2. 首次登录会弹出推荐套件安装
3. 选择 **LNMP** 或 **LAMP** 环境
   - Nginx / Apache
   - PHP 7.4 或 8.0+
   - MySQL（可选，本项目默认使用 SQLite）
4. 等待安装完成

#### 4. 创建网站

1. 点击左侧 **网站** → **添加站点**
2. 填写域名（如 `display.example.com`）
3. 数据库选择 **不创建**（使用 SQLite）
4. 点击 **提交**

#### 5. 上传项目

1. 点击左侧 **文件**
2. 进入网站根目录（如 `/www/wwwroot/display.example.com`）
3. 删除默认的 `index.html`
4. 上传或解压 `max-display` 目录内容到网站根目录

#### 6. 设置权限

1. 在文件管理器中，选中 `db` 和 `uploads` 目录
2. 点击 **权限**
3. 设置权限为 **755**，所有者为 **www**

#### 7. 访问网站

```
http://display.example.com
```

---

### 方式四：1Panel 面板（强烈推荐）

1Panel 是一个现代化、开源的 Linux 服务器运维管理面板，功能强大且界面美观。

#### 1. 安装 1Panel

使用 SSH 登录到服务器，执行以下命令安装 1Panel：

```bash
# 推荐安装命令（中国大陆服务器）
curl -sSL https://resource.fit2cloud.com/1panel/package/quick_start.sh -o quick_start.sh && sh quick_start.sh

# 海外服务器
curl -sSL https://github.com/1Panel-dev/1Panel/releases/latest/download/quick_start.sh -o quick_start.sh && sh quick_start.sh
```

安装过程中会提示：
- 设置安装目录（默认 `/opt`）
- 设置面板端口（默认随机）
- 设置面板用户名
- 设置面板密码

安装完成后，会显示登录地址，例如：

```
================= 感谢安装 1Panel =================
面板地址: http://xxx.xxx.xxx.xxx:xxxx
账户名称: 1panel
账户密码: **********
==================================================
```

**注意**：请务必保存好这些登录信息！

#### 2. 登录 1Panel 面板

1. 在浏览器中打开安装完成后显示的面板地址（例如 `http://服务器IP:端口号`）
2. 输入安装时设置的用户名和密码
3. 点击登录

#### 3. 安装运行环境

首次登录后，1Panel 会引导您安装运行环境。如果没有自动提示，请按以下步骤操作：

1. 点击左侧菜单 **应用商店**
2. 找到 **OpenResty**（推荐，比 Nginx 更强大）或 **Nginx**，点击 **安装**
3. 找到 **PHP**，选择 **PHP 8.1** 或 **PHP 8.2**（推荐 8.2），点击 **安装**
4. 等待安装完成

或者使用 1Panel 的 **一键安装** 功能：
1. 点击左侧菜单 **网站**
2. 点击 **创建网站**
3. 在弹出的向导中，选择 **PHP 网站**
4. 系统会自动检测并安装所需环境

#### 4. 创建网站

##### 方式一：绑定域名（推荐）

1. 点击左侧菜单 **网站**
2. 点击 **创建网站**
3. 填写网站信息：

| 配置项 | 说明 | 示例 |
|--------|------|------|
| 主域名 | 您要绑定的域名 | `display.example.com` |
| 可选域名 | 其他域名（如 www） | `www.display.example.com`（留空即可） |
| 站点目录 | 网站文件存放路径 | 默认即可 |
| PHP 版本 | 选择已安装的 PHP 版本 | `PHP 8.2` |
| 数据库 | 是否创建数据库 | **不创建数据库**（使用 SQLite） |

4. 点击 **确认创建**

##### 方式二：仅使用 IP 访问

如果没有域名，也可以直接使用服务器 IP 访问：

1. 创建网站时，主域名填写服务器的公网 IP
2. 其他配置同上

#### 5. 上传项目文件

##### 方式一：1Panel 文件管理器（推荐）

1. 点击左侧菜单 **网站**
2. 找到刚创建的网站，点击 **根目录** 或 **文件** 图标
3. 进入网站根目录后，点击 **删除** 选中默认生成的 `index.html` 和 `.user.ini` 等文件（.user.ini 可保留）
4. 点击 **上传** 按钮，选择 `max-display` 目录中的所有文件（不包含 `max-display` 目录本身）
5. 或者点击 **在线解压**，如果您是先上传了 zip 压缩包

**注意**：确保文件直接上传到网站根目录，而不是放在子目录中。

##### 方式二：使用 SFTP/FTP

1. 在 1Panel 中创建 FTP 账号（**文件** → **FTP** → **创建 FTP**）
2. 使用 FileZilla 等 FTP 工具连接服务器
3. 将 `max-display` 目录下的所有文件上传到网站根目录

##### 方式三：使用 Git（推荐开发者）

如果您的项目托管在 Git 仓库：

1. 点击左侧菜单 **网站**
2. 找到网站，点击 **更多** → **Git**
3. 填写 Git 仓库地址
4. 设置部署目录为网站根目录

#### 6. 检查项目文件结构

上传完成后，网站根目录应包含以下文件：

```
/opt/1panel/apps/openresty/openresty/www/sites/display.example.com/index/
├── config.php           ← 必须存在
├── api.php              ← 必须存在
├── index.php            ← 必须存在
├── login.php            ← 必须存在
├── admin.php            ← 必须存在
├── no_access.php        ← 必须存在
├── uploads/             ← 必须存在，确保有写入权限
├── db/                  ← 必须存在，确保有写入权限（如果不存在，PHP 会自动创建）
├── css/
├── font/
├── image/
├── js/
└── DEPLOYMENT.md
```

#### 7. 设置目录权限

这是非常重要的一步！确保 `db` 和 `uploads` 目录对 Web 服务器有写入权限。

##### 方式一：使用 1Panel 文件管理器

1. 进入网站根目录
2. 右键点击 `uploads` 目录（如果不存在，先手动创建一个空目录）
3. 选择 **权限**
4. 设置权限为 **755** 或 **775**
5. 所有者确保为 **www** 或 **www-data**
6. 对 `db` 目录执行相同操作（如果目录已存在）

##### 方式二：使用 SSH 命令

```bash
# 进入网站根目录（根据实际路径调整）
cd /opt/1panel/apps/openresty/openresty/www/sites/display.example.com/index

# 创建 db 目录（如果不存在）
mkdir -p db
mkdir -p uploads

# 设置目录权限
chmod -R 755 db
chmod -R 755 uploads

# 设置所有者（根据实际运行用户调整）
chown -R www:www db
chown -R www:www uploads
# 或者
chown -R www-data:www-data db
chown -R www-data:www-data uploads
```

**如何确定运行用户？**

1. 在 1Panel 中点击 **网站**
2. 查看网站运行用户，通常是 `www`（OpenResty/Nginx）或 `apache`

#### 8. 配置 PHP 扩展

本项目需要以下 PHP 扩展：
- PDO
- pdo_sqlite
- session
- fileinfo
- gd（可选，推荐）
- mbstring

1Panel 默认安装的 PHP 通常已包含这些扩展。如需确认：

1. 点击左侧菜单 **网站**
2. 找到网站，点击 **设置**
3. 切换到 **PHP** 选项卡
4. 查看 **禁用函数**，确保以下函数未被禁用：
   - `file_get_contents`
   - `curl_exec`（如果使用）
   - `unlink`（如果使用文件删除功能）
5. 点击 **PHP管理** → **PHP配置**，检查 `php.ini`：

```ini
extension=pdo_sqlite
extension=gd
extension=mbstring
extension=fileinfo
```

#### 9. 配置伪静态（可选）

虽然本项目不需要伪静态，但可以配置来保护敏感文件：

1. 点击左侧菜单 **网站**
2. 找到网站，点击 **设置**
3. 切换到 **伪静态** 选项卡
4. 添加以下规则（Nginx/OpenResty）：

```nginx
# 禁止访问配置文件
location ~* /config\.php$ {
    deny all;
    return 404;
}

# 禁止访问数据库目录
location ~* /db/ {
    deny all;
    return 404;
}

# 禁止访问隐藏文件
location ~ /\. {
    deny all;
    return 404;
}

# 禁止目录浏览
autoindex off;
```

5. 点击 **保存**

#### 10. 配置 SSL 证书（HTTPS，推荐）

1Panel 支持一键申请 Let's Encrypt 免费证书：

1. 点击左侧菜单 **网站**
2. 找到网站，点击 **设置**
3. 切换到 **SSL** 选项卡
4. 选择 **Let's Encrypt** 标签
5. 点击 **申请证书**
6. 等待证书申请成功
7. 开启 **强制 HTTPS**

或者上传已有证书：
1. 选择 **上传证书** 标签
2. 粘贴证书内容和私钥
3. 点击 **保存**

#### 11. 访问网站

现在您可以通过以下方式访问网站：

```
# 域名访问
https://display.example.com

# IP 访问（如果配置了）
http://服务器IP地址
```

**首次访问说明**：
- 首次访问会自动创建 SQLite 数据库文件
- 自动插入默认数据（轮播图、节日、时段文案等）
- 默认管理员账号：`admin` / `123456`

#### 12. 测试后台管理

1. 点击页面右下角的 **"后台管理"** 按钮
2. 输入默认账号：`admin` / `123456`
3. 登录成功后，进入后台管理界面
4. 测试各项功能：
   - ✅ 轮播图管理（上传图片、添加、编辑、删除）
   - ✅ 时段文案管理
   - ✅ 节日管理
   - ✅ 外部链接管理
   - ✅ IP 白名单管理
   - ✅ 外观设置（修改标题、欢迎词等）
   - ✅ 系统设置
   - ✅ 用户管理（修改密码、添加用户）

#### 13. 安全加固（重要）

##### 修改默认管理员密码

1. 登录后台管理
2. 进入 **用户管理** 选项卡
3. 点击 `admin` 用户的 **修改密码**
4. 设置一个强密码

##### 添加 IP 白名单

默认只允许 `127.0.0.1` 和 `::1` 访问，您需要添加访问者的 IP：

1. 登录后台管理
2. 进入 **IP 白名单** 选项卡
3. 查看"当前 IP"显示的是您的访问 IP
4. 点击 **添加 IP**，输入您的 IP 地址
5. 保存

**⚠️ 重要提示**：
- 如果您是通过域名访问，添加的应该是您的公网 IP
- 1Panel 面板所在服务器的 IP 也应该加入白名单
- 如果不小心把自己的 IP 移出白名单，可以通过 SSH 手动编辑数据库：

```bash
# 进入网站根目录的 db 子目录
cd /opt/1panel/apps/openresty/openresty/www/sites/display.example.com/index/db

# 使用 SQLite 命令行
sqlite3 maxdisplay.db

-- 查看当前白名单
SELECT * FROM ip_whitelist;

-- 添加您的 IP
INSERT INTO ip_whitelist (ip) VALUES ('您的IP地址');

-- 退出
.quit
```

##### 备份数据库

1. 点击左侧菜单 **文件**
2. 进入网站根目录的 `db` 目录
3. 下载 `maxdisplay.db` 文件保存到安全位置

或者设置 1Panel 的自动备份：
1. 点击左侧菜单 **备份**
2. 点击 **网站备份**
3. 设置备份策略（每日、每周等）

#### 14. 常见问题

##### Q1: 网站显示 500 错误

**原因**：PHP 错误或权限问题

**解决方法**：
1. 检查目录权限：`db` 和 `uploads` 目录是否有写入权限
2. 查看 1Panel 日志：**网站** → 点击网站 **日志** → **错误日志**
3. 确保 PHP 版本符合要求（7.4+）

##### Q2: 数据库无法创建或写入

**原因**：`db` 目录无写入权限

**解决方法**：
```bash
# 进入网站根目录
cd /opt/1panel/apps/openresty/openresty/www/sites/display.example.com/index

# 创建 db 目录（如果不存在）
mkdir -p db

# 设置权限
chmod 777 db
chown -R www:www db
```

##### Q3: 图片上传失败

**原因**：`uploads` 目录权限问题或 PHP 上传大小限制

**解决方法**：
1. 检查 `uploads` 目录权限
2. 在 1Panel 中修改 PHP 配置：
   - 点击 **网站** → 网站 **设置** → **PHP管理** → **PHP配置**
   - 找到并修改：
     ```ini
     upload_max_filesize = 10M
     post_max_size = 20M
     max_execution_time = 300
     ```

##### Q4: 访问显示"无访问权限"

**原因**：您的 IP 不在白名单中

**解决方法**：
1. 通过 SSH 登录服务器
2. 使用 SQLite 命令行添加 IP

##### Q5: 天气预报不显示

**原因**：服务器无法访问外部天气 API

**解决方法**：
1. 在 1Panel 中测试服务器网络连接
2. 确保 PHP `allow_url_fopen` 开启
3. 在后台系统设置中配置和风天气 API Key（可选）

#### 15. 1Panel 常用维护操作

##### 重启服务

1. 点击左侧菜单 **主机**
2. 找到 **OpenResty** 或 **Nginx** 服务
3. 点击 **重启**

##### 查看错误日志

1. 点击左侧菜单 **网站**
2. 点击网站的 **日志** 按钮
3. 切换到 **错误日志** 标签

##### 查看 PHP 信息

1. 在网站根目录创建 `phpinfo.php`：

```php
<?php
phpinfo();
```

2. 访问 `https://display.example.com/phpinfo.php`
3. 检查完毕后删除该文件（安全考虑）

##### 清理缓存

1. 点击左侧菜单 **网站**
2. 点击网站 **设置**
3. 如果使用了缓存插件，点击 **清理缓存**

---

## macOS 部署

### 方式一：MAMP

#### 1. 下载并安装 MAMP

- 访问 [https://www.mamp.info/](https://www.mamp.info/)
- 下载 MAMP for macOS
- 安装到 `/Applications/MAMP`

#### 2. 启动服务

1. 打开 MAMP
2. 点击 **Start Servers**

#### 3. 部署项目

```bash
# 复制项目到 htdocs 目录
cp -r /path/to/max-display /Applications/MAMP/htdocs/
```

#### 4. 设置权限

```bash
chmod -R 755 /Applications/MAMP/htdocs/max-display
chmod -R 777 /Applications/MAMP/htdocs/max-display/db
chmod -R 777 /Applications/MAMP/htdocs/max-display/uploads
```

#### 5. 访问

```
http://localhost:8888/max-display/
```

---

### 方式二：内置 Apache + PHP

macOS 内置了 Apache 和 PHP。

#### 1. 启用 Apache

```bash
# 启动 Apache
sudo apachectl start

# 设置开机自启
sudo launchctl load -w /System/Library/LaunchDaemons/org.apache.httpd.plist
```

#### 2. 部署项目

```bash
# 复制到网站根目录
sudo cp -r /path/to/max-display /Library/WebServer/Documents/
```

#### 3. 设置权限

```bash
sudo chmod -R 755 /Library/WebServer/Documents/max-display
sudo chmod -R 777 /Library/WebServer/Documents/max-display/db
sudo chmod -R 777 /Library/WebServer/Documents/max-display/uploads
```

#### 4. 访问

```
http://localhost/max-display/
```

---

## Docker 部署

### 方式一：使用官方 PHP 镜像

#### 1. 创建 Dockerfile

```dockerfile
FROM php:8.1-apache

# 安装扩展
RUN docker-php-ext-install pdo pdo_sqlite fileinfo gd mbstring

# 启用 mod_rewrite
RUN a2enmod rewrite

# 复制项目
COPY . /var/www/html/

# 设置权限
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/db \
    && chmod -R 775 /var/www/html/uploads

# 暴露端口
EXPOSE 80
```

#### 2. 创建 docker-compose.yml

```yaml
version: '3.8'

services:
  web:
    build: .
    ports:
      - "8080:80"
    volumes:
      - ./db:/var/www/html/db
      - ./uploads:/var/www/html/uploads
    restart: always
```

#### 3. 启动容器

```bash
docker-compose up -d
```

#### 4. 访问

```
http://localhost:8080
```

---

## 数据库切换

本项目默认使用 SQLite，也支持切换到 MySQL 或 PostgreSQL。

### 切换到 MySQL

#### 1. 创建数据库

```sql
CREATE DATABASE maxdisplay CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'maxdisplay'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON maxdisplay.* TO 'maxdisplay'@'localhost';
FLUSH PRIVILEGES;
```

#### 2. 修改 config.php

```php
<?php
// 修改数据库配置
define('DB_TYPE', 'mysql');
define('DB_HOST', 'localhost');
define('DB_NAME', 'maxdisplay');
define('DB_USER', 'maxdisplay');
define('DB_PASS', 'your_password');
define('DB_CHARSET', 'utf8mb4');

// ... 其余配置不变
```

#### 3. 修改 getDB() 函数

```php
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_NAME,
            DB_CHARSET
        );
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
    }
    return $pdo;
}
```

#### 4. 修改建表语句（MySQL 语法）

SQLite 的 `AUTOINCREMENT` 在 MySQL 中改为 `AUTO_INCREMENT`。

---

### 切换到 PostgreSQL

#### 1. 创建数据库

```sql
CREATE DATABASE maxdisplay;
CREATE USER maxdisplay WITH PASSWORD 'your_password';
GRANT ALL PRIVILEGES ON DATABASE maxdisplay TO maxdisplay;
```

#### 2. 修改 config.php

```php
<?php
define('DB_TYPE', 'pgsql');
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'maxdisplay');
define('DB_USER', 'maxdisplay');
define('DB_PASS', 'your_password');

// ... 其余配置不变
```

#### 3. 修改 getDB() 函数

```php
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s;user=%s;password=%s',
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_USER,
            DB_PASS
        );
        $pdo = new PDO($dsn, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }
    return $pdo;
}
```

#### 4. 修改建表语句（PostgreSQL 语法）

需要将 SQLite 语法转换为 PostgreSQL 语法，如 `AUTOINCREMENT` 改为 `SERIAL` 或 `GENERATED ALWAYS AS IDENTITY`。

---

## 常见问题排查

### 1. 页面空白或 500 错误

**可能原因**：
- PHP 错误未显示
- 目录权限问题

**排查步骤**：

```php
// 在 index.php 开头添加以下代码显示错误
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

或查看 Apache/Nginx 错误日志：

```bash
# Linux Apache
tail -f /var/log/apache2/error.log

# Linux Nginx
tail -f /var/log/nginx/error.log

# Windows XAMPP
# C:\xampp\apache\logs\error.log
```

### 2. 数据库写入失败

**可能原因**：
- `db` 目录无写入权限
- PHP 无法创建数据库文件

**解决方法**：

```bash
# Linux
sudo chown -R www-data:www-data db/
sudo chmod 777 db/

# Windows
# 右键 → 属性 → 安全 → 添加 Everyone → 完全控制
```

### 3. 图片上传失败

**可能原因**：
- `uploads` 目录无写入权限
- 上传文件大小超出限制
- PHP `post_max_size` 或 `upload_max_filesize` 设置过小

**解决方法**：

1. 检查目录权限
2. 修改 `php.ini`：

```ini
upload_max_filesize = 10M
post_max_size = 20M
max_execution_time = 300
max_input_time = 300
```

3. 重启 Web 服务器

### 4. IP 白名单问题

**症状**：访问显示"无访问权限"

**原因**：您的 IP 不在白名单中

**解决方法**：

1. 从已授权的 IP 访问管理后台
2. 在 **IP 白名单** 选项卡添加您的 IP
3. 或直接编辑数据库：

```bash
# 进入项目目录
cd /path/to/max-display/db

# 使用 SQLite 命令行
sqlite3 maxdisplay.db

-- 查看当前白名单
SELECT * FROM ip_whitelist;

-- 添加 IP
INSERT INTO ip_whitelist (ip) VALUES ('您的IP地址');

-- 退出
.quit
```

### 5. 登录失败

**可能原因**：
- 用户名或密码错误
- Session 未正确配置

**解决方法**：

1. 确认使用默认账号：`admin` / `123456`
2. 检查 PHP Session 配置：

```php
// php.ini
session.save_path = "/tmp"  # Linux
session.save_path = "C:\Windows\Temp"  # Windows
```

### 6. 天气预报不显示

**可能原因**：
- 服务器无法访问外部 API
- PHP `allow_url_fopen` 禁用

**解决方法**：

1. 检查 `php.ini`：

```ini
allow_url_fopen = On
```

2. 确保服务器能访问 `api.open-meteo.com`

3. 在后台系统设置中配置和风天气 API Key

### 7. 轮播图或图片不显示

**可能原因**：
- 图片上传失败
- 图片路径错误

**解决方法**：

1. 检查 `uploads` 目录是否有文件
2. 检查图片 URL 是否正确
3. 确保 Web 服务器能读取 `uploads` 目录

---

## 安全加固建议

### 1. 修改默认管理员密码

首次登录后立即修改默认密码：

1. 登录后台
2. 进入 **用户管理** 选项卡
3. 修改密码

### 2. 限制敏感文件访问

#### Apache（.htaccess）

在项目根目录创建 `.htaccess`：

```apache
# 禁止目录浏览
Options -Indexes

# 保护配置文件
<Files "config.php">
    Require all denied
</Files>

# 保护数据库目录
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^db/ - [F,L]
</IfModule>

# 禁止访问隐藏文件
<FilesMatch "^\.">
    Require all denied
</FilesMatch>
```

#### Nginx

在 server 块中添加：

```nginx
# 禁止访问配置文件
location ~* /config\.php$ {
    deny all;
    return 404;
}

# 禁止访问数据库目录
location ~* /db/ {
    deny all;
    return 404;
}

# 禁止访问隐藏文件
location ~ /\. {
    deny all;
    return 404;
}
```

### 3. 数据库文件保护

```bash
# 将 db 目录放在 Web 根目录之外
# 修改 config.php 中的 DB_PATH
define('DB_PATH', '/var/www/private/maxdisplay.db');
```

### 4. 启用 HTTPS

```bash
# 使用 Let's Encrypt 免费证书
sudo apt install certbot python3-certbot-apache  # Apache
sudo apt install certbot python3-certbot-nginx   # Nginx

# 获取证书
sudo certbot --apache -d your-domain.com
sudo certbot --nginx -d your-domain.com
```

### 5. 修改上传目录

```bash
# 将 uploads 目录移到 Web 根目录之外
# 使用别名或符号链接访问
```

### 6. 定期备份

```bash
# 备份数据库
cp db/maxdisplay.db backups/maxdisplay-$(date +%Y%m%d).db

# 备份上传文件
tar -czf backups/uploads-$(date +%Y%m%d).tar.gz uploads/
```

### 7. 监控访问日志

```bash
# 监控 Apache 访问日志
tail -f /var/log/apache2/access.log

# 监控 Nginx 访问日志
tail -f /var/log/nginx/access.log
```

### 8. 限制上传文件类型和大小

已在 [config.php](file:///e:/project/MAX-display/max-display/config.php#L7-L8) 中配置：

```php
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);  // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
```

---

## 运维建议

### 1. 定期更新

- 定期更新 PHP 版本
- 定期更新 Web 服务器
- 备份数据库和上传文件

### 2. 日志轮转

配置日志轮转避免日志文件过大：

```logrotate
# /etc/logrotate.d/max-display
/var/log/apache2/max-display-*.log {
    weekly
    rotate 12
    compress
    missingok
    notifempty
}
```

### 3. 性能优化

```php
// PHP 配置优化
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
```

### 4. 监控

使用监控工具（如 Prometheus、Grafana、Zabbix）监控：

- 服务器 CPU、内存、磁盘使用
- Web 服务器状态
- PHP-FPM 进程状态
- 磁盘空间（特别是 `uploads` 目录）

---

## 技术支持

如遇问题，请：

1. 查看 Web 服务器错误日志
2. 查看 PHP 错误日志
3. 检查文件权限
4. 确认 PHP 扩展是否正确安装

---

## 更新日志

| 版本 | 日期 | 更新内容 |
|------|------|----------|
| 1.0.0 | 2026-06-10 | 初始版本，支持 SQLite 数据库 |
