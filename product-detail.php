<?php
$product_key = trim($_GET['id'] ?? '');
require_once 'includes/db.php';

$product = null;
if ($product_key !== '') {
    $productResult = db_query(
        'SELECT p.*, c.slug AS category_slug, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.product_key = ?',
        's',
        [$product_key]
    );
    if ($productResult instanceof mysqli_result) {
        $product = $productResult->fetch_assoc();
    }
}

$page_title = $product ? $product['name'] : 'Sản phẩm không tìm thấy';
$page_desc = $product ? ($product['description'] ?: 'Chi tiết sản phẩm') : 'Sản phẩm bạn yêu cầu hiện không tồn tại hoặc đã được cập nhật.';
$active_page = 'products';
include 'includes/head.php';
include 'includes/header.php';

if (!$product) {
    ?>
    <section class="about-hero" style="background: linear-gradient(rgba(18, 24, 32, 0.75), rgba(18, 24, 32, 0.8)), url('images/hero-bg.jpg') center/cover;">
        <div class="container">
            <h1>Sản phẩm không tìm thấy</h1>
            <div class="breadcrumbs">
                <a href="index.php">Trang chủ</a>
                <span>/</span>
                <a href="products.php">Sản phẩm</a>
                <span>/</span>
                <span>Không tìm thấy</span>
            </div>
        </div>
    </section>
    <section class="section">
        <div class="container" style="text-align:center; padding: 4rem 0;">
            <h2>Rất tiếc, sản phẩm bạn tìm kiếm không tồn tại.</h2>
            <p style="color: var(--color-dark-muted); margin: 1rem 0 2rem;">Vui lòng quay lại trang danh mục hoặc thử lại bằng một sản phẩm khác.</p>
            <a href="products.php" class="btn btn-primary">Quay lại danh sách sản phẩm</a>
        </div>
    </section>
    <?php
    include 'includes/footer.php';
    return;
}

$related_products = [];
$relatedResult = db_query(
    'SELECT p.product_key, p.name, p.price, p.image, p.badge, p.badge_class, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? AND p.product_key <> ? ORDER BY p.name ASC LIMIT 4',
    'is',
    [$product['category_id'], $product['product_key']]
);
if ($relatedResult instanceof mysqli_result) {
    while ($row = $relatedResult->fetch_assoc()) {
        $related_products[] = $row;
    }
}

$content_detail = '<p>' . htmlspecialchars($product['description']) . '</p>';
$content_usage = '<p>Để nhận hướng dẫn kỹ thuật sử dụng và liều lượng phù hợp, vui lòng liên hệ Hotline hoặc gửi yêu cầu báo giá.</p>';
$content_technical = '<ul><li>Loại sản phẩm: ' . htmlspecialchars($product['badge'] ?: 'Nông nghiệp') . '</li><li>Xuất xứ: ' . htmlspecialchars($product['origin']) . '</li><li>Giá: ' . htmlspecialchars($product['price']) . '</li></ul><p>Thông tin kỹ thuật chi tiết sẽ được gửi theo yêu cầu khách hàng.</p>';
?>

<!-- Page Header Banner -->
<section class="about-hero" style="background: linear-gradient(rgba(18, 24, 32, 0.75), rgba(18, 24, 32, 0.8)), url('images/hero-bg.jpg') center/cover;">
    <div class="container">
        <h1>Chi Tiết Sản Phẩm</h1>
        <div class="breadcrumbs">
            <a href="index.php">Trang chủ</a>
            <span>/</span>
            <a href="products.php">Sản phẩm</a>
            <span>/</span>
            <span><?php echo htmlspecialchars($product['name']); ?></span>
        </div>
    </div>
</section>

<!-- Product Details Section -->
<section class="section">
    <div class="container">
        <div class="prod-detail-grid">
            <div class="detail-img-box">
                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php if (!empty($product['badge'])): ?>
                    <span class="prod-badge <?php echo htmlspecialchars($product['badge_class']); ?>" style="position:absolute; top: 1rem; left: 1rem; z-index: 2;"><?php echo htmlspecialchars($product['badge']); ?></span>
                <?php endif; ?>
            </div>
            <div class="prod-detail-info">
                <span class="detail-meta-cat"><?php echo htmlspecialchars($product['category_name']); ?></span>
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="prod-status-tag">
                    Xuất xứ: <strong><?php echo htmlspecialchars($product['origin']); ?></strong>
                </p>
                <div class="detail-price"><?php echo htmlspecialchars($product['price']); ?></div>
                <div class="detail-description" style="margin: 2rem 0; color: var(--color-dark-muted); line-height: 1.8;">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </div>
                <div class="detail-actions">
                    <a href="tel:0976828171" class="btn btn-primary" style="padding: 0.85rem 2.25rem;"><i class="fa-solid fa-phone-volume"></i> Gọi Ngay: 0976.828.171</a>
                    <a href="contact.php?subject=Yeu%20cau%20bao%20gia%20<?php echo urlencode($product['name']); ?>" class="btn btn-outline"><i class="fa-solid fa-envelope"></i> Gửi Yêu Cầu Báo Giá</a>
                </div>
            </div>
        </div>
        <div style="margin-top: 4rem;">
            <div class="detail-tabs-nav">
                <button class="tab-btn active" data-tab="tab-desc">Mô tả sản phẩm</button>
                <button class="tab-btn" data-tab="tab-usage">Hướng dẫn</button>
                <button class="tab-btn" data-tab="tab-tech">Thông số kỹ thuật</button>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="tab-desc">
                    <?php echo $content_detail; ?>
                </div>
                <div class="tab-pane" id="tab-usage">
                    <?php echo $content_usage; ?>
                </div>
                <div class="tab-pane" id="tab-tech">
                    <?php echo $content_technical; ?>
                </div>
            </div>
        </div>
        <?php if (!empty($related_products)): ?>
            <div style="margin-top: 5rem;">
                <div class="text-center" style="margin-bottom: 3rem;">
                    <h2 style="font-size: 1.75rem;">Sản Phẩm Cùng Loại Khác</h2>
                    <div style="width: 50px; height: 3px; background-color: var(--color-primary); margin: 0.5rem auto 0 auto; border-radius: 2px;"></div>
                </div>
                <div class="product-grid" style="grid-template-columns: repeat(4, 1fr);">
                    <?php foreach ($related_products as $p_data): ?>
                        <div class="product-card">
                            <div class="prod-img-wrapper">
                                <img src="<?php echo htmlspecialchars($p_data['image']); ?>" alt="<?php echo htmlspecialchars($p_data['name']); ?>" class="prod-img">
                                <?php if (!empty($p_data['badge'])): ?>
                                    <span class="prod-badge <?php echo htmlspecialchars($p_data['badge_class']); ?>"><?php echo htmlspecialchars($p_data['badge']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="prod-body">
                                <span class="prod-cat"><?php echo htmlspecialchars($p_data['category_name']); ?></span>
                                <h3 class="prod-title"><a href="product-detail.php?id=<?php echo htmlspecialchars($p_data['product_key']); ?>"><?php echo htmlspecialchars($p_data['name']); ?></a></h3>
                                <div class="prod-footer">
                                    <span class="prod-price"><?php echo htmlspecialchars($p_data['price']); ?></span>
                                    <a href="product-detail.php?id=<?php echo htmlspecialchars($p_data['product_key']); ?>" class="btn-detail" title="Xem chi tiết"><i class="fa-solid fa-eye"></i></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
include 'includes/footer.php';
?>
