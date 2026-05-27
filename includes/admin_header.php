<?php
require_once __DIR__ . '/auth.php';

// Enforce admin permission
auth_require_role('admin');

$current_admin = auth_get_user();
$active_tab = $active_admin_tab ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($page_title ?? 'Trang Quản Trị') ?> - Hóa Chất Ngọc Ánh Dương</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="admin-sidebar-header">
                <a href="admin_dashboard.php" class="admin-logo">
                    <i class="fa-solid fa-flask-vial"></i>
                    <span>ADMIN PANEL</span>
                </a>
            </div>
            <nav class="admin-nav">
                <ul class="admin-nav-list">
                    <li class="admin-nav-item <?= $active_tab === 'dashboard' ? 'active' : '' ?>">
                        <a href="admin_dashboard.php">
                            <i class="fa-solid fa-chart-pie"></i>
                            <span>Tổng Quan</span>
                        </a>
                    </li>
                    <li class="admin-nav-item <?= $active_tab === 'products' ? 'active' : '' ?>">
                        <a href="admin_products.php">
                            <i class="fa-solid fa-boxes-stacked"></i>
                            <span>Sản Phẩm</span>
                        </a>
                    </li>
                    <li class="admin-nav-item <?= $active_tab === 'categories' ? 'active' : '' ?>">
                        <a href="admin_categories.php">
                            <i class="fa-solid fa-tags"></i>
                            <span>Danh Mục</span>
                        </a>
                    </li>
                    <li class="admin-nav-item <?= $active_tab === 'messages' ? 'active' : '' ?>">
                        <a href="admin_messages.php">
                            <i class="fa-solid fa-envelope-open-text"></i>
                            <span>Tin Nhắn</span>
                        </a>
                    </li>
                    <li class="admin-nav-item <?= $active_tab === 'users' ? 'active' : '' ?>">
                        <a href="admin_users.php">
                            <i class="fa-solid fa-users-gear"></i>
                            <span>Người Dùng</span>
                        </a>
                    </li>
                    <li style="margin-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 1rem;" class="admin-nav-item">
                        <a href="index.php">
                            <i class="fa-solid fa-house"></i>
                            <span>Xem Trang Chủ</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="admin-sidebar-footer">
                &copy; 2026 Ngọc Ánh Dương
            </div>
        </aside>

        <!-- Main Panel -->
        <main class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <div class="admin-header-title">
                    <button class="mobile-sidebar-toggle" id="adminSidebarToggle" aria-label="Toggle Sidebar">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h2><?= h($page_title ?? 'Trang Quản Trị') ?></h2>
                </div>
                <div class="admin-header-user">
                    <div class="admin-user-profile">
                        <div class="admin-avatar">
                            <?= strtoupper(substr($current_admin['username'], 0, 1)) ?>
                        </div>
                        <div class="admin-user-details">
                            <span class="admin-user-name"><?= h($current_admin['full_name'] ?: $current_admin['username']) ?></span>
                            <span class="admin-user-role">Quản trị viên</span>
                        </div>
                    </div>
                    <button class="admin-logout-btn" onclick="location.href='logout.php'" title="Đăng xuất">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </div>
            </header>

            <!-- Content Area -->
            <div class="admin-content">
