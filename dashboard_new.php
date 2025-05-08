<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FinSystem | Dashboard</title>
    <link rel="shortcut icon" href="assets/img/2020-nia-logo.svg" type="image/x-icon">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-green: #10B981;
            --dark-green: #047857;
            --light-green: #6EE7B7;
            --dark-text: #111827;
            --light-text: #6B7280;
            --sidebar-bg: #111827;
            --sidebar-text: #F9FAFB;
            --sidebar-active: #1F2937;
            --card-bg: #FFFFFF;
            --bg-color: #F9FAFB;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--dark-text);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            padding: 2rem 0;
            height: 100vh;
            position: sticky;
            top: 0;
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1rem;
        }

        .sidebar-brand h2 {
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-brand i {
            color: var(--primary-green);
        }

        .nav-menu {
            padding: 0 1rem;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .nav-link.active {
            background-color: var(--sidebar-active);
            color: white;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 2.5rem;
            background-color: #f8fafc;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
        }

        .header h1 {
            font-weight: 700;
            font-size: 1.8rem;
            color: #1e293b;
            position: relative;
            display: inline-block;
        }

        .header h1::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 50px;
            height: 4px;
            background: linear-gradient(90deg, #10b981, #6ee7b7);
            border-radius: 2px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: white;
            padding: 0.6rem 1rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .user-profile img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-profile span {
            font-weight: 500;
            color: #334155;
        }

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 1.5rem;
        }

        /* Feature Cards */
        .feature-card {
            grid-column: span 6;
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
            border: 1px solid #f1f5f9;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, #10b981, #6ee7b7);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .card-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            font-size: 1.25rem;
        }

        .card-badge {
            background: #f0fdf4;
            color: #065f46;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .card-title {
            font-weight: 700;
            font-size: 1.25rem;
            color: #1e293b;
            margin-bottom: 0.75rem;
        }

        .card-description {
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .card-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-stats {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748b;
            font-size: 0.85rem;
        }

        .card-stats i {
            color: #94a3b8;
        }

        .card-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.7rem 1.25rem;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .card-button:hover {
            background: #059669;
            transform: translateY(-2px);
        }

        .card-button i {
            transition: transform 0.3s ease;
        }

        .card-button:hover i {
            transform: translateX(3px);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                width: 240px;
                padding: 1.5rem 0;
            }

            .main-content {
                padding: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                padding: 1rem 0;
            }

            .sidebar-brand {
                padding: 0 1rem 1rem;
            }

            .nav-menu {
                display: flex;
                overflow-x: auto;
                padding: 0.5rem 1rem;
                gap: 0.5rem;
            }

            .nav-item {
                margin-bottom: 0;
                flex-shrink: 0;
            }

            .nav-link {
                padding: 0.5rem 0.75rem;
            }

            .main-content {
                padding: 1.5rem 1rem;
            }

            .cards-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <h2>
                <!-- <i class="fas fa-leaf"></i>  -->
                <img src="assets/img/2020-nia-logo.svg" alt="" width="20%">
                FinSystem
            </h2>
        </div>

        <nav class=" nav-menu">
            <div class="nav-item">
                <a href="#" class="nav-link active">
                    <i class="fas fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-wallet"></i>
                    <span>Fund Management</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>2307 BIR Form</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fa fa-sign-out"></i>
                    <span>Logout</span>
                </a>
            </div>

        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="header">
            <h1>Financial Dashboard</h1>
            <div class="user-profile">
                <span>Admin User</span>
                <img src="assets/img/user.png" alt="User">
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Fund Management Card -->
            <div class="feature-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <span class="card-badge">Updated Daily</span>
                </div>
                <h3 class="card-title">Fund Management</h3>
                <p class="card-description">
                    Comprehensive tools to manage all financial resources with real-time tracking and allocation capabilities.
                </p>
                <div class="card-actions">
                    <div class="card-stats">
                        <i class="fas fa-clock"></i>
                        <span>Last updated: Today</span>
                    </div>
                    <button class="card-button" id="fundManagementBtn">
                        Access Tools
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- BIR Form Export Card -->
            <div class="feature-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <span class="card-badge">Tax Ready</span>
                </div>
                <h3 class="card-title">Export 2307 BIR Form</h3>
                <p class="card-description">
                    Generate fully compliant BIR Form 2307 with automatic calculations and validation for error-free submissions.
                </p>
                <div class="card-actions">
                    <div class="card-stats">
                        <i class="fas fa-check-circle"></i>
                        <span>Compliant with 2023 standards</span>
                    </div>
                    <a href="export_bir_pdf_new.php">
                        <button class="card-button" id="exportBirBtn">
                            Export Now
                            <i class="fas fa-download"></i>
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Button interactions
        document.getElementById('fundManagementBtn').addEventListener('click', function() {
            const icon = this.querySelector('i');
            icon.classList.remove('fa-arrow-right');
            icon.classList.add('fa-spinner', 'fa-spin');

            setTimeout(() => {
                alert('Navigating to Fund Management');
                icon.classList.remove('fa-spinner', 'fa-spin');
                icon.classList.add('fa-arrow-right');
                // window.location.href = '/fund-management';
            }, 1000);
        });

        // document.getElementById('exportBirBtn').addEventListener('click', function() {
        //     const icon = this.querySelector('i');
        //     icon.classList.remove('fa-download');
        //     icon.classList.add('fa-spinner', 'fa-spin');
        //     this.disabled = true;

        //     setTimeout(() => {
        //         icon.classList.remove('fa-spinner', 'fa-spin');
        //         icon.classList.add('fa-check');
        //         this.textContent = 'Form Ready ';
        //         this.appendChild(icon);

        //         setTimeout(() => {
        //             alert('BIR Form 2307 ready for download');
        //             this.textContent = 'Export Form ';
        //             icon.classList.remove('fa-check');
        //             icon.classList.add('fa-download');
        //             this.appendChild(icon);
        //             this.disabled = false;
        //         }, 800);
        //     }, 1500);
        // });

        // Sidebar navigation
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                navLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Responsive sidebar for mobile
        function handleResize() {
            if (window.innerWidth < 768) {
                document.body.classList.add('mobile-view');
            } else {
                document.body.classList.remove('mobile-view');
            }
        }

        window.addEventListener('resize', handleResize);
        handleResize();
    </script>
</body>

</html>