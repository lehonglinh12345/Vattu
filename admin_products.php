<?php
$page_title = "Quản Lý Sản Phẩm";
$active_admin_tab = "products";
require_once __DIR__ . '/includes/admin_header.php';

$error = '';
$success = '';

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch Categories for dropdown
$categories_res = db_query("SELECT * FROM categories ORDER BY name ASC");
$categories = [];
if ($categories_res) {
    while ($cat = $categories_res->fetch_assoc()) {
        $categories[] = $cat;
    }
}

// --------------------------------------------------------------------------
// Delete Product
// --------------------------------------------------------------------------
if ($action === 'delete' && $id > 0) {
    if (!isset($_GET['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_GET['csrf_token'])) {
        $error = "Yêu cầu không hợp lệ (Lỗi CSRF token).";
    } else {
        // Find product first to delete image file if custom
        $check = db_query("SELECT image FROM products WHERE id = ?", "i", [$id]);
        if ($check && $check->num_rows > 0) {
            $product = $check->fetch_assoc();
            $delete = db_query("DELETE FROM products WHERE id = ?", "i", [$id]);
            if ($delete) {
                // Optionally delete physical image if it is an uploaded file
                if (!empty($product['image']) && file_exists(__DIR__ . '/' . $product['image']) && !in_array($product['image'], ['images/tang-luc-x3.jpg', 'images/nuoi-dong.jpg', 'images/bio-prep.jpg', 'images/chem-bag.jpg'])) {
                    @unlink(__DIR__ . '/' . $product['image']);
                }
                $success = "Xóa sản phẩm thành công!";
            } else {
                $error = "Không thể xóa sản phẩm. Có thể có ràng buộc khóa ngoại.";
            }
        } else {
            $error = "Sản phẩm không tồn tại.";
        }
    }
    $action = 'list';
}

// --------------------------------------------------------------------------
// Process Save (Add / Edit)
// --------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($action === 'add' || $action === 'edit')) {
    if (!auth_csrf_verify()) {
        $error = "Yêu cầu không hợp lệ (Lỗi CSRF token).";
    } else {
        $name = trim($_POST['name'] ?? '');
        $product_key = trim($_POST['product_key'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $price = trim($_POST['price'] ?? 'Liên hệ báo giá');
        $origin = trim($_POST['origin'] ?? '');
        $badge = trim($_POST['badge'] ?? '');
        $badge_class = trim($_POST['badge_class'] ?? '');
        $description = trim($_POST['description'] ?? '');

        // Auto-slugify product key if blank
        if (empty($product_key) && !empty($name)) {
            // Simple slug conversion
            $slug = strtolower($name);
            $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
            $slug = preg_replace('/-+/', '-', $slug);
            $product_key = trim($slug, '-');
        }

        if (empty($name) || $category_id <= 0) {
            $error = "Tên sản phẩm và Danh mục là thông tin bắt buộc.";
        } else {
            // Check uniqueness of product_key
            $checkKeySQL = "SELECT id FROM products WHERE product_key = ? AND id != ?";
            $checkKey = db_query($checkKeySQL, "si", [$product_key, $id]);
            if ($checkKey && $checkKey->num_rows > 0) {
                // Append random string to duplicate product key to avoid conflict
                $product_key .= '-' . rand(100, 999);
            }

            // Handle Image Upload
            $image_path = $_POST['existing_image'] ?? 'images/chem-bag.jpg';
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['product_image']['tmp_name'];
                $file_name = $_FILES['product_image']['name'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                
                $allowed_exts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                if (in_array($file_ext, $allowed_exts)) {
                    // Make unique name
                    $new_file_name = 'prod_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
                    $upload_dir = __DIR__ . '/images';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    if (move_uploaded_file($file_tmp, $upload_dir . '/' . $new_file_name)) {
                        $image_path = 'images/' . $new_file_name;
                        // Clean up old custom image
                        if ($action === 'edit' && !empty($_POST['existing_image']) && !in_array($_POST['existing_image'], ['images/tang-luc-x3.jpg', 'images/nuoi-dong.jpg', 'images/bio-prep.jpg', 'images/chem-bag.jpg'])) {
                            @unlink(__DIR__ . '/' . $_POST['existing_image']);
                        }
                    } else {
                        $error = "Lỗi khi lưu trữ file hình ảnh.";
                    }
                } else {
                    $error = "Định dạng file ảnh không hợp lệ. Chỉ chấp nhận: " . implode(', ', $allowed_exts);
                }
            }

            if (empty($error)) {
                if ($action === 'add') {
                    $insertSQL = "INSERT INTO products (product_key, name, category_id, badge, badge_class, origin, price, image, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $insert = db_query($insertSQL, "ssissssss", [$product_key, $name, $category_id, $badge, $badge_class, $origin, $price, $image_path, $description]);
                    if ($insert) {
                        $success = "Thêm sản phẩm mới thành công!";
                        header('refresh:1.5;url=admin_products.php');
                        $action = 'list';
                    } else {
                        $error = "Lỗi khi lưu sản phẩm vào cơ sở dữ liệu.";
                    }
                } else {
                    $updateSQL = "UPDATE products SET product_key = ?, name = ?, category_id = ?, badge = ?, badge_class = ?, origin = ?, price = ?, image = ?, description = ? WHERE id = ?";
                    $update = db_query($updateSQL, "ssissssssi", [$product_key, $name, $category_id, $badge, $badge_class, $origin, $price, $image_path, $description, $id]);
                    if ($update) {
                        $success = "Cập nhật thông tin sản phẩm thành công!";
                        header('refresh:1.5;url=admin_products.php');
                        $action = 'list';
                    } else {
                        $error = "Lỗi khi cập nhật cơ sở dữ liệu.";
                    }
                }
            }
        }
    }
}

// --------------------------------------------------------------------------
// Form Rendering (Add / Edit)
// --------------------------------------------------------------------------
if ($action === 'add' || $action === 'edit'):
    $product = [
        'name' => '', 'product_key' => '', 'category_id' => '', 'price' => 'Liên hệ báo giá', 
        'origin' => '', 'badge' => '', 'badge_class' => '', 'image' => '', 'description' => ''
    ];
    if ($action === 'edit' && $id > 0) {
        $res = db_query("SELECT * FROM products WHERE id = ?", "i", [$id]);
        if ($res && $res->num_rows > 0) {
            $product = $res->fetch_assoc();
        } else {
            $error = "Không tìm thấy sản phẩm.";
            $action = 'list';
        }
    }
    
    if ($action !== 'list'):
?>
    <div class="admin-card">
        <div class="admin-card-header">
            <h3><i class="fa-solid fa-square-plus"></i> <?= $action === 'add' ? 'Thêm Sản Phẩm Mới' : 'Sửa Thông Tin Sản Phẩm' ?></h3>
            <a href="admin_products.php" class="btn btn-outline btn-sm" style="border-radius: 8px;"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>
        </div>
        <div class="admin-card-body">
            <?php if (!empty($error)): ?>
                <div class="admin-alert admin-alert-danger"><?= h($error) ?></div>
            <?php endif; ?>
            
            <form action="admin_products.php?action=<?= $action ?>&id=<?= $id ?>" method="POST" enctype="multipart/form-data" class="admin-form">
                <?= auth_csrf_token_field() ?>
                <input type="hidden" name="existing_image" value="<?= h($product['image']) ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Tên sản phẩm <span style="color:red">*</span></label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Ví dụ: Phân bón lá Amino" required value="<?= h($product['name']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="product_key">Mã định danh (Slug Key) - Để trống sẽ tự tạo</label>
                        <input type="text" id="product_key" name="product_key" class="form-control" placeholder="Ví dụ: phan-bon-amino" value="<?= h($product['product_key']) ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category_id">Danh mục sản phẩm <span style="color:red">*</span></label>
                        <select id="category_id" name="category_id" class="form-control" required>
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                    <?= h($cat['name']) ?> (<?= h($cat['type']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price">Giá hiển thị</label>
                        <input type="text" id="price" name="price" class="form-control" placeholder="Ví dụ: Liên hệ báo giá, 50.000đ" value="<?= h($product['price']) ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="origin">Xuất xứ / Thương hiệu</label>
                        <input type="text" id="origin" name="origin" class="form-control" placeholder="Ví dụ: Nhập khẩu Hàn Quốc" value="<?= h($product['origin']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="badge">Nhãn sản phẩm (Badge)</label>
                        <input type="text" id="badge" name="badge" class="form-control" placeholder="Ví dụ: Nông Nghiệp, Hóa Chất" value="<?= h($product['badge']) ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="badge_class">Lớp CSS cho nhãn (Badge Class)</label>
                        <select id="badge_class" name="badge_class" class="form-control">
                            <option value="" <?= empty($product['badge_class']) ? 'selected' : '' ?>>Mặc định (Màu xanh lá)</option>
                            <option value="badge-bio" <?= $product['badge_class'] === 'badge-bio' ? 'selected' : '' ?>>Vi Sinh (Màu xanh dương)</option>
                            <option value="badge-chemical" <?= $product['badge_class'] === 'badge-chemical' ? 'selected' : '' ?>>Hóa Chất (Màu cam)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="product_image">Hình ảnh sản phẩm</label>
                        <input type="file" id="product_image" name="product_image" class="form-control" accept="image/*">
                        <?php if (!empty($product['image'])): ?>
                            <div style="margin-top: 10px; display: flex; align-items: center; gap: 10px;">
                                <img src="<?= h($product['image']) ?>" alt="Ảnh hiện tại" style="max-height: 80px; border-radius: 6px; border: 1px solid var(--color-admin-border);">
                                <span style="font-size: 0.8rem; color: var(--color-admin-text-muted);">Đường dẫn: <?= h($product['image']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Mô tả sản phẩm</label>
                    <textarea id="description" name="description" class="form-control" placeholder="Nhập nội dung mô tả chi tiết sản phẩm..."><?= h($product['description']) ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" style="border-radius: 8px;">
                        Lưu Thay Đổi <i class="fa-solid fa-floppy-disk"></i>
                    </button>
                    <a href="admin_products.php" class="btn btn-outline" style="border-radius: 8px; border-color: var(--color-admin-border); color: var(--color-admin-text-dark);">Hủy bỏ</a>
                </div>
            </form>
        </div>
    </div>
<?php 
    endif;
endif; 

// --------------------------------------------------------------------------
// Products List View
// --------------------------------------------------------------------------
if ($action === 'list'):
    // Setup Search & Category Filter
    $search = trim($_GET['search'] ?? '');
    $cat_filter = (int)($_GET['category'] ?? 0);

    $sql = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE 1=1";
    $types = "";
    $params = [];

    if (!empty($search)) {
        $sql .= " AND (p.name LIKE ? OR p.product_key LIKE ?)";
        $search_wildcard = "%$search%";
        $types .= "ss";
        $params[] = $search_wildcard;
        $params[] = $search_wildcard;
    }

    if ($cat_filter > 0) {
        $sql .= " AND p.category_id = ?";
        $types .= "i";
        $params[] = $cat_filter;
    }

    $sql .= " ORDER BY p.id DESC";

    $products = db_query($sql, !empty($types) ? $types : null, !empty($params) ? $params : null);
?>
    <div class="admin-card">
        <div class="admin-card-header">
            <h3><i class="fa-solid fa-boxes-stacked"></i> Danh Sách Sản Phẩm</h3>
            <a href="admin_products.php?action=add" class="btn btn-primary btn-sm" style="border-radius: 8px;"><i class="fa-solid fa-plus"></i> Thêm sản phẩm mới</a>
        </div>
        <div class="admin-card-body" style="padding: 0;">
            <!-- Filters -->
            <div style="padding: 1.5rem 2rem; border-bottom: 1px solid var(--color-admin-border); background-color: #f8fafc;">
                <form action="admin_products.php" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 250px;">
                        <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên sản phẩm, slug..." value="<?= h($search) ?>">
                    </div>
                    <div style="min-width: 200px;">
                        <select name="category" class="form-control">
                            <option value="">-- Tất cả danh mục --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $cat_filter == $cat['id'] ? 'selected' : '' ?>>
                                    <?= h($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-secondary" style="border-radius: 8px; padding: 0.5rem 1.5rem;">Lọc sản phẩm</button>
                    <?php if (!empty($search) || $cat_filter > 0): ?>
                        <a href="admin_products.php" class="btn btn-outline" style="border-radius: 8px; padding: 0.5rem 1.5rem; border-color: var(--color-admin-border); color: var(--color-admin-text-dark);">Xóa lọc</a>
                    <?php endif; ?>
                </form>
            </div>

            <?php if (!empty($success)): ?>
                <div style="padding: 1rem 2rem 0 2rem;">
                    <div class="admin-alert admin-alert-success"><?= h($success) ?></div>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Hình</th>
                            <th>Mã định danh (Slug)</th>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Giá</th>
                            <th>Xuất xứ</th>
                            <th>Nhãn</th>
                            <th style="width: 120px; text-align: center;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($products && $products->num_rows > 0): ?>
                            <?php while ($prod = $products->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <img src="<?= h($prod['image'] ?: 'images/chem-bag.jpg') ?>" alt="Ảnh" style="max-height: 48px; max-width: 48px; object-fit: contain; border-radius: 4px; border: 1px solid var(--color-admin-border);">
                                    </td>
                                    <td><code><?= h($prod['product_key']) ?></code></td>
                                    <td style="font-weight: 600;"><?= h($prod['name']) ?></td>
                                    <td><?= h($prod['category_name']) ?></td>
                                    <td><span style="color: var(--color-primary); font-weight: 600;"><?= h($prod['price']) ?></span></td>
                                    <td><?= h($prod['origin'] ?: '-') ?></td>
                                    <td>
                                        <?php if (!empty($prod['badge'])): ?>
                                            <span class="badge <?= $prod['badge_class'] === 'badge-bio' ? 'badge-bio' : ($prod['badge_class'] === 'badge-chemical' ? 'badge-chemical' : 'badge-customer') ?>">
                                                <?= h($prod['badge']) ?>
                                            </span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="actions-cell" style="justify-content: center;">
                                            <a href="product-detail.php?id=<?= $prod['product_key'] ?>" class="btn-icon-only btn-view" target="_blank" title="Xem ngoài web">
                                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                            </a>
                                            <a href="admin_products.php?action=edit&id=<?= $prod['id'] ?>" class="btn-icon-only btn-edit" title="Sửa thông tin">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <a href="admin_products.php?action=delete&id=<?= $prod['id'] ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>" class="btn-icon-only btn-delete" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 3rem; color: var(--color-admin-text-muted);">
                                    Không tìm thấy sản phẩm nào phù hợp.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
require_once __DIR__ . '/includes/admin_footer.php';
?>
