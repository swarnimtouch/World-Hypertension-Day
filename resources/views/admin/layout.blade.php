<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        World Hypertension Day |
        {{
            collect(explode('.', request()->route()->getName()))
                ->reject(fn($part) => $part === 'admin')
                ->map(fn($part) => ucfirst($part))
                ->implode(' ')
        }}
    </title>

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <!-- Custom CSS (Welbourg Premium Theme) -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}" />
    
    <!-- Dynamic Styles Injection (for DataTables etc.) -->
    @stack('styles')
</head>
<body>

    <!-- Sidebar Overlay for Mobile -->
    <div id="sidebar-overlay"></div>

    <!-- Sidebar Section -->
    <aside id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="fas fa-heartbeat"></i></div>
            <div>
                <div class="brand-name">Admin Panel</div>
                <div class="brand-sub">World Hypertension Day</div>
            </div>
        </div>

        <div class="sidebar-nav">
            <div class="nav-section-label">Main Menu</div>
            
            <a href="{{ route('admin.dashboard') }}" class="nav-item-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <div class="nav-icon"><i class="fa-solid fa-chart-line"></i></div>
                <span>Dashboard</span>
            </a>
            
            <a href="{{ route('admin.employees') }}" class="nav-item-link {{ request()->routeIs('admin.employees') ? 'active' : '' }}">
                <div class="nav-icon"><i class="fa-solid fa-users"></i></div>
                <span>Employees</span>
            </a>
            
            <a href="{{ route('admin.doctors') }}" class="nav-item-link {{ request()->routeIs('admin.doctors') ? 'active' : '' }}">
                <div class="nav-icon"><i class="fas fa-user-md"></i></div>
                <span>Doctors</span>
            </a>
            
            <a href="{{ route('admin.banner') }}" class="nav-item-link {{ request()->routeIs('admin.banner') ? 'active' : '' }}">
                <div class="nav-icon"><i class="fa-solid fa-image"></i></div>
                <span>Banners</span>
            </a>
        </div>

        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar"><i class="fas fa-user-shield"></i></div>
                <div class="user-info">
                    <div class="user-name">Administrator</div>
                    <div class="user-role">Admin</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Topbar Section -->
    <header id="topbar">
        <button class="topbar-toggle" id="topbarToggleBtn">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="topbar-breadcrumb">
            World Hypertension Day / 
            <span>
                {{ collect(explode('.', request()->route()->getName()))->reject(fn($part) => $part === 'admin')->map(fn($part) => ucfirst($part))->implode(' ') }}
            </span>
        </div>
        
        <div class="topbar-right">
            <a href="#" id="logoutLink" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </header>

    <!-- Main Content Area -->
    <main id="main-content">
        @yield('content')
    </main>

    <!-- Core Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Layout & Logout Logic -->
    <script nonce="{{ $cspNonce ?? '' }}">
        // Smooth Sidebar Toggle Logic
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const topbar = document.getElementById('topbar');
            const overlay = document.getElementById('sidebar-overlay');
            
            if (window.innerWidth <= 991) {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('show');
            } else {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                topbar.classList.toggle('expanded');
            }
        }

        document.getElementById('topbarToggleBtn')?.addEventListener('click', toggleSidebar);
        document.getElementById('sidebar-overlay')?.addEventListener('click', toggleSidebar);

        // Auto close sidebar on resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 991) {
                document.getElementById('sidebar').classList.remove('mobile-open');
                document.getElementById('sidebar-overlay').classList.remove('show');
            }
        });

        // Logout Logic
        document.getElementById('logoutLink')?.addEventListener('click', function (e) {
            e.preventDefault();
            logoutAdmin();
        });

        function logoutAdmin() {
            fetch("{{ route('admin.logout') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            })
            .then(response => {
                if (response.ok) {
                    window.location.href = "{{ route('admin.login') }}";
                } else {
                    alert("Logout failed.");
                }
            })
            .catch(error => console.error("Error logging out:", error));
        }
    </script>

    <!-- Dynamic Scripts Injection (for DataTables etc.) -->
    @stack('scripts')
</body>
</html>