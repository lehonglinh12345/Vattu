<?php
$page_title = "Liên Hệ";
$page_desc = "Liên hệ Công ty Cổ phần Hóa chất Nhập khẩu Ngọc Ánh Dương tại Cần Thơ qua Hotline 0976828171 hoặc điền mẫu gửi yêu cầu báo giá phân bón và hóa chất.";
$active_page = 'contact';
include 'includes/head.php';
include 'includes/header.php';
require_once 'includes/db.php';

$form_errors = [];
$form_success = '';
$form_name = '';
$form_phone = '';
$form_email = '';
$form_subject = isset($_GET['subject']) ? trim($_GET['subject']) : '';
$form_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_name = trim($_POST['name'] ?? '');
    $form_phone = trim($_POST['phone'] ?? '');
    $form_email = trim($_POST['email'] ?? '');
    $form_subject = trim($_POST['subject'] ?? '');
    $form_message = trim($_POST['message'] ?? '');

    if ($form_name === '') {
        $form_errors[] = 'Vui lòng nhập họ và tên.';
    }
    if ($form_phone === '') {
        $form_errors[] = 'Vui lòng nhập số điện thoại.';
    }
    if ($form_message === '') {
        $form_errors[] = 'Vui lòng nhập nội dung yêu cầu.';
    }

    if (empty($form_errors)) {
        $saved = db_query(
            'INSERT INTO contact_messages (name, phone, email, subject, message) VALUES (?, ?, ?, ?, ?)',
            'sssss',
            [$form_name, $form_phone, $form_email, $form_subject, $form_message]
        );

        if ($saved === false) {
            $form_errors[] = 'Không thể lưu yêu cầu liên hệ. Vui lòng thử lại sau.';
        } else {
            $form_success = 'Cảm ơn bạn! Yêu cầu liên hệ đã được gửi thành công. Chúng tôi sẽ phản hồi sớm nhất có thể.';
            $form_name = $form_phone = $form_email = $form_message = '';
            $form_subject = isset($_GET['subject']) ? trim($_GET['subject']) : '';
        }
    }
}
?>

<!-- Page Header Banner -->
<section class="about-hero" style="background: linear-gradient(rgba(18, 24, 32, 0.75), rgba(18, 24, 32, 0.8)), url('images/hero-bg.jpg') center/cover;">
    <div class="container">
        <h1>Liên Hệ</h1>
        <div class="breadcrumbs">
            <a href="index.php">Trang chủ</a>
            <span>/</span>
            <span>Liên hệ</span>
        </div>
    </div>
</section>

