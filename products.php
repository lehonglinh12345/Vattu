<?php
$page_title = "Danh Mục Sản Phẩm";
$page_desc = "Danh sách sản phẩm hóa chất nhập khẩu, phân bón gốc lá, chế phẩm sinh học vi sinh do Ngọc Ánh Dương phân phối tại Cần Thơ.";
$active_page = 'products';
include 'includes/head.php';
include 'includes/header.php';
require_once 'includes/db.php';

// Get Filters from URL
$selected_category = isset($_GET['category']) ? trim($_GET['category']) : 'all';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Load product categories for the sidebar
$categories = [];
$categoryResult = db_query('SELECT id, slug, name FROM categories WHERE type = ? ORDER BY name ASC', 's', ['product']);
if ($categoryResult instanceof mysqli_result) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Load category counts
$category_counts = ['all' => 0];
$categoryCountResult = db_query('SELECT c.slug, COUNT(p.id) AS count FROM categories c LEFT JOIN products p ON p.category_id = c.id WHERE c.type = ? GROUP BY c.id', 's', ['product']);
if ($categoryCountResult instanceof mysqli_result) {
    while ($row = $categoryCountResult->fetch_assoc()) {
        $category_counts[$row['slug']] = (int)$row['count'];
        $category_counts['all'] += (int)$row['count'];
    }
}

// Build product query
$whereSql = '';
$params = [];
$types = '';
if ($selected_category !== 'all') {
    $whereSql .= ' AND c.slug = ?';
    $params[] = $selected_category;
    $types .= 's';
}
if ($search_query !== '') {
    $whereSql .= ' AND (p.name LIKE ? OR p.description LIKE ?)';
    $searchLike = '%' . $search_query . '%';
    $params[] = $searchLike;
    $params[] = $searchLike;
    $types .= 'ss';
}

$sql = 'SELECT p.*, c.slug AS category_slug, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE 1' . $whereSql . ' ORDER BY p.name ASC';
$productResult = db_query($sql, $types === '' ? null : $types, $params);
$filtered_products = [];
if ($productResult instanceof mysqli_result) {
    while ($row = $productResult->fetch_assoc()) {
        $filtered_products[] = $row;
    }
}

?>

<!-- Page Header Banner -->
<section class="about-hero" style="background: linear-gradient(rgba(18, 24, 32, 0.75), rgba(18, 24, 32, 0.8)), url('images/hero-bg.jpg') center/cover;">
    <div class="container">
        <h1>Sản Phẩm</h1>
        <div class="breadcrumbs">
            <a href="index.php">Trang chủ</a>
            <span>/</span>
            <span>Sản phẩm</span>
        </div>
    </div>
</section>

