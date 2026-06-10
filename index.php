<?php
require_once __DIR__ . '/config.php';

$clientIp = checkIpAccess();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="企业信息展示平台">
    <meta name="theme-color" content="#071324">
    <title>企业信息展示平台</title>
    
    <link rel="preconnect" href="https://api.ipify.org">
    <link rel="preconnect" href="https://images.unsplash.com">
    
    <link rel="stylesheet" href="css/font.css" media="all">
    <style>
        :root {
            --text-white: rgba(255, 255, 255, 1);
            --text-secondary: rgba(107, 114, 128, 1);
            --text-primary: rgba(17, 24, 39, 1);
            --sidebar-bg: rgba(13, 27, 42, 1);
            --primary: rgba(15, 98, 254, 1);
            --display-bg: rgba(7, 19, 36, 1);
            --border: rgba(229, 231, 235, 1);
            --bg-white: rgba(255, 255, 255, 1);
            --bg-page: rgba(240, 242, 245, 1);
            --accent-orange: rgba(245, 158, 11, 1);
            --accent-green: rgba(16, 185, 129, 1);
            --accent-purple: rgba(139, 92, 246, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Inter-Regular", sans-serif;
            background-color: var(--display-bg);
            color: var(--text-white);
            min-height: 100vh;
            overflow-x: hidden;
        }

        body.loading {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .loading-container {
            text-align: center;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.1);
            border-left-color: var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loading-text {
            color: var(--text-white);
            font-size: 16px;
        }

        .no-access-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            background: linear-gradient(135deg, var(--display-bg) 0%, rgba(13, 27, 42, 0.95) 100%);
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
            font-family: "Inter-Bold";
            font-size: 32px;
            color: var(--text-white);
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
            font-family: "Inter-Bold";
            color: #ef4444;
            font-size: 18px;
        }

        .main-container {
            display: none;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 60px;
            background: linear-gradient(180deg, rgba(7, 19, 36, 0.95) 0%, rgba(7, 19, 36, 0.7) 100%);
            z-index: 100;
            backdrop-filter: blur(10px);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .logo-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            background: linear-gradient(135deg, #000 0%, #1a1a1a 100%);
        }

        .logo-text {
            font-family: "Inter-Bold";
            font-size: 22px;
            letter-spacing: 3px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .weather-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
        }

        .weather-icon {
            width: 24px;
            height: 24px;
            background-image: url(./image/cloudsun.svg);
            background-size: cover;
        }

        .weather-text {
            font-size: 16px;
        }

        .datetime-info {
            text-align: right;
        }

        .datetime-time {
            font-family: "Inter-Bold";
            font-size: 24px;
        }

        .datetime-date {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
        }

        .hero-section {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .carousel {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            overflow: hidden;
        }

        .carousel-slides {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .carousel-slide {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0;
            transition: opacity 0.8s ease-in-out;
            background-size: cover;
            background-position: center;
        }

        .carousel-slide.active {
            opacity: 1;
        }

        .carousel-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, rgba(7, 19, 36, 0.6) 0%, rgba(7, 19, 36, 0.8) 50%, rgba(7, 19, 36, 0.95) 100%);
        }

        .carousel-button {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .carousel-button:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-50%) scale(1.1);
        }

        .carousel-button.prev {
            left: 40px;
        }

        .carousel-button.next {
            right: 40px;
        }

        .carousel-button svg {
            width: 20px;
            height: 20px;
            color: var(--text-white);
        }

        .carousel-indicators {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 10;
        }

        .carousel-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .carousel-indicator.active {
            background: var(--primary);
            width: 30px;
            border-radius: 6px;
        }

        .hero-content {
            position: relative;
            z-index: 5;
            text-align: center;
            padding: 0 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .welcome-tag {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 28px;
        }

        .welcome-line {
            width: 80px;
            height: 4px;
            border-radius: 2px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
        }

        .welcome-text {
            font-size: 22px;
            letter-spacing: 8px;
            color: rgba(255, 255, 255, 0.7);
        }

        .hero-title {
            font-family: "Inter-Bold";
            font-size: 72px;
            line-height: 1.2;
            margin-bottom: 28px;
            text-shadow: 0 8px 60px rgba(15, 98, 254, 0.26);
        }

        .hero-subtitle {
            font-family: "Inter-Light";
            font-size: 18px;
            letter-spacing: 6px;
            color: rgba(255, 255, 255, 0.5);
        }

        .festival-banner {
            position: absolute;
            bottom: 120px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 5;
            width: 90%;
            max-width: 1200px;
        }

        .festival-cards-container {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            justify-content: center;
            align-items: center;
        }

        .festival-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 20px 40px;
            backdrop-filter: blur(10px);
            text-align: center;
            flex: 1;
            min-width: 250px;
            max-width: 400px;
        }

        .festival-title {
            font-family: "Inter-Bold";
            font-size: 24px;
            color: var(--accent-orange);
            margin-bottom: 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .festival-message {
            font-size: 16px;
            color: var(--text-white);
            line-height: 1.5;
        }

        .features-section {
            padding: 80px 60px;
            background: var(--bg-page);
            content-visibility: auto;
            contain-intrinsic-size: 400px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .feature-card {
            background: var(--bg-white);
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .feature-icon.blue {
            background: rgba(15, 98, 254, 0.1);
        }

        .feature-icon.green {
            background: rgba(16, 185, 129, 0.1);
        }

        .feature-icon.orange {
            background: rgba(245, 158, 11, 0.1);
        }

        .feature-icon svg {
            width: 28px;
            height: 28px;
        }

        .feature-icon.blue svg { color: var(--primary); }
        .feature-icon.green svg { color: var(--accent-green); }
        .feature-icon.orange svg { color: var(--accent-orange); }

        .feature-title {
            font-family: "Inter-Bold";
            font-size: 20px;
            color: var(--text-primary);
            margin-bottom: 12px;
        }

        .feature-desc {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .admin-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }

        .admin-button {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 24px;
            background: var(--primary);
            color: var(--text-white);
            border: none;
            border-radius: 12px;
            font-family: "Inter-Semi Bold";
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(15, 98, 254, 0.3);
            transition: all 0.3s ease;
        }

        .admin-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(15, 98, 254, 0.4);
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal {
            background: var(--bg-white);
            border-radius: 20px;
            width: 100%;
            max-width: 900px;
            max-height: 90vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 24px 32px;
            border-bottom: 1px solid var(--border);
        }

        .modal-title {
            font-family: "Inter-Bold";
            font-size: 24px;
            color: var(--text-primary);
        }

        .modal-close {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: none;
            background: var(--bg-page);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s ease;
        }

        .modal-close:hover {
            background: var(--border);
        }

        .modal-close svg {
            width: 20px;
            height: 20px;
            color: var(--text-secondary);
        }

        .modal-tabs {
            display: flex;
            border-bottom: 1px solid var(--border);
            padding: 0 32px;
        }

        .modal-tab {
            padding: 16px 24px;
            font-family: "Inter-Medium";
            font-size: 14px;
            color: var(--text-secondary);
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .modal-tab.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        .modal-body {
            padding: 32px;
            overflow-y: auto;
            flex: 1;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-family: "Inter-Medium";
            font-size: 14px;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .form-input, .form-textarea, .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.3s ease;
        }

        .form-input:focus, .form-textarea:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary);
        }

        .form-textarea {
            min-height: 100px;
            resize: vertical;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-family: "Inter-Semi Bold";
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--text-white);
        }

        .btn-primary:hover {
            background: #0e5ce6;
        }

        .btn-secondary {
            background: var(--bg-page);
            color: var(--text-primary);
        }

        .btn-secondary:hover {
            background: var(--border);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.2);
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 13px;
        }

        .btn-ghost {
            background: transparent;
            color: var(--text-secondary);
        }

        .btn-ghost:hover {
            background: var(--bg-page);
        }

        .list-container {
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
        }

        .list-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            background: var(--bg-white);
        }

        .list-item:last-child {
            border-bottom: none;
        }

        .list-item-content {
            flex: 1;
        }

        .list-item-title {
            font-family: "Inter-Medium";
            font-size: 14px;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .list-item-subtitle {
            font-size: 12px;
            color: var(--text-secondary);
        }

        .list-item-actions {
            display: flex;
            gap: 8px;
        }

        .add-item-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 16px;
            border: 2px dashed var(--border);
            border-radius: 12px;
            background: transparent;
            color: var(--primary);
            font-family: "Inter-Medium";
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 16px;
        }

        .add-item-btn:hover {
            border-color: var(--primary);
            background: rgba(15, 98, 254, 0.05);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .edit-form {
            background: var(--bg-page);
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 16px;
        }

        .edit-form-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .edit-form-title {
            font-family: "Inter-Bold";
            font-size: 16px;
            color: var(--text-primary);
        }

        .edit-form-actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: var(--text-secondary);
        }

        .empty-state-icon {
            width: 64px;
            height: 64px;
            opacity: 0.5;
            margin-bottom: 16px;
        }

        .settings-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            background: var(--bg-page);
            border-radius: 12px;
            margin-bottom: 16px;
        }

        .settings-label {
            font-family: "Inter-Medium";
            font-size: 14px;
            color: var(--text-primary);
        }

        .settings-input {
            width: 120px;
            padding: 10px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-switch .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 26px;
        }

        .toggle-switch .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }

        .toggle-switch input:checked + .slider {
            background-color: var(--primary);
        }

        .toggle-switch input:checked + .slider:before {
            transform: translateX(24px);
        }

        .save-indicator {
            position: fixed;
            top: 100px;
            right: 30px;
            padding: 12px 24px;
            background: var(--accent-green);
            color: var(--text-white);
            border-radius: 10px;
            font-family: "Inter-Medium";
            font-size: 14px;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease;
            z-index: 3000;
        }

        .save-indicator.show {
            opacity: 1;
            transform: translateY(0);
        }

        .ip-list {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .ip-tag {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            background: var(--bg-page);
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            color: var(--text-primary);
        }

        .ip-remove {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: none;
            background: transparent;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            transition: all 0.3s ease;
        }

        .ip-remove:hover {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .ip-add-form {
            display: flex;
            gap: 12px;
            margin-top: 16px;
        }

        .ip-add-form .form-input {
            flex: 1;
        }

        .external-section {
            padding: 60px;
            background: var(--display-bg);
            content-visibility: auto;
            contain-intrinsic-size: 700px;
        }

        .external-container {
            max-width: 1600px;
            margin: 0 auto;
        }

        .external-title {
            font-family: "Inter-Bold";
            font-size: 28px;
            color: var(--text-white);
            margin-bottom: 24px;
            text-align: center;
        }

        .external-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
            justify-content: center;
        }

        .external-tab {
            padding: 12px 24px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: var(--text-white);
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: "Inter-Medium";
            font-size: 14px;
        }

        .external-tab:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .external-tab.active {
            background: var(--primary);
            border-color: var(--primary);
        }

        .external-iframe-container {
            width: 100%;
            background: var(--bg-white);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .external-iframe {
            width: 100%;
            height: 600px;
            border: none;
        }

        .hero-section {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        
        .user-bar {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            font-size: 14px;
        }
        
        .logout-link {
            color: #ef4444;
            text-decoration: none;
            font-size: 13px;
        }
        
        .logout-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .header {
                height: 70px;
                padding: 0 20px;
            }

            .logo-text {
                font-size: 16px;
                letter-spacing: 2px;
            }

            .weather-info {
                display: none;
            }

            .datetime-time {
                font-size: 18px;
            }

            .datetime-date {
                font-size: 12px;
            }

            .hero-title {
                font-size: 36px;
            }

            .welcome-text {
                font-size: 14px;
                letter-spacing: 4px;
            }

            .hero-subtitle {
                font-size: 12px;
                letter-spacing: 3px;
            }

            .carousel-button {
                width: 40px;
                height: 40px;
            }

            .carousel-button.prev {
                left: 15px;
            }

            .carousel-button.next {
                right: 15px;
            }

            .festival-banner {
                bottom: 100px;
                padding: 0 20px;
            }

            .festival-cards-container {
                flex-direction: column;
                gap: 12px;
            }

            .festival-card {
                padding: 16px 24px;
                max-width: none;
                min-width: auto;
            }

            .festival-title {
                font-size: 18px;
            }

            .festival-message {
                font-size: 14px;
            }

            .features-section {
                padding: 40px 20px;
            }

            .modal {
                margin: 0;
                border-radius: 16px 16px 0 0;
                max-height: 85vh;
            }

            .modal-header {
                padding: 20px;
            }

            .modal-tabs {
                padding: 0 10px;
                overflow-x: auto;
            }

            .modal-tab {
                padding: 14px 16px;
                font-size: 13px;
                white-space: nowrap;
            }

            .modal-body {
                padding: 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .admin-btn {
                bottom: 20px;
                right: 20px;
            }

            .admin-button {
                padding: 12px 20px;
                font-size: 13px;
            }

            .list-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .list-item-actions {
                width: 100%;
            }

            .external-section {
                padding: 30px 20px;
            }

            .external-title {
                font-size: 22px;
            }

            .external-tab {
                padding: 10px 16px;
                font-size: 13px;
            }

            .external-iframe {
                height: 400px;
            }

            .no-access-container {
                padding: 40px 20px;
            }

            .no-access-title {
                font-size: 24px;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 28px;
            }

            .welcome-line {
                width: 50px;
            }

            .feature-card {
                padding: 24px;
            }

            .modal-title {
                font-size: 20px;
            }
        }
    </style>
</head>
<body class="loading">
    <div id="loading" class="loading-container">
        <div class="loading-spinner"></div>
        <div class="loading-text">正在加载数据...</div>
    </div>

    <div id="noAccessPage" class="no-access-page" style="display: none;">
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
                <div class="no-access-ip-value" id="currentIpDisplay">-</div>
            </div>
        </div>
    </div>

    <div id="mainContainer" class="main-container" style="display: none;">
        <header class="header">
            <div class="logo" id="headerLeft">
                <div class="logo-icon" id="leftLogoIcon"></div>
                <span class="logo-text" id="leftLogoText">MAX DISPLAY</span>
            </div>
            <div class="header-right">
                <div class="weather-info">
                    <div class="weather-icon"></div>
                    <span class="weather-text" id="weatherDisplay">企业展示平台</span>
                </div>
                <div class="datetime-info">
                    <div class="datetime-time" id="timeDisplay">--:--</div>
                    <div class="datetime-date" id="dateDisplay">----年--月--日</div>
                </div>
                <?php if (isLoggedIn()): ?>
                    <div class="user-bar">
                        <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <a href="api.php?action=logout" class="logout-link">退出</a>
                    </div>
                <?php endif; ?>
            </div>
        </header>

        <section class="hero-section">
            <div class="carousel" id="carousel">
                <div class="carousel-slides" id="carouselSlides"></div>
                <button class="carousel-button prev" onclick="carousel.prev()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"/>
                    </svg>
                </button>
                <button class="carousel-button next" onclick="carousel.next()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </button>
                <div class="carousel-indicators" id="carouselIndicators"></div>
            </div>
            <div class="carousel-overlay"></div>

            <div class="hero-content">
                <div class="welcome-tag">
                    <div class="welcome-line"></div>
                    <span class="welcome-text" id="welcomeText">欢迎光临</span>
                    <div class="welcome-line"></div>
                </div>
                <h1 class="hero-title" id="mainTitle">企业信息展示平台</h1>
                <p class="hero-subtitle" id="subTitle">ENTERPRISE INFORMATION DISPLAY</p>
            </div>

            <div class="festival-banner" id="festivalBanner">
                <div class="festival-cards-container" id="festivalCardsContainer"></div>
            </div>
        </section>

        <section class="features-section">
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon blue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <path d="M21 15l-5-5L5 21"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">动态轮播展示</h3>
                    <p class="feature-desc">支持自定义轮播图内容，可配置图片、标题和链接，定时自动切换展示。</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon green">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 19l7-7 3 3-7 7-3-3z"/>
                            <path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/>
                            <path d="M2 2l7.586 7.586"/>
                            <circle cx="11" cy="11" r="2"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">节日问候</h3>
                    <p class="feature-desc">根据日期自动显示节日祝福，支持配置多个节日及其有效期。</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon orange">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">后台管理</h3>
                    <p class="feature-desc">内置管理面板，可随时修改内容，支持轮播图、欢迎词、节日、IP白名单管理。</p>
                </div>
            </div>
        </section>

        <section class="external-section" id="externalSection" style="display: none;">
            <div class="external-container">
                <h2 class="external-title">外部链接展示</h2>
                <div class="external-tabs" id="externalTabs"></div>
                <div class="external-content" id="externalContent">
                    <div class="external-iframe-container" id="externalIframeContainer">
                        <iframe id="externalIframe" class="external-iframe" sandbox="allow-same-origin allow-scripts allow-forms allow-popups"></iframe>
                    </div>
                </div>
            </div>
        </section>

        <div class="admin-btn">
            <button class="admin-button" onclick="openAdminModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>
                </svg>
                后台管理
            </button>
        </div>

        <div class="modal-overlay" id="adminModal" onclick="if(event.target === this) closeAdminModal()">
            <div class="modal" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h2 class="modal-title">后台管理面板</h2>
                    <button class="modal-close" onclick="closeAdminModal()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>
                <div class="modal-tabs">
                    <div class="modal-tab active" data-tab="carousel" onclick="switchTab('carousel')">轮播图</div>
                    <div class="modal-tab" data-tab="scheduled" onclick="switchTab('scheduled')">时段文案</div>
                    <div class="modal-tab" data-tab="appearance" onclick="switchTab('appearance')">外观设置</div>
                    <div class="modal-tab" data-tab="festival" onclick="switchTab('festival')">节日管理</div>
                    <div class="modal-tab" data-tab="external" onclick="switchTab('external')">外部链接</div>
                    <div class="modal-tab" data-tab="ip" onclick="switchTab('ip')">IP白名单</div>
                    <div class="modal-tab" data-tab="users" onclick="switchTab('users')">用户管理</div>
                    <div class="modal-tab" data-tab="settings" onclick="switchTab('settings')">系统设置</div>
                </div>
                <div class="modal-body">
                    <div class="tab-content active" id="tab-carousel">
                        <div class="form-group">
                            <label class="form-label">轮播图列表</label>
                            <div class="list-container" id="carouselList"></div>
                        </div>
                        <button class="add-item-btn" onclick="addCarouselItem()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            添加轮播项
                        </button>
                    </div>

                    <div class="tab-content" id="tab-scheduled">
                        <div class="form-group">
                            <label class="form-label">时段文案列表</label>
                            <small style="color: var(--text-secondary); margin-bottom: 10px; display: block;">在指定日期范围内显示自定义的主标题、副标题和欢迎词</small>
                            <div class="list-container" id="scheduledList"></div>
                        </div>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <button class="add-item-btn" onclick="addScheduledContent()">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
                                    <line x1="12" y1="5" x2="12" y2="19"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                                添加时段文案
                            </button>
                        </div>
                    </div>

                    <div class="tab-content" id="tab-appearance">
                        <div class="form-group">
                            <label class="form-label">左上角标题</label>
                            <input type="text" class="form-input" id="leftTitleInput" placeholder="例如：MAX DISPLAY">
                        </div>
                        <div class="form-group">
                            <label class="form-label">左上角LOGO上传</label>
                            <input type="file" class="form-input" id="leftLogoInput" accept="image/*" style="padding: 8px;">
                            <small style="color: var(--text-secondary); margin-top: 8px; display: block;">支持 JPG、PNG、WebP 格式，建议尺寸 48x48 像素</small>
                        </div>
                        <div class="form-group" id="leftLogoPreviewContainer" style="display: none;">
                            <label class="form-label">当前左上角LOGO预览</label>
                            <div style="position: relative;">
                                <img id="leftLogoPreview" style="width: 80px; height: 80px; object-fit: contain; border-radius: 8px; background: #1a1a2e;">
                                <button class="btn btn-danger btn-sm" onclick="clearLeftLogo()" style="position: absolute; top: 10px; right: 10px;">清除LOGO</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">主标题</label>
                            <input type="text" class="form-input" id="mainTitleInput" placeholder="例如：企业信息展示平台">
                        </div>
                        <div class="form-group">
                            <label class="form-label">副标题</label>
                            <input type="text" class="form-input" id="subTitleInput" placeholder="例如：ENTERPRISE INFORMATION DISPLAY">
                        </div>
                        <div class="form-group">
                            <label class="form-label">欢迎词</label>
                            <textarea class="form-textarea" id="welcomeInput" placeholder="请输入欢迎词..."></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">背景图片上传</label>
                            <input type="file" class="form-input" id="bgImageInput" accept="image/*" style="padding: 8px;">
                            <small style="color: var(--text-secondary); margin-top: 8px; display: block;">支持 JPG、PNG、WebP 格式，建议分辨率 1920x1080 以上</small>
                        </div>
                        <div class="form-group" id="bgImagePreviewContainer" style="display: none;">
                            <label class="form-label">当前背景预览</label>
                            <div style="position: relative;">
                                <img id="bgImagePreview" style="width: 100%; max-height: 200px; object-fit: cover; border-radius: 8px;">
                                <button class="btn btn-danger btn-sm" onclick="clearBackgroundImage()" style="position: absolute; top: 10px; right: 10px;">清除背景</button>
                            </div>
                        </div>
                        <button class="btn btn-primary" onclick="saveAppearanceSettings()">
                            保存外观设置
                        </button>
                    </div>

                    <div class="tab-content" id="tab-external">
                        <div class="form-group">
                            <label class="form-label">外部链接列表</label>
                            <div class="list-container" id="externalList"></div>
                        </div>
                        <button class="add-item-btn" onclick="addExternalUrl()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            添加外部链接
                        </button>
                    </div>

                    <div class="tab-content" id="tab-festival">
                        <div class="form-group">
                            <label class="form-label">默认问候语（无节日时显示）</label>
                            <input type="text" class="form-input" id="defaultGreeting" placeholder="请输入默认问候语...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">节日列表</label>
                            <div class="list-container" id="festivalList"></div>
                        </div>
                        <button class="add-item-btn" onclick="addFestivalItem()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            添加节日
                        </button>
                    </div>

                    <div class="tab-content" id="tab-ip">
                        <div class="form-group">
                            <label class="form-label">当前IP地址</label>
                            <div class="ip-tag">
                                <span id="currentIp"><?php echo htmlspecialchars($clientIp); ?></span>
                            </div>
                            <small style="color: var(--text-secondary); margin-top: 8px; display: block;">请确保此IP已添加到白名单</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label">白名单IP列表</label>
                            <div class="ip-list" id="ipList"></div>
                        </div>
                        <div class="ip-add-form">
                            <input type="text" class="form-input" id="newIpInput" placeholder="输入IP地址，例如：192.168.1.1">
                            <button class="btn btn-primary" onclick="addIpToWhitelist()">添加</button>
                        </div>
                    </div>

                    <div class="tab-content" id="tab-users">
                        <div class="form-group">
                            <label class="form-label">管理员列表</label>
                            <div class="list-container" id="usersList"></div>
                        </div>
                        <div class="edit-form" style="margin-top: 16px;">
                            <div class="edit-form-header">
                                <span class="edit-form-title">修改当前用户密码</span>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">当前密码</label>
                                    <input type="password" class="form-input" id="changeOldPassword" placeholder="请输入当前密码">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">新密码</label>
                                    <input type="password" class="form-input" id="changeNewPassword" placeholder="请输入新密码（至少6位）">
                                </div>
                            </div>
                            <button class="btn btn-primary" onclick="changePassword()">修改密码</button>
                        </div>
                        <button class="add-item-btn" onclick="showAddUserForm()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            添加管理员
                        </button>
                    </div>

                    <div class="tab-content" id="tab-settings">
                        <div class="form-group">
                            <div class="settings-row">
                                <span class="settings-label">背景轮播间隔（秒）</span>
                                <input type="number" class="settings-input" id="carouselInterval" min="1" max="60" value="3">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="settings-row">
                                <span class="settings-label">时段文案轮播间隔（秒）</span>
                                <input type="number" class="settings-input" id="scheduledCarouselInterval" min="3" max="120" value="10">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="settings-row">
                                <span class="settings-label">启用天气预报</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="weatherEnabled" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">天气预报地区</label>
                            <input type="text" class="form-input" id="weatherCity" placeholder="例如：北京、上海、广州" value="北京">
                            <small style="color: var(--text-secondary); margin-top: 8px; display: block;">支持城市名称或城市代码</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label">和风天气 API Key（可选）</label>
                            <input type="text" class="form-input" id="weatherApiKey" placeholder="留空则使用免费公共接口">
                            <small style="color: var(--text-secondary); margin-top: 8px; display: block;">可在 <a href="https://dev.qweather.com/" target="_blank" style="color: var(--primary);">和风天气开发平台</a> 免费申请</small>
                        </div>
                        <button class="btn btn-primary" onclick="saveSettings()">
                            保存设置
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="save-indicator" id="saveIndicator">
            保存成功
        </div>
    </div>

    <script>
        const DEFAULT_DATA = {
            carousel: [],
            welcome: '欢迎光临',
            mainTitle: '企业信息展示平台',
            subTitle: 'ENTERPRISE INFORMATION DISPLAY',
            festivals: [],
            defaultGreeting: '欢迎访问企业信息展示平台',
            ipWhitelist: [],
            carouselInterval: 3,
            backgroundImage: '',
            externalUrls: [],
            scheduledContent: [],
            scheduledCarouselInterval: 10,
            weatherCity: '北京',
            weatherEnabled: true,
            weatherApiKey: '',
            leftLogo: '',
            leftTitle: 'MAX DISPLAY'
        };

        let appData = JSON.parse(JSON.stringify(DEFAULT_DATA));

        async function apiGet(action) {
            try {
                const response = await fetch('api.php?action=' + action);
                return await response.json();
            } catch (e) {
                console.error('API请求失败:', e);
                return { success: false, message: '网络错误' };
            }
        }

        async function apiPost(action, data, isJson = true) {
            try {
                const options = {
                    method: 'POST'
                };
                
                if (isJson && data) {
                    options.headers = { 'Content-Type': 'application/json' };
                    options.body = JSON.stringify(data);
                } else if (data) {
                    options.body = data;
                }
                
                const response = await fetch('api.php?action=' + action, options);
                return await response.json();
            } catch (e) {
                console.error('API请求失败:', e);
                return { success: false, message: '网络错误' };
            }
        }

        function showSaveIndicator() {
            const indicator = document.getElementById('saveIndicator');
            indicator.classList.add('show');
            setTimeout(() => {
                indicator.classList.remove('show');
            }, 2000);
        }

        function generateId() {
            return Date.now() + Math.random().toString(36).substr(2, 9);
        }

        function getLocalDateString(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        const IP_APIS = [
            'https://api.ipify.org?format=json',
            'https://api.my-ip.io/ip.json',
            'https://ipapi.co/json/'
        ];

        const WEATHER_ICONS = {
            '晴': '☀️',
            '多云': '⛅',
            '阴': '☁️',
            '阵雨': '🌦️',
            '雷阵雨': '⛈️',
            '雷阵雨并伴有冰雹': '⛈️',
            '雨夹雪': '🌨️',
            '小雨': '🌧️',
            '中雨': '🌧️',
            '大雨': '🌧️',
            '暴雨': '🌧️',
            '大暴雨': '🌧️',
            '特大暴雨': '🌧️',
            '阵雪': '🌨️',
            '小雪': '❄️',
            '中雪': '❄️',
            '大雪': '❄️',
            '暴雪': '❄️',
            '雾': '🌫️',
            '冻雨': '🌧️',
            '沙尘暴': '🌪️',
            '小到中雨': '🌧️',
            '中到大雨': '🌧️',
            '大到暴雨': '🌧️',
            '暴雨到大暴雨': '🌧️',
            '大暴雨到特大暴雨': '🌧️',
            '小到中雪': '❄️',
            '中到大雪': '❄️',
            '大到暴雪': '❄️',
            '浮尘': '🌫️',
            '扬沙': '🌪️',
            '强沙尘暴': '🌪️',
            '霾': '🌫️'
        };

        const WEATHER_CODE_MAP = {
            '100': '晴',
            '101': '多云',
            '102': '少云',
            '103': '晴间多云',
            '104': '阴',
            '150': '晴',
            '151': '晴',
            '152': '晴',
            '153': '晴',
            '154': '晴',
            '200': '有风',
            '201': '平静',
            '202': '微风',
            '203': '和风',
            '204': '清风',
            '205': '强风/劲风',
            '206': '疾风',
            '207': '大风',
            '208': '烈风',
            '209': '风暴',
            '210': '狂爆风',
            '211': '飓风',
            '212': '龙卷风',
            '213': '热带风暴',
            '300': '阵雨',
            '301': '强阵雨',
            '302': '雷阵雨',
            '303': '强雷阵雨',
            '304': '雷阵雨伴有冰雹',
            '305': '小雨',
            '306': '中雨',
            '307': '大雨',
            '308': '极端降雨',
            '309': '毛毛雨/细雨',
            '310': '暴雨',
            '311': '大暴雨',
            '312': '特大暴雨',
            '313': '冻雨',
            '314': '小到中雨',
            '315': '中到大雨',
            '316': '大到暴雨',
            '317': '暴雨到大暴雨',
            '318': '大暴雨到特大暴雨',
            '350': '小雨',
            '351': '中雨',
            '352': '大雨',
            '353': '小雨',
            '354': '中雨',
            '355': '大雨',
            '356': '暴雨',
            '357': '小到中雨',
            '358': '中到大雨',
            '359': '大到暴雨',
            '400': '小雪',
            '401': '中雪',
            '402': '大雪',
            '403': '暴雪',
            '404': '雨夹雪',
            '405': '雨雪天气',
            '406': '阵雨夹雪',
            '407': '阵雪',
            '408': '小到中雪',
            '409': '中到大雪',
            '410': '大到暴雪',
            '456': '小雪',
            '457': '中雪',
            '458': '大雪',
            '499': '中雪',
            '500': '薄雾',
            '501': '雾',
            '502': '霾',
            '503': '扬沙',
            '504': '浮尘',
            '507': '沙尘暴',
            '508': '强沙尘暴',
            '509': '浓雾',
            '510': '强浓雾',
            '511': '中度霾',
            '512': '重度霾',
            '513': '严重霾',
            '514': '大雾',
            '515': '特强浓雾',
            '600': '热',
            '601': '冷',
            '602': '未知',
            '701': '未知',
            '702': '未知',
            '703': '未知',
            '704': '未知',
            '705': '未知',
            '706': '未知',
            '707': '未知',
            '708': '未知',
            '709': '未知',
            '710': '未知',
            '711': '未知',
            '712': '未知',
            '713': '未知',
            '714': '未知',
            '715': '未知',
            '716': '未知',
            '717': '未知',
            '718': '未知',
            '719': '未知',
            '720': '未知',
            '721': '未知',
            '722': '未知',
            '723': '未知',
            '724': '未知',
            '725': '未知',
            '726': '未知',
            '727': '未知',
            '728': '未知',
            '729': '未知',
            '730': '未知',
            '731': '未知',
            '732': '未知',
            '733': '未知',
            '734': '未知',
            '735': '未知',
            '736': '未知',
            '737': '未知',
            '738': '未知',
            '739': '未知',
            '740': '未知',
            '741': '未知',
            '742': '未知',
            '743': '未知',
            '744': '未知',
            '745': '未知',
            '746': '未知',
            '747': '未知',
            '748': '未知',
            '749': '未知',
            '750': '未知',
            '751': '未知',
            '752': '未知',
            '753': '未知',
            '754': '未知',
            '755': '未知',
            '756': '未知',
            '757': '未知',
            '758': '未知',
            '759': '未知',
            '760': '未知',
            '761': '未知',
            '762': '未知',
            '763': '未知',
            '764': '未知',
            '765': '未知',
            '766': '未知',
            '767': '未知',
            '768': '未知',
            '769': '未知',
            '770': '未知',
            '771': '未知',
            '772': '未知',
            '773': '未知',
            '774': '未知',
            '775': '未知',
            '776': '未知',
            '777': '未知',
            '778': '未知',
            '779': '未知',
            '780': '未知',
            '781': '未知',
            '782': '未知',
            '783': '未知',
            '784': '未知',
            '785': '未知',
            '786': '未知',
            '787': '未知',
            '788': '未知',
            '789': '未知',
            '790': '未知',
            '791': '未知',
            '792': '未知',
            '793': '未知',
            '794': '未知',
            '795': '未知',
            '796': '未知',
            '797': '未知',
            '798': '未知',
            '799': '未知',
            '800': '未知',
            '801': '未知',
            '802': '未知',
            '803': '未知',
            '804': '未知',
            '805': '未知',
            '806': '未知',
            '807': '未知',
            '900': '雨',
            '901': '雪'
        };

        function getWeatherIcon(text) {
            if (!text) return '🌤️';
            if (WEATHER_ICONS[text]) {
                return WEATHER_ICONS[text];
            }
            for (const [key, value] of Object.entries(WEATHER_ICONS)) {
                if (text.includes(key)) {
                    return value;
                }
            }
            return '🌤️';
        }

        const CITY_COORDINATES = {
            '北京': { lat: 39.9042, lon: 116.4074 },
            '上海': { lat: 31.2304, lon: 121.4737 },
            '广州': { lat: 23.1291, lon: 113.2644 },
            '深圳': { lat: 22.5431, lon: 114.0579 },
            '成都': { lat: 30.5728, lon: 104.0668 },
            '杭州': { lat: 30.2741, lon: 120.1551 },
            '武汉': { lat: 30.5928, lon: 114.3055 },
            '西安': { lat: 34.3416, lon: 108.9398 },
            '重庆': { lat: 29.4316, lon: 106.9123 },
            '南京': { lat: 32.0603, lon: 118.7969 },
            '天津': { lat: 39.0842, lon: 117.2009 },
            '苏州': { lat: 31.2989, lon: 120.5853 },
            '郑州': { lat: 34.7466, lon: 113.6254 },
            '长沙': { lat: 28.2282, lon: 112.9388 },
            '东莞': { lat: 23.0489, lon: 113.7447 },
            '青岛': { lat: 36.0671, lon: 120.3826 },
            '沈阳': { lat: 41.8057, lon: 123.4315 },
            '宁波': { lat: 29.8683, lon: 121.5440 },
            '昆明': { lat: 25.0389, lon: 102.7183 },
            '大连': { lat: 38.9140, lon: 121.6147 },
            '厦门': { lat: 24.4798, lon: 118.0894 },
            '济南': { lat: 36.6512, lon: 117.1201 },
            '哈尔滨': { lat: 45.8038, lon: 126.5350 },
            '福州': { lat: 26.0745, lon: 119.2965 },
            '温州': { lat: 28.0005, lon: 120.6964 },
            '长春': { lat: 43.8171, lon: 125.3235 },
            '石家庄': { lat: 38.0428, lon: 114.5149 },
            '南宁': { lat: 22.8170, lon: 108.3665 },
            '南昌': { lat: 28.6820, lon: 115.8579 },
            '贵阳': { lat: 26.6470, lon: 106.6302 },
            '太原': { lat: 37.8706, lon: 112.5489 },
            '合肥': { lat: 31.8206, lon: 117.2272 },
            '呼和浩特': { lat: 40.8414, lon: 111.7519 },
            '兰州': { lat: 36.0611, lon: 103.8343 },
            '海口': { lat: 20.0174, lon: 110.3492 },
            '乌鲁木齐': { lat: 43.8256, lon: 87.6168 },
            '拉萨': { lat: 29.6500, lon: 91.1000 },
            '银川': { lat: 38.4872, lon: 106.2309 },
            '西宁': { lat: 36.6232, lon: 101.7804 },
            '三亚': { lat: 18.2528, lon: 109.5120 },
            '桂林': { lat: 25.2737, lon: 110.2903 },
            '洛阳': { lat: 34.6197, lon: 112.4540 },
            '无锡': { lat: 31.4912, lon: 120.3119 },
            '常州': { lat: 31.8112, lon: 119.9740 },
            '南通': { lat: 32.0148, lon: 120.8655 },
            '徐州': { lat: 34.2049, lon: 117.2717 },
            '烟台': { lat: 37.4638, lon: 121.4474 },
            '潍坊': { lat: 36.7070, lon: 119.1618 },
            '佛山': { lat: 23.0218, lon: 113.1219 },
            '珠海': { lat: 22.2707, lon: 113.5767 },
            '惠州': { lat: 23.1115, lon: 114.4131 },
            '中山': { lat: 22.5172, lon: 113.3927 }
        };

        const WMO_CODE_MAP = {
            0: { text: '晴', icon: '☀️' },
            1: { text: '晴间多云', icon: '🌤️' },
            2: { text: '多云', icon: '⛅' },
            3: { text: '阴', icon: '☁️' },
            45: { text: '雾', icon: '🌫️' },
            48: { text: '雾凇', icon: '🌫️' },
            51: { text: '小毛毛雨', icon: '🌦️' },
            53: { text: '毛毛雨', icon: '🌦️' },
            55: { text: '大毛毛雨', icon: '🌦️' },
            56: { text: '冻毛毛雨', icon: '🌨️' },
            57: { text: '大冻毛毛雨', icon: '🌨️' },
            61: { text: '小雨', icon: '🌧️' },
            63: { text: '中雨', icon: '🌧️' },
            65: { text: '大雨', icon: '🌧️' },
            66: { text: '冻雨', icon: '🌨️' },
            67: { text: '大冻雨', icon: '🌨️' },
            71: { text: '小雪', icon: '❄️' },
            73: { text: '中雪', icon: '❄️' },
            75: { text: '大雪', icon: '❄️' },
            77: { text: '雪粒', icon: '❄️' },
            80: { text: '小阵雨', icon: '🌦️' },
            81: { text: '阵雨', icon: '🌦️' },
            82: { text: '强阵雨', icon: '🌧️' },
            85: { text: '小阵雪', icon: '🌨️' },
            86: { text: '大阵雪', icon: '🌨️' },
            95: { text: '雷阵雨', icon: '⛈️' },
            96: { text: '雷阵雨伴冰雹', icon: '⛈️' },
            99: { text: '强雷阵雨伴冰雹', icon: '⛈️' }
        };

        async function fetchWeatherDataWithOpenMeteo(city) {
            const coords = CITY_COORDINATES[city];
            if (!coords) {
                return null;
            }
            
            const url = `https://api.open-meteo.com/v1/forecast?latitude=${coords.lat}&longitude=${coords.lon}&current=temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m&timezone=auto`;
            
            try {
                const response = await fetch(url);
                const data = await response.json();
                
                if (data && data.current) {
                    const weatherCode = data.current.weather_code;
                    const weatherInfo = WMO_CODE_MAP[weatherCode] || { text: '未知', icon: '🌤️' };
                    
                    return {
                        city: city,
                        temp: Math.round(data.current.temperature_2m),
                        text: weatherInfo.text,
                        humidity: data.current.relative_humidity_2m,
                        wind: '',
                        windSpeed: Math.round(data.current.wind_speed_10m),
                        icon: weatherInfo.icon
                    };
                }
                return null;
            } catch (e) {
                console.error('Open-Meteo API 失败:', e);
                return null;
            }
        }

        async function fetchWeatherData(city) {
            const apiKey = appData.settings?.weather_api_key || appData.weatherApiKey || '';
            
            try {
                if (apiKey) {
                    let locationId = null;
                    
                    const geoUrl = `https://geoapi.qweather.com/v2/city/lookup?location=${encodeURIComponent(city)}&key=${apiKey}`;
                    const geoResponse = await fetch(geoUrl);
                    const geoData = await geoResponse.json();
                    
                    if (geoData.code === '200' && geoData.location && geoData.location.length > 0) {
                        locationId = geoData.location[0].id;
                    }
                    
                    if (locationId) {
                        const weatherUrl = `https://devapi.qweather.com/v7/weather/now?location=${locationId}&key=${apiKey}`;
                        const weatherResponse = await fetch(weatherUrl);
                        const weatherData = await weatherResponse.json();
                        
                        if (weatherData.code === '200' && weatherData.now) {
                            const text = WEATHER_CODE_MAP[weatherData.now.icon] || weatherData.now.text;
                            return {
                                city: city,
                                temp: weatherData.now.temp,
                                text: text,
                                humidity: weatherData.now.humidity,
                                wind: weatherData.now.windDir,
                                windSpeed: weatherData.now.windSpeed
                            };
                        }
                    }
                }
                
                const openMeteoData = await fetchWeatherDataWithOpenMeteo(city);
                if (openMeteoData) {
                    return openMeteoData;
                }
                
                return null;
            } catch (e) {
                console.error('获取天气数据失败:', e);
                return null;
            }
        }

        let weatherCache = null;
        let weatherCacheTime = 0;
        const WEATHER_CACHE_DURATION = 30 * 60 * 1000;

        async function getWeather() {
            const now = Date.now();
            const cachedCity = appData.settings?.weather_city || appData.weatherCity || '北京';
            
            if (weatherCache && (now - weatherCacheTime < WEATHER_CACHE_DURATION) && weatherCache.city === cachedCity) {
                return weatherCache;
            }
            
            const data = await fetchWeatherData(cachedCity);
            if (data) {
                weatherCache = data;
                weatherCacheTime = now;
            }
            return data;
        }

        async function renderWeather() {
            const enabled = (appData.settings?.weather_enabled === '1' || appData.settings?.weather_enabled === true || appData.weatherEnabled);
            const weatherDisplay = document.getElementById('weatherDisplay');
            const weatherIcon = document.querySelector('.weather-icon');
            const weatherInfo = document.querySelector('.weather-info');
            
            if (!enabled) {
                if (weatherDisplay) weatherDisplay.textContent = '企业展示平台';
                if (weatherIcon) weatherIcon.innerHTML = '';
                return;
            }
            
            if (weatherDisplay) weatherDisplay.textContent = '加载中...';
            if (weatherIcon) weatherIcon.innerHTML = '';
            
            const data = await getWeather();
            
            if (data) {
                const icon = data.icon || getWeatherIcon(data.text);
                if (weatherDisplay) {
                    weatherDisplay.textContent = `${data.city} ${data.text} ${data.temp}°C`;
                }
                if (weatherIcon) {
                    weatherIcon.innerHTML = `<span style="font-size: 20px;">${icon}</span>`;
                }
            } else {
                if (weatherDisplay) weatherDisplay.textContent = '天气获取失败';
                if (weatherIcon) weatherIcon.innerHTML = '<span style="font-size: 20px;">❓</span>';
            }
        }

        let scheduledCheckTimer = null;
        let carouselCheckTimer = null;

        function restartCarousel() {
            renderCarousel();
        }

        function updateDateTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const weekDays = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'];
            const weekDay = weekDays[now.getDay()];

            document.getElementById('timeDisplay').textContent = `${hours}:${minutes}`;
            document.getElementById('dateDisplay').textContent = `${year}年${month}月${day}日 ${weekDay}`;
        }

        const carousel = {
            currentIndex: 0,
            timer: null,
            items: []
        };

        function isCarouselItemActive(item) {
            const todayStr = getLocalDateString(new Date());
            const startDate = item.startDate || item.start_date || '2020-01-01';
            const endDate = item.endDate || item.end_date || '2099-12-31';

            if (todayStr < startDate || todayStr > endDate) {
                return false;
            }

            const timeType = item.timeType || item.time_type || 'all_day';
            const now = new Date();
            const currentHours = now.getHours();
            const currentMinutes = now.getMinutes();
            const currentTotalMinutes = currentHours * 60 + currentMinutes;

            if (timeType === 'all_day') {
                return true;
            }

            if (timeType === 'work_hours') {
                const workStartMinutes = 9 * 60;
                const workEndMinutes = 18 * 60;
                return currentTotalMinutes >= workStartMinutes && currentTotalMinutes < workEndMinutes;
            }

            if (timeType === 'off_hours') {
                const workStartMinutes = 9 * 60;
                const workEndMinutes = 18 * 60;
                return currentTotalMinutes < workStartMinutes || currentTotalMinutes >= workEndMinutes;
            }

            if (timeType === 'custom' && (item.customStartTime || item.custom_start_time) && (item.customEndTime || item.custom_end_time)) {
                const customStartTime = item.customStartTime || item.custom_start_time || '09:00';
                const customEndTime = item.customEndTime || item.custom_end_time || '18:00';
                const [startH, startM] = customStartTime.split(':').map(Number);
                const [endH, endM] = customEndTime.split(':').map(Number);
                const customStartMinutes = startH * 60 + startM;
                const customEndMinutes = endH * 60 + endM;

                if (customStartMinutes < customEndMinutes) {
                    return currentTotalMinutes >= customStartMinutes && currentTotalMinutes < customEndMinutes;
                } else {
                    return currentTotalMinutes >= customStartMinutes || currentTotalMinutes < customEndMinutes;
                }
            }

            return true;
        }

        function getActiveCarouselItems() {
            const items = appData.carousel || [];
            return items.filter(item => isCarouselItemActive(item));
        }

        function renderCarousel() {
            const allItems = appData.carousel || [];
            const activeItems = getActiveCarouselItems();
            carousel.items = allItems;

            const slidesContainer = document.getElementById('carouselSlides');
            const indicatorsContainer = document.getElementById('carouselIndicators');

            slidesContainer.innerHTML = '';
            indicatorsContainer.innerHTML = '';

            if (activeItems.length === 0) {
                const defaultSlide = document.createElement('div');
                defaultSlide.className = 'carousel-slide active';
                defaultSlide.style.background = 'linear-gradient(135deg, #071324 0%, #0d1b2a 100%)';
                slidesContainer.appendChild(defaultSlide);
                return;
            }

            activeItems.forEach((item, index) => {
                const slide = document.createElement('div');
                slide.className = `carousel-slide ${index === 0 ? 'active' : ''}`;
                slide.style.backgroundImage = `url(${item.image})`;
                if (item.link && item.link !== '#') {
                    slide.style.cursor = 'pointer';
                    slide.onclick = () => window.open(item.link, '_blank');
                }
                slidesContainer.appendChild(slide);

                const indicator = document.createElement('div');
                indicator.className = `carousel-indicator ${index === 0 ? 'active' : ''}`;
                indicator.onclick = () => goToSlide(index);
                indicatorsContainer.appendChild(indicator);
            });

            carousel.currentIndex = 0;
        }

        function goToSlide(index) {
            const slides = document.querySelectorAll('.carousel-slide');
            const indicators = document.querySelectorAll('.carousel-indicator');

            if (slides.length === 0) return;

            slides.forEach(slide => slide.classList.remove('active'));
            indicators.forEach(ind => ind.classList.remove('active'));

            carousel.currentIndex = index % slides.length;
            slides[carousel.currentIndex].classList.add('active');
            indicators[carousel.currentIndex].classList.add('active');
        }

        carousel.next = function() {
            goToSlide(carousel.currentIndex + 1);
        };

        carousel.prev = function() {
            const slides = document.querySelectorAll('.carousel-slide');
            const newIndex = carousel.currentIndex - 1;
            goToSlide(newIndex < 0 ? slides.length - 1 : newIndex);
        };

        function startCarousel() {
            if (carousel.timer) {
                clearInterval(carousel.timer);
            }
            const interval = (parseInt(appData.settings?.carousel_interval) || appData.carouselInterval || 3) * 1000;
            carousel.timer = setInterval(() => carousel.next(), interval);
        }

        function renderMainTitle() {
            const mainTitle = appData.settings?.main_title || appData.mainTitle || '企业信息展示平台';
            const subTitle = appData.settings?.sub_title || appData.subTitle || 'ENTERPRISE INFORMATION DISPLAY';
            document.getElementById('mainTitle').textContent = mainTitle;
            document.getElementById('subTitle').textContent = subTitle;
        }

        function renderHeaderLeft() {
            const leftLogo = appData.settings?.left_logo || appData.leftLogo || '';
            const leftTitle = appData.settings?.left_title || appData.leftTitle || 'MAX DISPLAY';
            const logoIcon = document.getElementById('leftLogoIcon');
            const logoText = document.getElementById('leftLogoText');

            if (leftLogo) {
                logoIcon.style.backgroundImage = `url(${leftLogo})`;
                logoIcon.style.backgroundSize = 'cover';
                logoIcon.style.backgroundPosition = 'center';
                logoIcon.style.backgroundRepeat = 'no-repeat';
            } else {
                logoIcon.style.backgroundImage = '';
            }

            logoText.textContent = leftTitle;
        }

        function renderBackgroundImage() {
            const bgImage = appData.settings?.background_image || appData.backgroundImage || '';
            const heroSection = document.querySelector('.hero-section');
            if (bgImage) {
                heroSection.style.backgroundImage = `url(${bgImage})`;
            } else {
                heroSection.style.backgroundImage = '';
            }
        }

        function loadAppearanceInputs() {
            const leftTitle = appData.settings?.left_title || appData.leftTitle || 'MAX DISPLAY';
            const leftLogo = appData.settings?.left_logo || appData.leftLogo || '';
            const mainTitle = appData.settings?.main_title || appData.mainTitle || '企业信息展示平台';
            const subTitle = appData.settings?.sub_title || appData.subTitle || 'ENTERPRISE INFORMATION DISPLAY';
            const welcome = appData.settings?.welcome || appData.welcome || '欢迎光临';
            const bgImage = appData.settings?.background_image || appData.backgroundImage || '';
            
            document.getElementById('leftTitleInput').value = leftTitle;
            document.getElementById('mainTitleInput').value = mainTitle;
            document.getElementById('subTitleInput').value = subTitle;
            document.getElementById('welcomeInput').value = welcome;
            
            const leftLogoPreviewContainer = document.getElementById('leftLogoPreviewContainer');
            const leftLogoPreviewImg = document.getElementById('leftLogoPreview');
            if (leftLogo) {
                leftLogoPreviewContainer.style.display = 'block';
                leftLogoPreviewImg.src = leftLogo;
            } else {
                leftLogoPreviewContainer.style.display = 'none';
            }
            
            const previewContainer = document.getElementById('bgImagePreviewContainer');
            const previewImg = document.getElementById('bgImagePreview');
            if (bgImage) {
                previewContainer.style.display = 'block';
                previewImg.src = bgImage;
            } else {
                previewContainer.style.display = 'none';
            }
        }

        async function saveAppearanceSettings() {
            const leftTitle = document.getElementById('leftTitleInput').value.trim() || 'MAX DISPLAY';
            const mainTitle = document.getElementById('mainTitleInput').value.trim() || '企业信息展示平台';
            const subTitle = document.getElementById('subTitleInput').value.trim() || 'ENTERPRISE INFORMATION DISPLAY';
            const welcome = document.getElementById('welcomeInput').value.trim() || '欢迎光临';
            
            const data = {
                left_title: leftTitle,
                main_title: mainTitle,
                sub_title: subTitle,
                welcome: welcome
            };
            
            const result = await apiPost('update_appearance', data);
            
            if (result.success) {
                appData.settings.left_title = leftTitle;
                appData.settings.main_title = mainTitle;
                appData.settings.sub_title = subTitle;
                appData.settings.welcome = welcome;
                showSaveIndicator();
                renderHeaderLeft();
                renderMainTitle();
                renderWelcomeText();
            } else {
                alert('保存失败: ' + (result.message || '未知错误'));
            }
        }

        async function clearBackgroundImage() {
            if (!confirm('确定要清除背景图片吗？')) return;
            
            const result = await apiPost('update_appearance', { background_image: '' });
            
            if (result.success) {
                appData.settings.background_image = '';
                document.getElementById('bgImagePreviewContainer').style.display = 'none';
                document.getElementById('bgImageInput').value = '';
                renderBackgroundImage();
                showSaveIndicator();
            } else {
                alert('清除失败: ' + (result.message || '未知错误'));
            }
        }

        async function clearLeftLogo() {
            if (!confirm('确定要清除左上角LOGO吗？')) return;
            
            const result = await apiPost('update_appearance', { left_logo: '' });
            
            if (result.success) {
                appData.settings.left_logo = '';
                document.getElementById('leftLogoPreviewContainer').style.display = 'none';
                document.getElementById('leftLogoInput').value = '';
                renderHeaderLeft();
                showSaveIndicator();
            } else {
                alert('清除失败: ' + (result.message || '未知错误'));
            }
        }

        function renderWelcomeText() {
            const welcome = appData.settings?.welcome || appData.welcome || '欢迎光临';
            document.getElementById('welcomeText').textContent = welcome;
        }

        function loadDefaultGreeting() {
            const greeting = appData.settings?.default_greeting || appData.defaultGreeting || '';
            document.getElementById('defaultGreeting').value = greeting;
        }

        function renderFestival() {
            const festivals = appData.festivals || [];
            const defaultGreeting = appData.settings?.default_greeting || appData.defaultGreeting || '';
            const todayStr = getLocalDateString(new Date());

            const activeFestivals = [];
            for (const festival of festivals) {
                const startDate = festival.startDate || festival.start_date;
                const endDate = festival.endDate || festival.end_date;
                if (todayStr >= startDate && todayStr <= endDate) {
                    activeFestivals.push(festival);
                }
            }

            const container = document.getElementById('festivalCardsContainer');
            container.innerHTML = '';

            if (activeFestivals.length > 0) {
                activeFestivals.forEach(festival => {
                    const card = document.createElement('div');
                    card.className = 'festival-card';
                    card.innerHTML = `
                        <div class="festival-title">${escapeHtml(festival.title)}</div>
                        <div class="festival-message">${escapeHtml(festival.message)}</div>
                    `;
                    container.appendChild(card);
                });
            } else if (defaultGreeting) {
                const card = document.createElement('div');
                card.className = 'festival-card';
                card.innerHTML = `
                    <div class="festival-title">温馨提示</div>
                    <div class="festival-message">${escapeHtml(defaultGreeting)}</div>
                `;
                container.appendChild(card);
            }
        }

        function renderExternalUrls() {
            const urls = appData.externalUrls || [];
            const section = document.getElementById('externalSection');
            const tabsContainer = document.getElementById('externalTabs');
            const iframe = document.getElementById('externalIframe');
            
            if (urls.length === 0) {
                section.style.display = 'none';
                return;
            }
            
            section.style.display = 'block';
            tabsContainer.innerHTML = '';
            
            urls.forEach((item, index) => {
                const tab = document.createElement('div');
                tab.className = `external-tab ${index === 0 ? 'active' : ''}`;
                tab.textContent = item.name;
                tab.onclick = () => switchExternalTab(index);
                tabsContainer.appendChild(tab);
            });
            
            if (urls.length > 0) {
                iframe.src = urls[0].url;
            }
        }

        function switchExternalTab(index) {
            const urls = appData.externalUrls || [];
            const tabs = document.querySelectorAll('.external-tab');
            const iframe = document.getElementById('externalIframe');
            
            tabs.forEach((tab, i) => {
                tab.classList.toggle('active', i === index);
            });
            
            if (urls[index]) {
                iframe.src = urls[index].url;
            }
        }

        function renderExternalList() {
            const items = appData.externalUrls || [];
            const container = document.getElementById('externalList');
            
            if (items.length === 0) {
                container.innerHTML = '<div class="empty-state">暂无外部链接，请添加</div>';
                return;
            }
            
            container.innerHTML = items.map(item => `
                <div class="list-item">
                    <div class="list-item-content">
                        <div class="list-item-title">${escapeHtml(item.name)}</div>
                        <div class="list-item-subtitle">${escapeHtml(item.url)}</div>
                    </div>
                    <div class="list-item-actions">
                        <button class="btn btn-secondary btn-sm" onclick="editExternalUrl('${item.id}')">编辑</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteExternalUrl('${item.id}')">删除</button>
                    </div>
                </div>
            `).join('');
        }

        function addExternalUrl() {
            const items = appData.externalUrls || [];
            const newItem = {
                id: generateId(),
                name: '新链接',
                url: 'https://example.com'
            };
            items.push(newItem);
            appData.externalUrls = items;
            renderExternalList();
            renderExternalUrls();
            editExternalUrl(newItem.id);
        }

        function editExternalUrl(id) {
            const items = appData.externalUrls || [];
            const item = items.find(i => i.id == id);
            if (!item) return;
            
            const container = document.getElementById('externalList');
            const editForm = document.createElement('div');
            editForm.className = 'edit-form';
            editForm.id = `edit-external-${id}`;
            editForm.innerHTML = `
                <div class="edit-form-header">
                    <span class="edit-form-title">编辑外部链接</span>
                </div>
                <div class="form-group">
                    <label class="form-label">链接名称</label>
                    <input type="text" class="form-input" id="external-name-${id}" value="${escapeHtml(item.name)}">
                </div>
                <div class="form-group">
                    <label class="form-label">URL地址</label>
                    <input type="text" class="form-input" id="external-url-${id}" value="${escapeHtml(item.url)}" placeholder="https://...">
                </div>
                <div class="edit-form-actions">
                    <button class="btn btn-primary" onclick="saveExternalUrl('${id}')">保存</button>
                    <button class="btn btn-ghost" onclick="cancelEditExternal('${id}')">取消</button>
                </div>
            `;
            container.prepend(editForm);
        }

        async function saveExternalUrl(id) {
            const items = appData.externalUrls || [];
            const index = items.findIndex(i => i.id == id);
            if (index === -1) return;
            
            items[index].name = document.getElementById(`external-name-${id}`).value.trim() || '未命名';
            items[index].url = document.getElementById(`external-url-${id}`).value.trim();
            
            const result = await apiPost('update_external', { items: items });
            
            if (result.success) {
                appData.externalUrls = items;
                cancelEditExternal(id);
                renderExternalList();
                renderExternalUrls();
                showSaveIndicator();
            } else {
                alert('保存失败: ' + (result.message || '未知错误'));
            }
        }

        function cancelEditExternal(id) {
            const form = document.getElementById(`edit-external-${id}`);
            if (form) form.remove();
        }

        async function deleteExternalUrl(id) {
            if (!confirm('确定要删除这个外部链接吗？')) return;
            
            let items = appData.externalUrls || [];
            items = items.filter(i => i.id != id);
            
            const result = await apiPost('update_external', { items: items });
            
            if (result.success) {
                appData.externalUrls = items;
                renderExternalList();
                renderExternalUrls();
                showSaveIndicator();
            } else {
                alert('删除失败: ' + (result.message || '未知错误'));
            }
        }

        function isTimeInScheduledRange(item) {
            const now = new Date();
            const currentHours = now.getHours();
            const currentMinutes = now.getMinutes();
            const currentTotalMinutes = currentHours * 60 + currentMinutes;

            const timeType = item.timeType || item.time_type || 'all_day';

            if (timeType === 'all_day') {
                return true;
            }

            if (timeType === 'work_hours') {
                const workStartMinutes = 9 * 60;
                const workEndMinutes = 18 * 60;
                return currentTotalMinutes >= workStartMinutes && currentTotalMinutes < workEndMinutes;
            }

            if (timeType === 'off_hours') {
                const workStartMinutes = 9 * 60;
                const workEndMinutes = 18 * 60;
                return currentTotalMinutes < workStartMinutes || currentTotalMinutes >= workEndMinutes;
            }

            if (timeType === 'custom' && (item.customStartTime || item.custom_start_time) && (item.customEndTime || item.custom_end_time)) {
                const customStartTime = item.customStartTime || item.custom_start_time || '09:00';
                const customEndTime = item.customEndTime || item.custom_end_time || '18:00';
                const [startH, startM] = customStartTime.split(':').map(Number);
                const [endH, endM] = customEndTime.split(':').map(Number);
                const customStartMinutes = startH * 60 + startM;
                const customEndMinutes = endH * 60 + endM;

                if (customStartMinutes < customEndMinutes) {
                    return currentTotalMinutes >= customStartMinutes && currentTotalMinutes < customEndMinutes;
                } else {
                    return currentTotalMinutes >= customStartMinutes || currentTotalMinutes < customEndMinutes;
                }
            }

            return true;
        }

        function getActiveScheduledContent() {
            const items = appData.scheduledContent || [];
            const todayStr = getLocalDateString(new Date());
            const activeItems = [];
            
            for (const item of items) {
                const startDate = item.startDate || item.start_date;
                const endDate = item.endDate || item.end_date;
                if (todayStr >= startDate && todayStr <= endDate && isTimeInScheduledRange(item)) {
                    activeItems.push(item);
                }
            }
            return activeItems;
        }

        function renderScheduledList() {
            const items = appData.scheduledContent || [];
            const container = document.getElementById('scheduledList');
            
            if (items.length === 0) {
                container.innerHTML = '<div class="empty-state">暂无时段文案，请添加</div>';
                return;
            }
            
            container.innerHTML = items.map(item => `
                <div class="list-item">
                    <div class="list-item-content">
                        <div class="list-item-title">${escapeHtml(item.name)}</div>
                        <div class="list-item-subtitle">${item.startDate || item.start_date} 至 ${item.endDate || item.end_date}</div>
                    </div>
                    <div class="list-item-actions">
                        <button class="btn btn-secondary btn-sm" onclick="editScheduledContent('${item.id}')">编辑</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteScheduledContent('${item.id}')">删除</button>
                    </div>
                </div>
            `).join('');
        }

        function addScheduledContent() {
            const items = appData.scheduledContent || [];
            const today = new Date();
            const startDate = getLocalDateString(today);
            const endDate = getLocalDateString(new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000));

            const newItem = {
                id: generateId(),
                name: '新时段文案',
                mainTitle: '自定义主标题',
                subTitle: 'CUSTOM SUB TITLE',
                welcome: '自定义欢迎词',
                startDate: startDate,
                endDate: endDate,
                timeType: 'all_day',
                customStartTime: '',
                customEndTime: ''
            };
            items.push(newItem);
            appData.scheduledContent = items;
            renderScheduledList();
            updateScheduledDisplay();
            editScheduledContent(newItem.id);
        }

        function editScheduledContent(id) {
            const items = appData.scheduledContent || [];
            const item = items.find(i => i.id == id);
            if (!item) return;

            const timeType = item.timeType || item.time_type || 'all_day';
            const customStartTime = item.customStartTime || item.custom_start_time || '09:00';
            const customEndTime = item.customEndTime || item.custom_end_time || '18:00';

            const timeTypeOptions = [
                { value: 'all_day', label: '全天' },
                { value: 'work_hours', label: '上班时间（09:00-18:00）' },
                { value: 'off_hours', label: '下班时间（18:00-次日09:00）' },
                { value: 'custom', label: '自定义时间' }
            ];

            const timeTypeHtml = timeTypeOptions.map(opt =>
                `<option value="${opt.value}" ${timeType === opt.value ? 'selected' : ''}>${opt.label}</option>`
            ).join('');

            const customTimeStyle = timeType === 'custom' ? 'display: flex;' : 'display: none;';

            const container = document.getElementById('scheduledList');
            const editForm = document.createElement('div');
            editForm.className = 'edit-form';
            editForm.id = `edit-scheduled-${id}`;
            editForm.innerHTML = `
                <div class="edit-form-header">
                    <span class="edit-form-title">编辑时段文案</span>
                </div>
                <div class="form-group">
                    <label class="form-label">时段名称</label>
                    <input type="text" class="form-input" id="scheduled-name-${id}" value="${escapeHtml(item.name)}">
                </div>
                <div class="form-group">
                    <label class="form-label">主标题</label>
                    <input type="text" class="form-input" id="scheduled-main-${id}" value="${escapeHtml(item.mainTitle || item.main_title)}" placeholder="例如：企业信息展示平台">
                </div>
                <div class="form-group">
                    <label class="form-label">副标题</label>
                    <input type="text" class="form-input" id="scheduled-sub-${id}" value="${escapeHtml(item.subTitle || item.sub_title)}" placeholder="例如：ENTERPRISE INFORMATION DISPLAY">
                </div>
                <div class="form-group">
                    <label class="form-label">欢迎词</label>
                    <textarea class="form-textarea" id="scheduled-welcome-${id}" placeholder="请输入欢迎词...">${escapeHtml(item.welcome)}</textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">开始日期</label>
                        <input type="date" class="form-input" id="scheduled-start-${id}" value="${item.startDate || item.start_date}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">结束日期</label>
                        <input type="date" class="form-input" id="scheduled-end-${id}" value="${item.endDate || item.end_date}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">时间类型</label>
                    <select class="form-input" id="scheduled-timeType-${id}" onchange="toggleCustomTime('${id}')">
                        ${timeTypeHtml}
                    </select>
                </div>
                <div class="form-row" id="custom-time-${id}" style="${customTimeStyle}">
                    <div class="form-group">
                        <label class="form-label">自定义开始时间</label>
                        <input type="time" class="form-input" id="scheduled-customStart-${id}" value="${customStartTime}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">自定义结束时间</label>
                        <input type="time" class="form-input" id="scheduled-customEnd-${id}" value="${customEndTime}">
                    </div>
                </div>
                <div class="edit-form-actions">
                    <button class="btn btn-primary" onclick="saveScheduledContent('${id}')">保存</button>
                    <button class="btn btn-ghost" onclick="cancelEditScheduled('${id}')">取消</button>
                </div>
            `;
            container.prepend(editForm);
        }

        function toggleCustomTime(id) {
            const timeType = document.getElementById(`scheduled-timeType-${id}`).value;
            const customTimeDiv = document.getElementById(`custom-time-${id}`);
            if (timeType === 'custom') {
                customTimeDiv.style.display = 'flex';
            } else {
                customTimeDiv.style.display = 'none';
            }
        }

        async function saveScheduledContent(id) {
            const items = appData.scheduledContent || [];
            const index = items.findIndex(i => i.id == id);
            if (index === -1) return;

            const timeType = document.getElementById(`scheduled-timeType-${id}`).value;
            let customStartTime = '';
            let customEndTime = '';
            if (timeType === 'custom') {
                customStartTime = document.getElementById(`scheduled-customStart-${id}`).value;
                customEndTime = document.getElementById(`scheduled-customEnd-${id}`).value;
            }

            items[index].name = document.getElementById(`scheduled-name-${id}`).value.trim() || '未命名';
            items[index].mainTitle = document.getElementById(`scheduled-main-${id}`).value.trim();
            items[index].subTitle = document.getElementById(`scheduled-sub-${id}`).value.trim();
            items[index].welcome = document.getElementById(`scheduled-welcome-${id}`).value.trim();
            items[index].startDate = document.getElementById(`scheduled-start-${id}`).value;
            items[index].endDate = document.getElementById(`scheduled-end-${id}`).value;
            items[index].timeType = timeType;
            items[index].customStartTime = customStartTime;
            items[index].customEndTime = customEndTime;

            const result = await apiPost('update_scheduled', { items: items });
            
            if (result.success) {
                appData.scheduledContent = items;
                cancelEditScheduled(id);
                renderScheduledList();
                restartScheduledCarousel();
                showSaveIndicator();
            } else {
                alert('保存失败: ' + (result.message || '未知错误'));
            }
        }

        function cancelEditScheduled(id) {
            const form = document.getElementById(`edit-scheduled-${id}`);
            if (form) form.remove();
        }

        async function deleteScheduledContent(id) {
            if (!confirm('确定要删除这个时段文案吗？')) return;
            
            let items = appData.scheduledContent || [];
            items = items.filter(i => i.id != id);
            
            const result = await apiPost('update_scheduled', { items: items });
            
            if (result.success) {
                appData.scheduledContent = items;
                renderScheduledList();
                updateScheduledDisplay();
                showSaveIndicator();
            } else {
                alert('删除失败: ' + (result.message || '未知错误'));
            }
        }

        const scheduledCarousel = {
            currentIndex: 0,
            timer: null,
            activeItems: [],
            prevActiveCount: -1
        };

        function showScheduledItem(index) {
            const items = scheduledCarousel.activeItems;
            if (items.length === 0) {
                renderMainTitle();
                renderWelcomeText();
                return;
            }
            const item = items[index % items.length];
            document.getElementById('mainTitle').textContent = item.mainTitle || item.main_title;
            document.getElementById('subTitle').textContent = item.subTitle || item.sub_title;
            document.getElementById('welcomeText').textContent = item.welcome;
            scheduledCarousel.currentIndex = index % items.length;
        }

        function startScheduledCarousel() {
            if (scheduledCarousel.timer) {
                clearInterval(scheduledCarousel.timer);
                scheduledCarousel.timer = null;
            }
            const items = getActiveScheduledContent();
            scheduledCarousel.activeItems = items;
            
            if (items.length === 0) {
                renderMainTitle();
                renderWelcomeText();
                return;
            }
            
            showScheduledItem(scheduledCarousel.currentIndex);
            
            if (items.length > 1) {
                const interval = (parseInt(appData.settings?.scheduled_carousel_interval) || appData.scheduledCarouselInterval || 10) * 1000;
                scheduledCarousel.timer = setInterval(() => {
                    showScheduledItem(scheduledCarousel.currentIndex + 1);
                }, interval);
            }
        }

        function stopScheduledCarousel() {
            if (scheduledCarousel.timer) {
                clearInterval(scheduledCarousel.timer);
                scheduledCarousel.timer = null;
            }
        }

        function restartScheduledCarousel() {
            const currentActiveCount = scheduledCarousel.activeItems.length;
            const items = getActiveScheduledContent();
            const newActiveCount = items.length;
            const idsChanged = JSON.stringify(scheduledCarousel.activeItems.map(i => i.id)) !== JSON.stringify(items.map(i => i.id));
            
            if (currentActiveCount !== newActiveCount || idsChanged) {
                scheduledCarousel.currentIndex = 0;
                startScheduledCarousel();
            }
        }

        function updateScheduledDisplay() {
            restartScheduledCarousel();
        }

        function renderIpList() {
            const whitelist = appData.ipWhitelist || [];
            const container = document.getElementById('ipList');

            if (whitelist.length === 0) {
                container.innerHTML = '<div class="empty-state">暂无白名单IP，请添加</div>';
                return;
            }

            container.innerHTML = whitelist.map(ip => `
                <div class="ip-tag">
                    <span>${escapeHtml(ip)}</span>
                    <button class="ip-remove" onclick="removeIpFromWhitelist('${ip}')" title="移除">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>
            `).join('');
        }

        async function addIpToWhitelist() {
            const input = document.getElementById('newIpInput');
            const ip = input.value.trim();

            if (!ip) {
                alert('请输入IP地址');
                return;
            }

            const ipRegex = /^(\d{1,3}\.){3}\d{1,3}$/;
            if (!ipRegex.test(ip)) {
                alert('请输入有效的IP地址格式');
                return;
            }

            const whitelist = appData.ipWhitelist || [];
            if (whitelist.includes(ip)) {
                alert('该IP已在白名单中');
                return;
            }

            whitelist.push(ip);
            
            const result = await apiPost('update_whitelist', { ips: whitelist });
            
            if (result.success) {
                appData.ipWhitelist = whitelist;
                input.value = '';
                renderIpList();
                showSaveIndicator();
            } else {
                alert('添加失败: ' + (result.message || '未知错误'));
            }
        }

        async function removeIpFromWhitelist(ip) {
            if (!confirm(`确定要移除IP ${ip} 吗？`)) return;

            const whitelist = appData.ipWhitelist || [];
            const index = whitelist.indexOf(ip);
            if (index > -1) {
                whitelist.splice(index, 1);
                
                const result = await apiPost('update_whitelist', { ips: whitelist });
                
                if (result.success) {
                    appData.ipWhitelist = whitelist;
                    renderIpList();
                    showSaveIndicator();
                } else {
                    alert('移除失败: ' + (result.message || '未知错误'));
                }
            }
        }

        function renderCarouselList() {
            const items = appData.carousel || [];
            const container = document.getElementById('carouselList');

            if (items.length === 0) {
                container.innerHTML = '<div class="empty-state">暂无轮播图，请添加</div>';
                return;
            }

            container.innerHTML = items.map((item, index) => `
                <div class="list-item">
                    <div class="list-item-content">
                        <div class="list-item-title">${escapeHtml(item.title || '轮播图 ' + (index + 1))}</div>
                        <div class="list-item-subtitle">图片: ${escapeHtml(String(item.image).substring(0, 60))}${String(item.image).length > 60 ? '...' : ''}</div>
                    </div>
                    <div class="list-item-actions">
                        <button class="btn btn-secondary btn-sm" onclick="editCarouselItem('${item.id}')">编辑</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteCarouselItem('${item.id}')">删除</button>
                    </div>
                </div>
            `).join('');
        }

        function addCarouselItem() {
            const items = appData.carousel || [];
            const today = new Date();
            const startDate = getLocalDateString(today);
            const endDate = getLocalDateString(new Date(today.getTime() + 30 * 24 * 60 * 60 * 1000));
            
            const newItem = {
                id: generateId(),
                image: 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1920&q=80',
                title: '新轮播图',
                link: '#',
                startDate: startDate,
                endDate: endDate,
                timeType: 'all_day',
                customStartTime: '',
                customEndTime: ''
            };
            items.push(newItem);
            appData.carousel = items;
            renderCarouselList();
            renderCarousel();
            editCarouselItem(newItem.id);
        }

        function editCarouselItem(id) {
            const items = appData.carousel || [];
            const item = items.find(i => i.id == id);
            if (!item) return;

            const container = document.getElementById('carouselList');
            const editForm = document.createElement('div');
            editForm.className = 'edit-form';
            editForm.id = `edit-form-${id}`;
            
            const timeTypeOptions = [
                { value: 'all_day', label: '全天显示' },
                { value: 'work_hours', label: '工作时间 (9:00-18:00)' },
                { value: 'off_hours', label: '非工作时间' },
                { value: 'custom', label: '自定义时间' }
            ];

            const timeType = item.timeType || item.time_type || 'all_day';
            const customStartTime = item.customStartTime || item.custom_start_time || '09:00';
            const customEndTime = item.customEndTime || item.custom_end_time || '18:00';

            editForm.innerHTML = `
                <div class="edit-form-header">
                    <span class="edit-form-title">编辑轮播图</span>
                </div>
                <div class="form-group">
                    <label class="form-label">标题</label>
                    <input type="text" class="form-input" id="edit-title-${id}" value="${escapeHtml(item.title)}">
                </div>
                <div class="form-group">
                    <label class="form-label">图片URL</label>
                    <input type="text" class="form-input" id="edit-image-${id}" value="${escapeHtml(item.image)}">
                </div>
                <div class="form-group">
                    <label class="form-label">或上传本地图片</label>
                    <input type="file" class="form-input" id="carouselImageInput-${id}" accept="image/*" style="padding: 8px;" onchange="handleCarouselImageUpload('${id}')">
                    <small style="color: var(--text-secondary); margin-top: 8px; display: block;">支持 JPG、PNG、WebP 格式，建议分辨率 1920x1080 以上，大小不超过5MB</small>
                </div>
                <div class="form-group" id="carouselImagePreview-${id}" style="display: ${item.image ? 'block' : 'none'};">
                    <label class="form-label">当前图片预览</label>
                    <img id="carouselImgPreview-${id}" src="${item.image}" style="width: 100%; max-height: 200px; object-fit: cover; border-radius: 8px;">
                </div>
                <div class="form-group">
                    <label class="form-label">链接地址</label>
                    <input type="text" class="form-input" id="edit-link-${id}" value="${escapeHtml(item.link)}">
                </div>
                <div class="form-group">
                    <label class="form-label">开始日期</label>
                    <input type="date" class="form-input" id="edit-startDate-${id}" value="${item.startDate || item.start_date || getLocalDateString(new Date())}">
                </div>
                <div class="form-group">
                    <label class="form-label">结束日期</label>
                    <input type="date" class="form-input" id="edit-endDate-${id}" value="${item.endDate || item.end_date || getLocalDateString(new Date(new Date().getTime() + 30 * 24 * 60 * 60 * 1000))}">
                </div>
                <div class="form-group">
                    <label class="form-label">显示时段</label>
                    <select class="form-input" id="edit-timeType-${id}" onchange="toggleCustomCarouselTime('${id}')">
                        ${timeTypeOptions.map(opt => 
                            `<option value="${opt.value}" ${timeType === opt.value ? 'selected' : ''}>${opt.label}</option>`
                        ).join('')}
                    </select>
                </div>
                <div class="form-group" id="customTimeGroup-${id}" style="display: ${timeType === 'custom' ? 'block' : 'none'};">
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <label class="form-label">开始时间</label>
                            <input type="time" class="form-input" id="edit-customStartTime-${id}" value="${customStartTime}">
                        </div>
                        <div style="flex: 1;">
                            <label class="form-label">结束时间</label>
                            <input type="time" class="form-input" id="edit-customEndTime-${id}" value="${customEndTime}">
                        </div>
                    </div>
                </div>
                <div class="edit-form-actions">
                    <button class="btn btn-primary" onclick="saveCarouselItem('${id}')">保存</button>
                    <button class="btn btn-ghost" onclick="cancelEditCarousel('${id}')">取消</button>
                </div>
            `;
            container.prepend(editForm);
        }

        function toggleCustomCarouselTime(id) {
            const timeType = document.getElementById(`edit-timeType-${id}`).value;
            const customGroup = document.getElementById(`customTimeGroup-${id}`);
            customGroup.style.display = timeType === 'custom' ? 'block' : 'none';
        }

        async function handleCarouselImageUpload(id) {
            const input = document.getElementById(`carouselImageInput-${id}`);
            const file = input.files[0];
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                alert('请选择图片文件');
                return;
            }

            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('图片大小不能超过5MB，请压缩后再上传');
                return;
            }

            const formData = new FormData();
            formData.append('file', file);
            
            const result = await apiPost('upload_image', formData, false);
            
            if (result.success) {
                document.getElementById(`edit-image-${id}`).value = result.url;
                document.getElementById(`carouselImagePreview-${id}`).style.display = 'block';
                document.getElementById(`carouselImgPreview-${id}`).src = result.url;
                showSaveIndicator();
            } else {
                alert('上传失败: ' + (result.message || '未知错误'));
            }
        }

        async function saveCarouselItem(id) {
            const items = appData.carousel || [];
            const index = items.findIndex(i => i.id == id);
            if (index === -1) return;

            items[index].title = document.getElementById(`edit-title-${id}`).value.trim();
            items[index].image = document.getElementById(`edit-image-${id}`).value.trim();
            items[index].link = document.getElementById(`edit-link-${id}`).value.trim() || '#';
            items[index].startDate = document.getElementById(`edit-startDate-${id}`).value;
            items[index].endDate = document.getElementById(`edit-endDate-${id}`).value;
            items[index].timeType = document.getElementById(`edit-timeType-${id}`).value;
            items[index].customStartTime = document.getElementById(`edit-customStartTime-${id}`)?.value || '';
            items[index].customEndTime = document.getElementById(`edit-customEndTime-${id}`)?.value || '';

            const result = await apiPost('update_carousel', { items: items });
            
            if (result.success) {
                appData.carousel = items;
                cancelEditCarousel(id);
                renderCarouselList();
                renderCarousel();
                showSaveIndicator();
            } else {
                alert('保存失败: ' + (result.message || '未知错误'));
            }
        }

        function cancelEditCarousel(id) {
            const form = document.getElementById(`edit-form-${id}`);
            if (form) form.remove();
        }

        async function deleteCarouselItem(id) {
            if (!confirm('确定要删除这个轮播图吗？')) return;

            let items = appData.carousel || [];
            items = items.filter(i => i.id != id);

            const result = await apiPost('update_carousel', { items: items });
            
            if (result.success) {
                appData.carousel = items;
                renderCarouselList();
                renderCarousel();
                showSaveIndicator();
            } else {
                alert('删除失败: ' + (result.message || '未知错误'));
            }
        }

        function renderFestivalList() {
            const items = appData.festivals || [];
            const container = document.getElementById('festivalList');

            if (items.length === 0) {
                container.innerHTML = '<div class="empty-state">暂无节日，请添加</div>';
                return;
            }

            container.innerHTML = items.map(item => `
                <div class="list-item">
                    <div class="list-item-content">
                        <div class="list-item-title">${escapeHtml(item.title)}</div>
                        <div class="list-item-subtitle">${item.startDate || item.start_date} 至 ${item.endDate || item.end_date}</div>
                    </div>
                    <div class="list-item-actions">
                        <button class="btn btn-secondary btn-sm" onclick="editFestivalItem('${item.id}')">编辑</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteFestivalItem('${item.id}')">删除</button>
                    </div>
                </div>
            `).join('');
        }

        function addFestivalItem() {
            const items = appData.festivals || [];
            const today = new Date();
            const startDate = today.toISOString().split('T')[0];
            const endDate = new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

            const newItem = {
                id: generateId(),
                title: '新节日',
                message: '节日快乐！',
                startDate: startDate,
                endDate: endDate
            };
            items.push(newItem);
            appData.festivals = items;
            renderFestivalList();
            renderFestival();
            editFestivalItem(newItem.id);
        }

        function editFestivalItem(id) {
            const items = appData.festivals || [];
            const item = items.find(i => i.id == id);
            if (!item) return;

            const container = document.getElementById('festivalList');
            const editForm = document.createElement('div');
            editForm.className = 'edit-form';
            editForm.id = `edit-festival-${id}`;
            editForm.innerHTML = `
                <div class="edit-form-header">
                    <span class="edit-form-title">编辑节日</span>
                </div>
                <div class="form-group">
                    <label class="form-label">节日标题</label>
                    <input type="text" class="form-input" id="festival-title-${id}" value="${escapeHtml(item.title)}">
                </div>
                <div class="form-group">
                    <label class="form-label">祝福语</label>
                    <textarea class="form-textarea" id="festival-message-${id}">${escapeHtml(item.message)}</textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">开始日期</label>
                        <input type="date" class="form-input" id="festival-start-${id}" value="${item.startDate || item.start_date}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">结束日期</label>
                        <input type="date" class="form-input" id="festival-end-${id}" value="${item.endDate || item.end_date}">
                    </div>
                </div>
                <div class="edit-form-actions">
                    <button class="btn btn-primary" onclick="saveFestivalItem('${id}')">保存</button>
                    <button class="btn btn-ghost" onclick="cancelEditFestival('${id}')">取消</button>
                </div>
            `;
            container.prepend(editForm);
        }

        async function saveFestivalItem(id) {
            const items = appData.festivals || [];
            const index = items.findIndex(i => i.id == id);
            if (index === -1) return;

            items[index].title = document.getElementById(`festival-title-${id}`).value.trim();
            items[index].message = document.getElementById(`festival-message-${id}`).value.trim();
            items[index].startDate = document.getElementById(`festival-start-${id}`).value;
            items[index].endDate = document.getElementById(`festival-end-${id}`).value;

            const result = await apiPost('update_festivals', { items: items });
            
            if (result.success) {
                appData.festivals = items;
                cancelEditFestival(id);
                renderFestivalList();
                renderFestival();
                showSaveIndicator();
            } else {
                alert('保存失败: ' + (result.message || '未知错误'));
            }
        }

        function cancelEditFestival(id) {
            const form = document.getElementById(`edit-festival-${id}`);
            if (form) form.remove();
        }

        async function deleteFestivalItem(id) {
            if (!confirm('确定要删除这个节日吗？')) return;

            let items = appData.festivals || [];
            items = items.filter(i => i.id != id);

            const result = await apiPost('update_festivals', { items: items });
            
            if (result.success) {
                appData.festivals = items;
                renderFestivalList();
                renderFestival();
                showSaveIndicator();
            } else {
                alert('删除失败: ' + (result.message || '未知错误'));
            }
        }

        function loadSettings() {
            const interval = parseInt(appData.settings?.carousel_interval) || appData.carouselInterval || 3;
            document.getElementById('carouselInterval').value = interval;
            const scheduledInterval = parseInt(appData.settings?.scheduled_carousel_interval) || appData.scheduledCarouselInterval || 10;
            document.getElementById('scheduledCarouselInterval').value = scheduledInterval;
            
            const weatherEnabled = appData.settings?.weather_enabled === '1' || appData.settings?.weather_enabled === true || appData.weatherEnabled;
            document.getElementById('weatherEnabled').checked = weatherEnabled;
            const weatherCity = appData.settings?.weather_city || appData.weatherCity || '北京';
            document.getElementById('weatherCity').value = weatherCity;
            const weatherApiKey = appData.settings?.weather_api_key || appData.weatherApiKey || '';
            document.getElementById('weatherApiKey').value = weatherApiKey;
        }

        async function saveSettings() {
            const interval = parseInt(document.getElementById('carouselInterval').value) || 3;
            if (interval < 1 || interval > 60) {
                alert('背景轮播间隔应在1-60秒之间');
                return;
            }

            const scheduledInterval = parseInt(document.getElementById('scheduledCarouselInterval').value) || 10;
            if (scheduledInterval < 3 || scheduledInterval > 120) {
                alert('时段文案轮播间隔应在3-120秒之间');
                return;
            }

            const weatherEnabled = document.getElementById('weatherEnabled').checked ? '1' : '0';
            const weatherCity = document.getElementById('weatherCity').value.trim() || '北京';
            const weatherApiKey = document.getElementById('weatherApiKey').value.trim();
            const defaultGreeting = document.getElementById('defaultGreeting').value.trim();

            const data = {
                carousel_interval: interval,
                scheduled_carousel_interval: scheduledInterval,
                weather_enabled: weatherEnabled,
                weather_city: weatherCity,
                weather_api_key: weatherApiKey,
                default_greeting: defaultGreeting
            };

            const result = await apiPost('update_settings', data);
            
            if (result.success) {
                appData.settings.carousel_interval = interval;
                appData.settings.scheduled_carousel_interval = scheduledInterval;
                appData.settings.weather_enabled = weatherEnabled;
                appData.settings.weather_city = weatherCity;
                appData.settings.weather_api_key = weatherApiKey;
                appData.settings.default_greeting = defaultGreeting;
                
                startCarousel();
                scheduledCarousel.currentIndex = 0;
                startScheduledCarousel();
                renderFestival();
                
                weatherCache = null;
                weatherCacheTime = 0;
                renderWeather();
                
                showSaveIndicator();
            } else {
                alert('保存失败: ' + (result.message || '未知错误'));
            }
        }

        function switchTab(tabName) {
            document.querySelectorAll('.modal-tab').forEach(tab => {
                tab.classList.remove('active');
                if (tab.dataset.tab === tabName) {
                    tab.classList.add('active');
                }
            });

            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(`tab-${tabName}`).classList.add('active');
            
            if (tabName === 'users') {
                loadUsers();
            }
        }

        async function openAdminModal() {
            const loginResult = await apiGet('check_login');
            
            if (!loginResult.loggedIn) {
                if (confirm('请先登录后再使用后台管理功能。是否跳转到登录页面？')) {
                    window.location.href = 'login.php';
                }
                return;
            }
            
            document.getElementById('adminModal').classList.add('active');
            renderCarouselList();
            renderFestivalList();
            renderExternalList();
            renderIpList();
            loadAppearanceInputs();
            loadDefaultGreeting();
            loadSettings();
        }

        function closeAdminModal() {
            document.getElementById('adminModal').classList.remove('active');
        }

        function setupBgImageUpload() {
            const input = document.getElementById('bgImageInput');
            if (!input) return;
            
            input.addEventListener('change', async function(e) {
                const file = e.target.files[0];
                if (!file) return;
                
                if (!file.type.startsWith('image/')) {
                    alert('请选择图片文件');
                    return;
                }
                
                const maxSize = 5 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('图片大小不能超过5MB，请压缩后再上传');
                    return;
                }
                
                const formData = new FormData();
                formData.append('file', file);
                
                const result = await apiPost('upload_image', formData, false);
                
                if (result.success) {
                    const updateResult = await apiPost('update_appearance', { background_image: result.url });
                    
                    if (updateResult.success) {
                        appData.settings.background_image = result.url;
                        
                        const previewContainer = document.getElementById('bgImagePreviewContainer');
                        const previewImg = document.getElementById('bgImagePreview');
                        previewContainer.style.display = 'block';
                        previewImg.src = result.url;
                        
                        renderBackgroundImage();
                        showSaveIndicator();
                    } else {
                        alert('保存失败: ' + (updateResult.message || '未知错误'));
                    }
                } else {
                    alert('上传失败: ' + (result.message || '未知错误'));
                }
            });
        }

        function setupLeftLogoUpload() {
            const input = document.getElementById('leftLogoInput');
            if (!input) return;
            
            input.addEventListener('change', async function(e) {
                const file = e.target.files[0];
                if (!file) return;
                
                if (!file.type.startsWith('image/')) {
                    alert('请选择图片文件');
                    return;
                }
                
                const maxSize = 2 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('LOGO图片大小不能超过2MB，请压缩后再上传');
                    return;
                }
                
                const formData = new FormData();
                formData.append('file', file);
                
                const result = await apiPost('upload_image', formData, false);
                
                if (result.success) {
                    const updateResult = await apiPost('update_appearance', { left_logo: result.url });
                    
                    if (updateResult.success) {
                        appData.settings.left_logo = result.url;
                        
                        const previewContainer = document.getElementById('leftLogoPreviewContainer');
                        const previewImg = document.getElementById('leftLogoPreview');
                        previewContainer.style.display = 'block';
                        previewImg.src = result.url;
                        
                        renderHeaderLeft();
                        showSaveIndicator();
                    } else {
                        alert('保存失败: ' + (updateResult.message || '未知错误'));
                    }
                } else {
                    alert('上传失败: ' + (result.message || '未知错误'));
                }
            });
        }

        async function loadUsers() {
            const result = await apiGet('get_users');
            
            if (result.success && result.users) {
                const container = document.getElementById('usersList');
                const currentUserId = <?php echo $_SESSION['user_id'] ?? 0; ?>;
                
                if (result.users.length === 0) {
                    container.innerHTML = '<div class="empty-state">暂无管理员</div>';
                    return;
                }
                
                container.innerHTML = result.users.map(user => `
                    <div class="list-item">
                        <div class="list-item-content">
                            <div class="list-item-title">${escapeHtml(user.username)} ${user.id == currentUserId ? '(当前用户)' : ''}</div>
                            <div class="list-item-subtitle">角色: ${escapeHtml(user.role)} | 创建时间: ${escapeHtml(user.created_at)}</div>
                        </div>
                        <div class="list-item-actions">
                            ${user.id != currentUserId ? `<button class="btn btn-danger btn-sm" onclick="deleteUser('${user.id}')">删除</button>` : ''}
                        </div>
                    </div>
                `).join('');
            }
        }

        function showAddUserForm() {
            const container = document.getElementById('usersList');
            const editForm = document.createElement('div');
            editForm.className = 'edit-form';
            editForm.id = 'add-user-form';
            editForm.innerHTML = `
                <div class="edit-form-header">
                    <span class="edit-form-title">添加新管理员</span>
                </div>
                <div class="form-group">
                    <label class="form-label">用户名</label>
                    <input type="text" class="form-input" id="newUsername" placeholder="请输入用户名">
                </div>
                <div class="form-group">
                    <label class="form-label">密码</label>
                    <input type="password" class="form-input" id="newPassword" placeholder="请输入密码（至少6位）">
                </div>
                <div class="edit-form-actions">
                    <button class="btn btn-primary" onclick="addUser()">添加</button>
                    <button class="btn btn-ghost" onclick="cancelAddUser()">取消</button>
                </div>
            `;
            container.prepend(editForm);
        }

        async function addUser() {
            const username = document.getElementById('newUsername').value.trim();
            const password = document.getElementById('newPassword').value;
            
            if (!username || !password) {
                alert('请输入用户名和密码');
                return;
            }
            
            if (password.length < 6) {
                alert('密码长度至少6位');
                return;
            }
            
            const result = await apiPost('add_user', { username, password, role: 'admin' });
            
            if (result.success) {
                cancelAddUser();
                loadUsers();
                showSaveIndicator();
            } else {
                alert('添加失败: ' + (result.message || '未知错误'));
            }
        }

        function cancelAddUser() {
            const form = document.getElementById('add-user-form');
            if (form) form.remove();
        }

        async function deleteUser(id) {
            if (!confirm('确定要删除这个管理员吗？')) return;
            
            const result = await apiPost('delete_user', { id: parseInt(id) });
            
            if (result.success) {
                loadUsers();
                showSaveIndicator();
            } else {
                alert('删除失败: ' + (result.message || '未知错误'));
            }
        }

        async function changePassword() {
            const oldPassword = document.getElementById('changeOldPassword').value;
            const newPassword = document.getElementById('changeNewPassword').value;
            
            if (!oldPassword || !newPassword) {
                alert('请输入当前密码和新密码');
                return;
            }
            
            if (newPassword.length < 6) {
                alert('新密码长度至少6位');
                return;
            }
            
            const result = await apiPost('change_password', { oldPassword, newPassword });
            
            if (result.success) {
                document.getElementById('changeOldPassword').value = '';
                document.getElementById('changeNewPassword').value = '';
                showSaveIndicator();
            } else {
                alert('修改失败: ' + (result.message || '未知错误'));
            }
        }

        async function initializeApp() {
            document.getElementById('loading').style.display = 'none';
            document.body.classList.remove('loading');

            const configResult = await apiGet('get_config');
            
            if (configResult.success && configResult.data) {
                const data = configResult.data;
                appData.carousel = data.carousel || [];
                appData.scheduledContent = data.scheduledContent || [];
                appData.festivals = data.festivals || [];
                appData.externalUrls = data.externalUrls || [];
                appData.ipWhitelist = data.ipWhitelist || [];
                appData.settings = data.settings || {};
            }

            document.getElementById('mainContainer').style.display = 'block';
            document.getElementById('currentIp').textContent = '<?php echo htmlspecialchars($clientIp); ?>';
            
            requestAnimationFrame(() => {
                initMainApp();
            });
        }

        function initMainApp() {
            updateDateTime();
            setInterval(updateDateTime, 1000);
            
            startScheduledCarousel();
            if (scheduledCheckTimer) clearInterval(scheduledCheckTimer);
            scheduledCheckTimer = setInterval(restartScheduledCarousel, 30000);
            
            if (carouselCheckTimer) clearInterval(carouselCheckTimer);
            carouselCheckTimer = setInterval(restartCarousel, 30000);
            
            renderWeather();
            setInterval(renderWeather, 30 * 60 * 1000);
            
            renderHeaderLeft();
            renderBackgroundImage();
            renderCarousel();
            renderFestival();
            renderExternalUrls();
            
            requestIdleCallback(() => {
                renderIpList();
                renderCarouselList();
                renderScheduledList();
                renderFestivalList();
                renderExternalList();
                loadSettings();
                loadAppearanceInputs();
                loadDefaultGreeting();
            });
            
            startCarousel();
        }

        document.addEventListener('DOMContentLoaded', function() {
            initializeApp();
            setTimeout(setupBgImageUpload, 100);
            setTimeout(setupLeftLogoUpload, 100);
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAdminModal();
            }
        });
    </script>
</body>
</html>
