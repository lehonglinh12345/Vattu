<?php
$page_title = "Bảng Điều Khiển Tổng Quan";
$active_admin_tab = "dashboard";
require_once __DIR__ . '/includes/admin_header.php';

// Fetch statistics
$prod_count = 0;
$cat_count = 0;
$news_count = 0;
$msg_count = 0;
$user_count = 0;

// Products count
$res = db_query("SELECT COUNT(*) as cnt FROM products");
if ($res) {
    $row = $res->fetch_assoc();
    $prod_count = $row['cnt'];
}

// Categories count
$res = db_query("SELECT COUNT(*) as cnt FROM categories");
if ($res) {
    $row = $res->fetch_assoc();
    $cat_count = $row['cnt'];
}

// News articles count
$res = db_query("SELECT COUNT(*) as cnt FROM news_articles");
if ($res) {
    $row = $res->fetch_assoc();
    $news_count = $row['cnt'];
}

// Unread/New messages count
$res = db_query("SELECT COUNT(*) as cnt FROM contact_messages WHERE status = 'new'");
if ($res) {
    $row = $res->fetch_assoc();
    $msg_count = $row['cnt'];
}

// Customers count
$res = db_query("SELECT COUNT(*) as cnt FROM users WHERE role = 'customer'");
if ($res) {
    $row = $res->fetch_assoc();
    $user_count = $row['cnt'];
}

// Fetch recent messages
$recent_messages = db_query("SELECT * FROM contact_messages ORDER BY id DESC LIMIT 5");
?>

<!-- Metrics Grid -->
<div class="admin-metrics-grid">
    <div class="admin-metric-card">
        <div class="admin-metric-info">
            <span class="admin-metric-label">Sản Phẩm</span>
            <span class="admin-metric-value"><?= $prod_count ?></span>
        </div>
        <div class="admin-metric-icon icon-primary">
            <i class="fa-solid fa-boxes-stacked"></i>
        </div>
    </div>
    
    <div class="admin-metric-card">
        <div class="admin-metric-info">
            <span class="admin-metric-label">Danh Mục</span>
            <span class="admin-metric-value"><?= $cat_count ?></span>
        </div>
        <div class="admin-metric-icon icon-secondary">
            <i class="fa-solid fa-tags"></i>
        </div>
    </div>

    <div class="admin-metric-card">
        <div class="admin-metric-info">
            <span class="admin-metric-label">Bài Viết Tin Tức</span>
            <span class="admin-metric-value"><?= $news_count ?></span>
        </div>
        <div class="admin-metric-icon icon-success">
            <i class="fa-solid fa-newspaper"></i>
        </div>
    </div>

    <div class="admin-metric-card">
        <div class="admin-metric-info">
            <span class="admin-metric-label">Tin Nhắn Mới</span>
            <span class="admin-metric-value"><?= $msg_count ?></span>
        </div>
        <div class="admin-metric-icon icon-danger">
            <i class="fa-solid fa-comment-dots"></i>
        </div>
    </div>

    <div class="admin-metric-card">
        <div class="admin-metric-info">
            <span class="admin-metric-label">Khách Hàng</span>
            <span class="admin-metric-value"><?= $user_count ?></span>
        </div>
        <div class="admin-metric-icon icon-warning">
            <i class="fa-solid fa-users"></i>
        </div>
    </div>
</div>

<!-- Recent Submissions Section -->
<div class="admin-card">
    <div class="admin-card-header">
        <h3><i class="fa-solid fa-bell"></i> Tin Nhắn Liên Hệ Gần Đây</h3>
        <a href="admin_messages.php" class="btn btn-outline btn-sm" style="border-radius: 8px;">Xem tất cả</a>
    </div>
    <div class="admin-card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Người gửi</th>
                        <th>Số điện thoại</th>
                        <th>Chủ đề</th>
                        <th>Ngày gửi</th>
                        <th>Trạng thái</th>
                        <th style="width: 100px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recent_messages && $recent_messages->num_rows > 0): ?>
                        <?php while ($msg = $recent_messages->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600;"><?= h($msg['name']) ?></div>
                                    <div style="font-size: 0.8rem; color: var(--color-admin-text-muted);"><?= h($msg['email']) ?></div>
                                </td>
                                <td><?= h($msg['phone']) ?></td>
                                <td><?= h($msg['subject'] ?: '(Không có tiêu đề)') ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?></td>
                                <td>
                                    <?php if ($msg['status'] === 'new'): ?>
                                        <span class="badge badge-new">Mới</span>
                                    <?php elseif ($msg['status'] === 'read'): ?>
                                        <span class="badge badge-read">Đã đọc</span>
                                    <?php else: ?>
                                        <span class="badge badge-closed">Đã xử lý</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        <a href="admin_messages.php?action=view&id=<?= $msg['id'] ?>" class="btn-icon-only btn-view" title="Xem chi tiết">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem; color: var(--color-admin-text-muted);">
                                Chưa có tin nhắn liên hệ nào được gửi.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/admin_footer.php';
?>