<!-- Catalog Main Section -->
<section class="section">
    <div class="container catalog-layout">
        
        <!-- Sidebar Filters -->
        <aside class="filter-sidebar">
            <div class="filter-widget">
                <h3>Tìm Kiếm Nhanh</h3>
                <form action="products.php" method="GET" style="position: relative;">
                    <?php if ($selected_category !== 'all'): ?>
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($selected_category); ?>">
                    <?php endif; ?>
                    <input type="text" name="search" placeholder="Nhập tên sản phẩm..." value="<?php echo htmlspecialchars($search_query); ?>" style="width: 100%; padding: 0.75rem 2.5rem 0.75rem 1rem; border: 1px solid var(--color-border); border-radius: 8px; font-family: var(--font-primary);">
                    <button type="submit" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--color-dark-muted); cursor: pointer;"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>
            
            <div class="filter-widget">
                <h3>Nhóm Sản Phẩm</h3>
                <ul class="category-list">
                    <li>
                        <a href="products.php?category=all<?php echo !empty($search_query) ? '&search='.urlencode($search_query) : ''; ?>" class="<?php echo $selected_category == 'all' ? 'active' : ''; ?>">
                            <span>Tất cả sản phẩm</span>
                            <span class="category-count"><?php echo htmlspecialchars($category_counts['all'] ?? 0); ?></span>
                        </a>
                    </li>
                    <?php foreach ($categories as $category): ?>
                        <li>
                            <a href="products.php?category=<?php echo urlencode($category['slug']); ?><?php echo !empty($search_query) ? '&search='.urlencode($search_query) : ''; ?>" class="<?php echo $selected_category == $category['slug'] ? 'active' : ''; ?>">
                                <span><?php echo htmlspecialchars($category['name']); ?></span>
                                <span class="category-count"><?php echo htmlspecialchars($category_counts[$category['slug']] ?? 0); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="filter-widget" style="background-color: var(--color-light); padding: 1.5rem; border-radius: 12px; border: 1px dashed var(--color-primary);">
                <h4 style="color: var(--color-primary); margin-bottom: 0.5rem;"><i class="fa-solid fa-phone"></i> Hỗ Trợ Đặt Hàng</h4>
                <p style="font-size: 0.85rem; color: var(--color-dark-muted); margin-bottom: 1rem;">Liên hệ trực tiếp để nhận bảng báo giá sỉ đại lý chiết khấu cao tốt nhất.</p>
                <a href="tel:0976828171" style="font-weight: 700; color: var(--color-secondary); font-size: 1.1rem; display: block; text-align: center; border: 1px solid var(--color-secondary); padding: 0.5rem; border-radius: 6px; background-color: var(--color-white);">0976.828.171</a>
            </div>
        </aside>
        
        <!-- Main Catalog Results -->
        <main class="catalog-results">
            <!-- Filter Header Bar -->
            <div class="catalog-header">
                <div class="results-count">
                    Hiển thị <span><?php echo count($filtered_products); ?></span> kết quả 
                    <?php if (!empty($search_query)): ?>
                        cho từ khóa "<strong><?php echo htmlspecialchars($search_query); ?></strong>"
                    <?php endif; ?>
                </div>
                
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <span style="font-size: 0.9rem; color: var(--color-dark-muted);">Sắp xếp:</span>
                    <select class="sort-select">
                        <option value="featured">Sản phẩm nổi bật</option>
                        <option value="name_asc">Tên A-Z</option>
                        <option value="name_desc">Tên Z-A</option>
                    </select>
                </div>
            </div>
            
            <!-- Products Catalog Grid -->
            <?php if (count($filtered_products) > 0): ?>
                <div class="product-grid" style="grid-template-columns: repeat(3, 1fr);">
                    <?php foreach ($filtered_products as $prod): ?>
                        <div class="product-card">
                            <div class="prod-img-wrapper">
                                <img src="<?php echo htmlspecialchars($prod['image']); ?>" alt="<?php echo htmlspecialchars($prod['name']); ?>" class="prod-img">
                                <span class="prod-badge <?php echo htmlspecialchars($prod['badge_class']); ?>"><?php echo htmlspecialchars($prod['badge']); ?></span>
                            </div>
                            <div class="prod-body">
                                <span class="prod-cat"><?php echo htmlspecialchars($prod['category_name']); ?></span>
                                <h3 class="prod-title"><a href="product-detail.php?id=<?php echo htmlspecialchars($prod['product_key']); ?>"><?php echo htmlspecialchars($prod['name']); ?></a></h3>
                                <p class="prod-origin">Xuất xứ: <strong><?php echo htmlspecialchars($prod['origin']); ?></strong></p>
                                <div class="prod-footer">
                                    <span class="prod-price"><?php echo htmlspecialchars($prod['price']); ?></span>
                                    <a href="product-detail.php?id=<?php echo htmlspecialchars($prod['product_key']); ?>" class="btn-detail" title="Xem chi tiết"><i class="fa-solid fa-eye"></i></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- No Results State -->
                <div style="text-align: center; padding: 5rem 2rem; background-color: var(--color-white); border-radius: 16px; border: 1px solid var(--color-border); box-shadow: var(--shadow-sm);">
                    <div style="font-size: 3.5rem; color: var(--color-dark-muted); margin-bottom: 1.5rem;"><i class="fa-solid fa-folder-open"></i></div>
                    <h3>Không tìm thấy sản phẩm phù hợp</h3>
                    <p style="color: var(--color-dark-muted); margin-bottom: 2rem;">Vui lòng thử lại với từ khóa tìm kiếm hoặc danh mục khác.</p>
                    <a href="products.php" class="btn btn-primary">Xóa bộ lọc</a>
                </div>
            <?php endif; ?>
        </main>
        
    </div>
</section>

<?php
include 'includes/footer.php';
?>