<!-- Contact Info & Form Grid Section -->
<section class="section">
    <div class="container contact-grid">
        
        <!-- Left Column: Contact details card -->
        <div class="contact-info-panel">
            <h2>Thông Tin Liên Hệ</h2>
            <p>Vui lòng chọn phương thức liên hệ phù hợp nhất bên dưới để kết nối với bộ phận chăm sóc khách hàng và kỹ thuật của Ngọc Ánh Dương.</p>
            
            <div class="contact-info-list">
                <!-- Item 1: Office Address -->
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <div class="contact-info-text">
                        <h4>Trụ Sở Chính</h4>
                        <p>Số 100 đường A3, Khu dân cư Phú An, Phường Hưng Phú, Quận Cái Răng, Thành phố Cần Thơ, Việt Nam.</p>
                    </div>
                </div>
                
                <!-- Item 2: Phone Hotline -->
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fa-solid fa-phone-volume"></i>
                    </div>
                    <div class="contact-info-text">
                        <h4>Điện Thoại / Hotline</h4>
                        <p><a href="tel:0976828171" style="font-weight: 700; font-size: 1.15rem; color: var(--color-accent);">0976.828.171</a> (Mr. Dương)</p>
                    </div>
                </div>
                
                <!-- Item 3: Email Support -->
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </div>
                    <div class="contact-info-text">
                        <h4>Hộp Thư Điện Tử</h4>
                        <p><a href="mailto:ngocanhduongchemical@gmail.com">ngocanhduongchemical@gmail.com</a></p>
                    </div>
                </div>

                <!-- Item 4: Legal tax identity -->
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                    <div class="contact-info-text">
                        <h4>Mã Số Thuế Doanh Nghiệp</h4>
                        <p><strong>1801786436</strong> - Công ty Cổ phần Hóa chất Nhập khẩu Ngọc Ánh Dương.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column: Interactive Form -->
        <div class="contact-form-panel">
            <h3 style="font-size: 1.6rem; margin-bottom: 0.5rem; color: var(--color-secondary);">Gửi Tin Nhắn Cho Chúng Tôi</h3>
            <p style="color: var(--color-dark-muted); margin-bottom: 2rem; font-size: 0.95rem;">Quý khách vui lòng điền mẫu dưới đây để chúng tôi hỗ trợ nhanh nhất.</p>
            
            <?php if (!empty($form_success)): ?>
                <div style="background: #e6f7e8; border: 1px solid #8fcd97; color: #1f5d2b; padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem;">
                    <?php echo h($form_success); ?>
                </div>
            <?php elseif (!empty($form_errors)): ?>
                <div style="background: #fff0f0; border: 1px solid #e09b9b; color: #7a1f1f; padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem;">
                    <ul style="margin:0; padding-left: 1.25rem;">
                        <?php foreach ($form_errors as $error): ?>
                            <li><?php echo h($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form id="contactForm" method="POST">
                <div class="form-group">
                    <label for="formName">Họ và tên của bạn <span style="color: red;">*</span></label>
                    <input type="text" id="formName" name="name" class="form-control" placeholder="Nguyễn Văn A" required value="<?php echo h($form_name); ?>">
                </div>
                
                <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div>
                        <label for="formPhone">Số điện thoại <span style="color: red;">*</span></label>
                        <input type="tel" id="formPhone" name="phone" class="form-control" placeholder="0901xxxxxx" required value="<?php echo h($form_phone); ?>">
                    </div>
                    <div>
                        <label for="formEmail">Hộp thư Email</label>
                        <input type="email" id="formEmail" name="email" class="form-control" placeholder="email@gmail.com" value="<?php echo h($form_email); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="formSubject">Tiêu đề yêu cầu</label>
                    <input type="text" id="formSubject" name="subject" class="form-control" placeholder="Ví dụ: Báo giá sỉ phân bón Tăng Lực X3" value="<?php echo h($form_subject); ?>">
                </div>
                
                <div class="form-group">
                    <label for="formMessage">Nội dung chi tiết <span style="color: red;">*</span></label>
                    <textarea id="formMessage" name="message" class="form-control" placeholder="Quý khách vui lòng cung cấp quy cách đặt hàng, số lượng dự kiến hoặc yêu cầu kỹ thuật cần hỗ trợ..." required><?php echo h($form_message); ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; border-radius: 10px; margin-top: 1rem;">Gửi Yêu Cầu Liên Hệ <i class="fa-solid fa-paper-plane"></i></button>
            </form>
        </div>
        
    </div>
</section>

<!-- Google Maps Section -->
<section class="section map-section">
    <div class="container">
        <div class="map-container">
            <!-- Embedded map pointing to Ngọc Ánh Dương Company -->
            <iframe src="https://maps.google.com/maps?q=C%C3%94NG+TY+C%E1%BB%9ED+PH%E1%BA%A6N+H%C3%93A+CH%E1%BA%A4T+NH%E1%BA%ACP+KH%E1%BA%A8U+NG%E1%BB%8CC+%C3%81NH+D%C6%AF%C6%A0NG%2C+100+Duong+A3+KDC+Phu+An+Hung+Phu+Can+Tho&t=&z=16&ie=UTF8&iwloc=&output=embed" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</section>

<?php
include 'includes/footer.php';
?>
