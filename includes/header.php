<?php
require_once __DIR__ . '/auth.php';

$nav_items = [
    ['href' => 'index.php',             'label' => 'Trang chủ',  'key' => 'home'],
    ['href' => 'about.php',             'label' => 'Giới thiệu', 'key' => 'about'],
    ['href' => 'products.php',          'label' => 'Sản phẩm',   'key' => 'products'],
    ['href' => 'news.php?section=tech', 'label' => 'Kỹ thuật',   'key' => 'tech'],
    ['href' => 'news.php?section=news', 'label' => 'Tin tức',    'key' => 'news'],
    ['href' => 'contact.php',           'label' => 'Liên hệ',    'key' => 'contact'],
];

// Reusable logo — dùng chung desktop & mobile
function render_logo(string $href = 'index.php', bool $show_sub = true): string {
    $sub = $show_sub ? '<span class="brand-sub">IMPORT CHEMICAL</span>' : '';
    return <<<HTML
    <a href="{$href}" class="logo">
        <div class="logo-icon">
            <img src="images/logo.jpg" alt="Ngọc Ánh Dương" class="logo-bg">
            <svg class="logo-pill" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" fill="none">
                <rect x="2" y="15" width="44" height="18" rx="9" fill="url(#pill-hdr)"/>
                <rect x="2" y="15" width="22" height="18" rx="9" fill="rgba(255,255,255,0.14)"/>
                <line x1="24" y1="15" x2="24" y2="33" stroke="rgba(255,255,255,0.55)" stroke-width="1.2"/>
                <path d="M28 24 C30 19.5,38 19.5,38 24 C38 28.5,30 28.5,28 24Z"
                      fill="rgba(255,255,255,0.26)" stroke="rgba(255,255,255,0.6)" stroke-width="0.8"/>
                <line x1="28" y1="24" x2="38" y2="24" stroke="rgba(255,255,255,0.4)" stroke-width="0.7"/>
                <rect x="5" y="17" width="13" height="3.5" rx="1.75" fill="rgba(255,255,255,0.16)"/>
                <defs>
                    <linearGradient id="pill-hdr" x1="2" y1="15" x2="46" y2="33" gradientUnits="userSpaceOnUse">
                        <stop offset="0%"   stop-color="#0b6623"/>
                        <stop offset="100%" stop-color="#38b249"/>
                    </linearGradient>
                </defs>
            </svg>
        </div>
        <div class="logo-text">
            <span class="brand-name">NGỌC ÁNH DƯƠNG</span>
            {$sub}
        </div>
    </a>
    HTML;
}
?>

<!-- TOP BAR -->
<div class="top-bar">
    <div class="container top-bar-container">
        <div class="top-contacts">
            <a href="tel:0976828171"><i class="fa-solid fa-phone"></i> Hotline: 0976.828.171</a>
            <a href="mailto:ngocanhduongchemical@gmail.com"><i class="fa-solid fa-envelope"></i> ngocanhduongchemical@gmail.com</a>
        </div>
        <div class="top-meta">
            <span><i class="fa-solid fa-clock"></i> 7:30 – 17:00</span>
            <div class="lang-switch">
                <span class="active">VI</span> | <span>EN</span>
            </div>
        </div>
    </div>
</div>

<!-- MAIN HEADER -->
<header class="main-header" id="mainHeader">
    <div class="container header-container">

        <?php echo render_logo('index.php', true); ?>

        <nav class="nav-menu" aria-label="Điều hướng chính">
            <ul>
                <?php foreach ($nav_items as $item):
                    $active = isset($active_page) && $active_page === $item['key'];
                ?>
                <li>
                    <a href="<?= $item['href'] ?>"
                       class="<?= $active ? 'active' : '' ?>"
                       <?= $active ? 'aria-current="page"' : '' ?>>
                        <?= $item['label'] ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <style>
        /* User Dropdown Navigation Styles */
        .user-menu-wrapper {
            position: relative;
            display: inline-block;
        }
        .user-menu-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.25rem !important;
            border-radius: 50px;
            border: 2px solid var(--color-primary);
            background: transparent;
            color: var(--color-primary);
            cursor: pointer;
            font-family: var(--font-primary);
            font-weight: 600;
            font-size: 0.85rem;
            transition: var(--transition-normal);
        }
        .user-menu-btn:hover {
            background-color: rgba(11, 102, 37, 0.06);
            transform: translateY(-1px);
        }
        .user-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            background-color: var(--color-white);
            border: 1px solid var(--color-border);
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            min-width: 200px;
            padding: 0.5rem 0;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: var(--transition-normal);
            z-index: 999;
        }
        .user-dropdown.open {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.25rem;
            font-size: 0.88rem;
            color: var(--color-dark);
            font-weight: 500;
            transition: var(--transition-fast);
        }
        .dropdown-item:hover {
            background-color: var(--color-light);
            color: var(--color-primary);
        }
        .dropdown-item i {
            font-size: 1rem;
            color: var(--color-dark-muted);
        }
        .dropdown-item:hover i {
            color: var(--color-primary);
        }
        .dropdown-item.logout-link:hover {
            color: #ef4444 !important;
            background-color: #fef2f2;
        }
        .dropdown-item.logout-link:hover i {
            color: #ef4444 !important;
        }
        .user-name-text {
            max-width: 100px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
        }
        </style>

        <div class="header-actions">
            <div class="search-box">
                <form action="products.php" method="GET" role="search">
                    <input type="text" name="search" placeholder="Tìm sản phẩm…"
                           class="search-input" aria-label="Tìm kiếm sản phẩm">
                    <button type="submit" class="search-btn" aria-label="Tìm kiếm">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>
            </div>

            <?php if (auth_is_logged_in()): 
                $curr_user = auth_get_user();
            ?>
                <div class="user-menu-wrapper">
                    <button class="user-menu-btn" id="userMenuBtn" aria-label="Menu người dùng">
                        <i class="fa-solid fa-circle-user" style="font-size: 1.1rem;"></i>
                        <span class="user-name-text"><?= h($curr_user['full_name'] ?: $curr_user['username']) ?></span>
                        <i class="fa-solid fa-chevron-down" style="font-size: 0.7rem; opacity: 0.7;"></i>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <?php if ($curr_user['role'] === 'admin'): ?>
                            <a href="admin_dashboard.php" class="dropdown-item">
                                <i class="fa-solid fa-chart-line"></i> Trang Quản Trị
                            </a>
                        <?php endif; ?>
                        <a href="logout.php" class="dropdown-item logout-link">
                            <i class="fa-solid fa-right-from-bracket"></i> Đăng Xuất
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline" style="padding: 0.6rem 1.25rem; font-size: 0.85rem; border-radius: 50px;">
                    <i class="fa-solid fa-right-to-bracket"></i> Đăng Nhập
                </a>
            <?php endif; ?>

            <a href="contact.php" class="btn btn-primary btn-quote">
                <i class="fa-solid fa-tag"></i> Báo Giá
            </a>
            <button class="mobile-toggle" id="mobileMenuToggle"
                    aria-label="Mở menu" aria-expanded="false" aria-controls="mobileNav">
                <span></span><span></span><span></span>
            </button>
        </div>

    </div>
