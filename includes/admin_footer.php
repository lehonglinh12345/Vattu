            </div> <!-- End admin-content -->
        </main> <!-- End admin-main -->
    </div> <!-- End admin-wrapper -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('adminSidebarToggle');
            const adminSidebar = document.getElementById('adminSidebar');
            
            if (sidebarToggle && adminSidebar) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    adminSidebar.classList.toggle('open');
                });

                // Close sidebar when clicking outside on tablets/mobiles
                document.addEventListener('click', function(e) {
                    if (window.innerWidth <= 992) {
                        if (!adminSidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                            adminSidebar.classList.remove('open');
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
