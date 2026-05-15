<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختيار الإدارة - منصة صوتي</title>
    <link rel="stylesheet" href="style.css?v=5">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
</head>

<body class="submit-page-body">
    <!-- Background Elements for Glassmorphism -->
    <div class="glass-bg-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>

    <!-- Modern Transparent Navigation Bar -->
    <nav class="navbar navbar-transparent submit-nav">
        <div class="container navbar-container">
            <div class="navbar-brand">
                <a href="index.html" class="brand-logo">
                    <span class="brand-name">صوتي</span><span class="brand-dot">.</span><span class="brand-domain">ma</span>
                </a>
            </div>
            <a href="index.html" class="back-home-btn"><i class="fas fa-arrow-right"></i> العودة للرئيسية</a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="submit-container">
        <div class="submit-header">
            <h1 class="submit-title">تحديد الإدارة المعنية</h1>
            <p class="submit-subtitle">المرجو البحث واختيار الإدارة التي تود توجيه طلبك أو شكايتك إليها</p>
        </div>

        <!-- Search and Filter Section -->
        <div class="glass-panel search-panel">
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="adminSearch" placeholder="ابحث عن وزارة، عمالة، مستشفى، مؤسسة..." autocomplete="off">
            </div>
            
            <div class="filter-categories" id="categoryFilters">
                <button class="filter-btn active" data-filter="all">الكل</button>
                <button class="filter-btn" data-filter="وزارات">وزارات</button>
                <button class="filter-btn" data-filter="مؤسسات عمومية">مؤسسات عمومية</button>
                <button class="filter-btn" data-filter="عمالات وأقاليم">عمالات وأقاليم</button>
                <button class="filter-btn" data-filter="جامعات وتعليم">جامعات وتعليم</button>
                <button class="filter-btn" data-filter="مستشفيات وصحة">مستشفيات وصحة</button>
                <button class="filter-btn" data-filter="محاكم وعدل">محاكم وعدل</button>
            </div>
        </div>

        <!-- Administrations Grid -->
        <div class="admin-grid" id="adminGrid">
            <!-- Items will be generated here by JS -->
        </div>

        <!-- Pagination or Load More (Optional, we can show all or implement simple pagination in JS) -->
        <div class="no-results" id="noResults" style="display: none;">
            <i class="fas fa-folder-open"></i>
            <h3>لا توجد نتائج</h3>
            <p>لم نتمكن من العثور على إدارة تطابق بحثك. يرجى المحاولة بكلمات أخرى.</p>
        </div>
    </main>

    <!-- Footer -->
    <footer class="submit-footer">
        <p>&copy; 2026 منصة صوتي - المملكة المغربية. جميع الحقوق محفوظة.</p>
    </footer>

    <!-- Scripts -->
    <script src="js/administrations.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const adminGrid = document.getElementById('adminGrid');
            const searchInput = document.getElementById('adminSearch');
            const filterBtns = document.querySelectorAll('.filter-btn');
            const noResults = document.getElementById('noResults');

            // Render Function
            function renderAdministrations(data) {
                adminGrid.innerHTML = '';
                
                if (data.length === 0) {
                    noResults.style.display = 'flex';
                } else {
                    noResults.style.display = 'none';
                    data.forEach(admin => {
                        const card = document.createElement('div');
                        card.className = 'admin-glass-card';
                        
                        card.innerHTML = `
                            <div class="admin-icon ${admin.colorClass}">
                                <i class="${admin.icon}"></i>
                            </div>
                            <div class="admin-info">
                                <h3>${admin.name}</h3>
                                <div class="admin-meta">
                                    <span class="admin-cat">${admin.category}</span>
                                    <span class="admin-phone"><i class="fas fa-phone-alt"></i> ${admin.phone}</span>
                                </div>
                            </div>
                            <div class="admin-action">
                                <a href="form.php?id=${admin.id}" class="select-admin-btn">
                                    اختيار <i class="fas fa-check-circle"></i>
                                </a>
                            </div>
                        `;
                        adminGrid.appendChild(card);
                    });
                }
            }

            // Initial Render
            renderAdministrations(administrations);

            // Search Functionality
            searchInput.addEventListener('input', (e) => {
                const searchTerm = e.target.value.trim().toLowerCase();
                const activeFilter = document.querySelector('.filter-btn.active').dataset.filter;
                filterData(searchTerm, activeFilter);
            });

            // Filter Functionality
            filterBtns.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    filterBtns.forEach(b => b.classList.remove('active'));
                    e.target.classList.add('active');
                    
                    const searchTerm = searchInput.value.trim().toLowerCase();
                    const activeFilter = e.target.dataset.filter;
                    filterData(searchTerm, activeFilter);
                });
            });

            function filterData(searchTerm, filterCategory) {
                let filtered = administrations;

                if (filterCategory !== 'all') {
                    filtered = filtered.filter(a => a.category === filterCategory);
                }

                if (searchTerm !== '') {
                    filtered = filtered.filter(a => 
                        a.name.toLowerCase().includes(searchTerm) || 
                        a.category.toLowerCase().includes(searchTerm) ||
                        a.phone.includes(searchTerm)
                    );
                }

                renderAdministrations(filtered);
            }
        });
    </script>
</body>
</html>