</header>

<!-- MOBILE DRAWER -->
<div class="mobile-nav" id="mobileNav" role="dialog"
     aria-label="Menu điều hướng" aria-hidden="true">

    <div class="mobile-nav-header">
        <?php echo render_logo('index.php', false); ?>
        <button class="mobile-close" id="mobileMenuClose" aria-label="Đóng menu">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <div class="mobile-search">
        <form action="products.php" method="GET" role="search">
            <input type="text" name="search" placeholder="Tìm sản phẩm…"
                   aria-label="Tìm kiếm sản phẩm">
            <button type="submit" aria-label="Tìm kiếm">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>
    </div>

    <ul>
        <?php foreach ($nav_items as $item):
            $active = isset($active_page) && $active_page === $item['key'];
        ?>
        <li>
            <a href="<?= $item['href'] ?>"
               class="<?= $active ? 'active' : '' ?>"
               <?= $active ? 'aria-current="page"' : '' ?>>
                <?= $item['label'] ?>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>

    <div class="mobile-contacts">
        <?php if (auth_is_logged_in()): 
            $curr_user = auth_get_user();
        ?>
            <div class="mobile-user-info" style="margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--color-border);">
                <div style="font-weight: 700; color: var(--color-secondary); margin-bottom: 0.25rem; font-size: 1.05rem;">
                    <i class="fa-solid fa-circle-user"></i> Xin chào, <?= h($curr_user['full_name'] ?: $curr_user['username']) ?>
                </div>
                <div style="font-size: 0.8rem; color: var(--color-dark-muted); text-transform: uppercase; letter-spacing: 0.5px;">
                    Vai trò: <strong><?= $curr_user['role'] === 'admin' ? 'Quản trị viên' : 'Khách hàng' ?></strong>
                </div>
            </div>
            <?php if ($curr_user['role'] === 'admin'): ?>
                <a href="admin_dashboard.php" class="btn btn-outline" style="margin-bottom:.5rem;justify-content:center;width:100%;border-radius:8px;padding:0.6rem;">
                    <i class="fa-solid fa-chart-line"></i> Trang Quản Trị
                </a>
            <?php endif; ?>
            <a href="logout.php" class="btn btn-outline" style="margin-bottom:.5rem;justify-content:center;width:100%;border-radius:8px;padding:0.6rem;border-color:#ef4444;color:#ef4444;">
                <i class="fa-solid fa-right-from-bracket"></i> Đăng Xuất
            </a>
        <?php else: ?>
            <a href="login.php" class="btn btn-outline" style="margin-bottom:.5rem;justify-content:center;width:100%;border-radius:8px;padding:0.6rem;">
                <i class="fa-solid fa-right-to-bracket"></i> Đăng Nhập
            </a>
            <a href="register.php" class="btn btn-outline" style="margin-bottom:.5rem;justify-content:center;width:100%;border-radius:8px;padding:0.6rem;">
                <i class="fa-solid fa-user-plus"></i> Đăng Ký
            </a>
        <?php endif; ?>

        <div style="margin-top: 1rem; border-top: 1px solid var(--color-border); padding-top: 1rem; display: flex; flex-direction: column; gap: 0.5rem;">
            <a href="tel:0976828171"><i class="fa-solid fa-phone" style="color: var(--color-primary);"></i> 0976.828.171</a>
            <a href="mailto:ngocanhduongchemical@gmail.com"><i class="fa-solid fa-envelope" style="color: var(--color-primary);"></i> ngocanhduongchemical@gmail.com</a>
        </div>
        <a href="contact.php" class="btn btn-primary" style="margin-top:.8rem;justify-content:center;border-radius:8px;padding:0.6rem;">
            <i class="fa-solid fa-tag"></i> Nhận Báo Giá
        </a>
    </div>
</div>

<div class="mobile-overlay" id="mobileOverlay" aria-hidden="true"></div>