<?php
require_once __DIR__ . '/config.php';

checkIpAccess();
requireLogin();

header('Location: index.php');
exit;
