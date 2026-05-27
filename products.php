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

<!-- Nút kích hoạt bộ lọc nhanh trên Mobile -->
<div class="mobile-filter-trigger container">
    <button id="toggleFilterBtn"><i class="fa-solid fa-filter"></i> Bộ lọc & Tìm kiếm</button>
</div>

<!-- Catalog Main Section -->
<section class="section catalog-section">
    <div class="container catalog-layout">
        
        <!-- Sidebar Filters -->
        <aside class="filter-sidebar" id="filterSidebar">
            <div class="filter-widget">
                <h3>Tìm Kiếm Nhanh</h3>
                <form action="products.php" method="GET" class="search-form-wrapper">
                    <?php if ($selected_category !== 'all'): ?>
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($selected_category); ?>">
                    <?php endif; ?>
                    <input type="text" name="search" class="search-input" placeholder="Nhập tên sản phẩm..." value="<?php echo htmlspecialchars($search_query); ?>">
                    <button type="submit" class="search-submit-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
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
            
            <div class="filter-widget widget-support">
                <h4><i class="fa-solid fa-phone"></i> Hỗ Trợ Đặt Hàng</h4>
                <p>Liên hệ trực tiếp để nhận bảng báo giá sỉ đại lý chiết khấu cao tốt nhất.</p>
                <a href="tel:0976828171" class="support-phone-btn">0976.828.171</a>
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
                
                <div class="sort-wrapper">
                    <span class="sort-label">Sắp xếp:</span>
                    <select class="sort-select">
                        <option value="featured">Sản phẩm nổi bật</option>
                        <option value="name_asc">Tên A-Z</option>
                        <option value="name_desc">Tên Z-A</option>
                    </select>
                </div>
            </div>
            
            <!-- Products Catalog Grid (Đã bỏ inline style cứng để CSS điều khiển) -->
            <?php if (count($filtered_products) > 0): ?>
                <div class="product-grid">
                    <?php foreach ($filtered_products as $prod): ?>
                        <div class="product-card">
                            <div class="prod-img-wrapper">
                                <img src="<?php echo htmlspecialchars($prod['image']); ?>" alt="<?php echo htmlspecialchars($prod['name']); ?>" class="prod-img">
                                <?php if(!empty($prod['badge'])): ?>
                                    <span class="prod-badge <?php echo htmlspecialchars($prod['badge_class']); ?>"><?php echo htmlspecialchars($prod['badge']); ?></span>
                                <?php endif; ?>
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
                <div class="no-results-box">
                    <div class="no-results-icon"><i class="fa-solid fa-folder-open"></i></div>
                    <h3>Không tìm thấy sản phẩm phù hợp</h3>
                    <p>Vui lòng thử lại với từ khóa tìm kiếm hoặc danh mục khác.</p>
                    <a href="products.php" class="btn btn-primary">Xóa bộ lọc</a>
                </div>
            <?php endif; ?>
        </main>
        
    </div>
</section>

<!-- Script nhỏ xử lý Đóng/Mở bộ lọc mượt mà trên Điện thoại -->

<script>
document.addEventListener("DOMContentLoaded", function() {
    const toggleBtn = document.getElementById("toggleFilterBtn");
    const sidebar = document.getElementById("filterSidebar");
    
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener("click", function(e) {
            e.preventDefault(); // Ngăn chặn mọi hành vi cuộn mặc định
            sidebar.classList.toggle("show-mobile");
            
            // Thay đổi nội dung nút để người dùng biết trạng thái
            if (sidebar.classList.contains("show-mobile")) {
                toggleBtn.innerHTML = '<i class="fa-solid fa-xmark"></i> Đóng bộ lọc';
                toggleBtn.style.backgroundColor = '#e74c3c'; // Đổi sang màu đỏ khi muốn đóng
            } else {
                toggleBtn.innerHTML = '<i class="fa-solid fa-filter"></i> Bộ lọc & Tìm kiếm';
                toggleBtn.style.backgroundColor = ''; // Về màu chủ đạo mặc định
            }
        });
    }
});

</script>

<?php
include 'includes/footer.php';
?>