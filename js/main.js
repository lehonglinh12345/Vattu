document.addEventListener('DOMContentLoaded', function() {
    
    // ==========================================================================
    // 1. Sticky Header & Scroll-to-Top Button
    // ==========================================================================
    const header = document.querySelector('.main-header');
    const scrollToTopBtn = document.getElementById('scrollToTopBtn');

    window.addEventListener('scroll', function() {
        // Sticky header
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }

        // Scroll-to-top button visibility
        if (window.scrollY > 300) {
            scrollToTopBtn.classList.add('show');
        } else {
            scrollToTopBtn.classList.remove('show');
        }
    });

    // Scroll back to top
    if (scrollToTopBtn) {
        scrollToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // ==========================================================================
    // 2. Mobile Navigation Toggle Drawer
    // ==========================================================================
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenuClose = document.getElementById('mobileMenuClose');
    const mobileNav = document.getElementById('mobileNav');
    const mobileOverlay = document.getElementById('mobileOverlay');

    function openMobileMenu() {
        mobileNav.classList.add('open');
        mobileOverlay.classList.add('open');
        document.body.style.overflow = 'hidden'; // Disable background scroll
    }

    function closeMobileMenu() {
        mobileNav.classList.remove('open');
        mobileOverlay.classList.remove('open');
        document.body.style.overflow = ''; // Re-enable scroll
    }

    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', openMobileMenu);
    }

    if (mobileMenuClose) {
        mobileMenuClose.addEventListener('click', closeMobileMenu);
    }

    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', closeMobileMenu);
    }

    // ==========================================================================
    // 3. Tab System (Product Detail Page)
    // ==========================================================================
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');

            // Deactivate all buttons & panes
            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));

            // Activate current button & target pane
            this.classList.add('active');
            const targetPane = document.getElementById(targetTab);
            if (targetPane) {
                targetPane.classList.add('active');
            }
        });
    });

    // ==========================================================================
    // 4. Contact Form Handler (Simulated AJAX Submit)
    // ==========================================================================
    const contactForm = document.getElementById('contactForm');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Fetch inputs
            const nameInput = document.getElementById('formName');
            const emailInput = document.getElementById('formEmail');
            const phoneInput = document.getElementById('formPhone');
            const subjectInput = document.getElementById('formSubject');
            const messageInput = document.getElementById('formMessage');

            // Quick validation
            if (!nameInput.value.trim() || !phoneInput.value.trim() || !messageInput.value.trim()) {
                alert('Vui lòng điền đầy đủ các thông tin bắt buộc (*): Họ tên, Số điện thoại và Nội dung.');
                return;
            }

            // Create dynamic success alert modal if it doesn't exist
            let alertModal = document.getElementById('successModal');
            if (!alertModal) {
                alertModal = document.createElement('div');
                alertModal.id = 'successModal';
                alertModal.className = 'modal-alert';
                alertModal.innerHTML = `
                    <div class="modal-alert-icon">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <h3>Gửi thành công!</h3>
                    <p>Cảm ơn bạn đã liên hệ. Ngọc Ánh Dương sẽ liên hệ lại với bạn trong vòng 24 giờ làm việc.</p>
                    <button class="btn btn-primary" id="closeModalBtn" style="padding: 0.5rem 1.5rem; font-size: 0.9rem;">Đóng</button>
                `;
                document.body.appendChild(alertModal);
                
                // Add overlay if needed
                let modalOverlay = document.getElementById('modalOverlay');
                if (!modalOverlay) {
                    modalOverlay = document.createElement('div');
                    modalOverlay.id = 'modalOverlay';
                    modalOverlay.className = 'mobile-overlay';
                    document.body.appendChild(modalOverlay);
                }
            }

            const modalOverlay = document.getElementById('modalOverlay');

            // Show success modal
            alertModal.classList.add('show');
            modalOverlay.classList.add('open');
            document.body.style.overflow = 'hidden';

            // Close logic
            const closeBtn = document.getElementById('closeModalBtn');
            const closeModal = () => {
                alertModal.classList.remove('show');
                modalOverlay.classList.remove('open');
                document.body.style.overflow = '';
            };

            closeBtn.onclick = closeModal;
            modalOverlay.onclick = closeModal;

            // Reset form fields
            contactForm.reset();
        });
    }

    // ==========================================================================
    // 5. Active Link Highlight Fallback
    // ==========================================================================
    // Handles toggles or extra client interactions
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            this.parentElement.classList.toggle('active');
        });
    });

});
