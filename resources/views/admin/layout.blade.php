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



    <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
    }

    .app {
      display: flex;
      min-height: 100vh;
    }

    .sidebar {
      width: 200px;
      padding: 20px;
      transition: transform 0.3s ease-in-out;
    }

    .sidebar.hide {
      transform: translateX(-100%);
    }

    .sidebar h2 {
      font-size: 20px;
      margin-bottom: 20px;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
    }

    .sidebar ul li {
      margin-bottom: 10px;
    }

    .sidebar ul li a {
      text-decoration: none;
      color: #ffff;
      display: block;
      padding: 8px;
      border-radius: 4px;
    }

    .sidebar ul li a.active {
      background-color: #007bff;
      color: #fff;
    }

    .main-content {
      flex-grow: 1;
      padding: 20px;
      transition: margin-left 0.3s ease-in-out;
    }

    .main-content.expanded {
      margin-left: 0;
    }

    .toggle-btn {
      position: absolute;
      left: 10px;
      top: 10px;
      font-size: 20px;
      background: none;
      border: none;
      cursor: pointer;
      z-index: 1001;
    }
     .table {
      width: 100%;
      border-collapse: collapse;
    }

    .table th, .table td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: left;
    }

    .table th {
      background-color: #f0f0f0;
    }

    .btn {
      display: inline-block;
      padding: 6px 12px;
      text-decoration: none;
      border-radius: 4px;
    }

    .btn-success {
      background-color: #28a745;
      color: white;
    }

    .btn-danger {
      background-color: #dc3545;
      color: white;
    }

    .btn-sm {
      padding: 4px 8px;
      font-size: 0.875rem;
    }

    #pagination {
      margin-top: 15px;
      display: flex;
      flex-wrap: wrap;
      gap: 5px;
    }

    #pagination button {
      padding: 6px 12px;
      border: 1px solid #ccc;
      background-color: #f9f9f9;
      cursor: pointer;
      border-radius: 4px;
    }

    #pagination button.active {
      background-color: #007bff;
      color: white;
      border-color: #007bff;
    }

    .main-content {
      padding: 20px;
    }
.logout-btn {
  background: none;
  border: none;
  padding: 8px;
  text-align: left;
  color: #fff;
  width: 100%;
  font-size: 16px;
  cursor: pointer;
  border-radius: 4px;
}

.logout-btn:hover {
  background-color: #007bff;
  color: #fff;
}
/* Modernized search box */
.search-container {
  position: relative;
  max-width: 360px;
  margin-bottom: 16px;
}

.search-container input.search-box {
  width: 100%;
  padding: 12px 16px 12px 44px;   /* room for the icon */
  border: 1px solid #ccc;
  border-radius: 24px;
  font-size: 0.95rem;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
  transition: border-color 0.3s, box-shadow 0.3s;
}

.search-container input.search-box:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 2px 12px rgba(0, 123, 255, 0.2);
}

.search-container i.fa-search {
  position: absolute;
  top: 50%;
  left: 14px;
  transform: translateY(-50%);
  font-size: 1rem;
  color: #888;
  transition: color 0.3s;
}

/* change icon color on focus */
.search-container input.search-box:focus + .fa-search,
.search-container input.search-box:not(:placeholder-shown) + .fa-search {
  color:#007bff;
}

    @media (max-width: 768px) {
      .sidebar {
        position: absolute;
        height: 100%;
        z-index: 1000;
      }

      .main-content {
        margin-left: 0;
      }
    }
    @media (max-width: 768px) {
  .sidebar {
    transform: translateX(-100%);
  }
  .sidebar.show {
    transform: translateX(0);
  }
}

@media (min-width: 769px) {
  .summary-cards {
    justify-content: flex-start;
  }

  .summary-card {
    flex: 0 0 300px;
    max-width:300px;
    }
}
/* Responsive Table for Mobile */
@media (max-width: 768px) {
  /* Make table use full width and fixed layout */
  .table {
    width: 100% !important;
    table-layout: fixed;
  }

  /* Shrink padding and font-size so columns fit */
  .table th,
  .table td {
    padding: 6px 8px !important;
    font-size: 0.6rem !important;
    white-space: nowrap;  /* कॉलम टेक्स्ट लाइन ब्रेक न हो */
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .btn,
  .btn-sm {
    padding: 2px 4px !important;
    font-size: 0.5rem !important;
  }

  #pagination button {
    padding: 4px 8px !important;
    font-size: 0.75rem !important;
  }

  .table-container,
  #table-view {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
}


.btn-danger,
.btn-danger.btn-sm {
  border-radius: 15px !important;
}

.btn-success {
  border-radius: 20px !important;
}

  </style>
</head>
<body>
<div class="app">
  <aside class="sidebar" id="sidebar">

    <ul style="margin-top:50px;">
      <li>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
          <i class="fa-solid fa-chart-line"></i> Dashboard
        </a>
      </li>
      <li>
        <a href="{{ route('admin.employees') }}" class="{{ request()->routeIs('admin.employees') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i> Employee

        </a>
      </li>
      <li>
        <a href="{{ route('admin.doctors') }}" class="{{ request()->routeIs('admin.doctors') ? 'active' : '' }}">
                <i class="fas fa-user-md"></i> Doctors

        </a>
      </li>
      <li>
        <a href="{{ route('admin.banner') }}" class="{{ request()->routeIs('admin.banner') ? 'active' : '' }}">
                <i class="fa-solid fa-flag"></i> Banner
        </a>
      </li>
        <li>
            <a href="#" id="logoutLink"
               style="color:#fff; text-decoration:none; display:block; padding:8px;">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>

        </li>

    </ul>
  </aside>

  <!-- Toggle button -->
    <button class="toggle-btn" id="toggleSidebarBtn">
        <i class="fa fa-bars"></i>
    </button>


    <div class="main-content" id="mainContent">
    <div class="container mt-4">
      @yield('content')
    </div>
  </div>
</div>
<script nonce="{{ $cspNonce }}">
    document.addEventListener('DOMContentLoaded', function () {

        const logoutLink = document.getElementById('logoutLink');
        if (!logoutLink) return;

        logoutLink.addEventListener('click', function (e) {
            e.preventDefault();
            logoutAdmin();
        });

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
            .catch(error => {
                console.error("Error logging out:", error);
            });
    }
</script>


<script nonce="{{ $cspNonce }}">
    document.addEventListener('DOMContentLoaded', function () {

        const btn = document.getElementById('toggleSidebarBtn');
        if (!btn) return;

        btn.addEventListener('click', toggleSidebar);

    });

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('mainContent');

        sidebar?.classList.toggle('hide');
        content?.classList.toggle('expanded');
    }
</script>

<script nonce="{{ $cspNonce }}">
  document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('mainContent');
    const toggleBtn = document.querySelector('.toggle-btn');

    // helper to detect "mobile"
    const isMobile = () => window.innerWidth <= 768;

    // on page load & resize: ensure correct default state
    const applyResponsiveDefault = () => {
      if (isMobile()) {
        sidebar.classList.remove('show');      // hide
        content.classList.remove('expanded');
      } else {
        sidebar.classList.add('show');         // show on desktop
        content.classList.add('expanded');
      }
    };
    applyResponsiveDefault();
    window.addEventListener('resize', applyResponsiveDefault);

    // toggle button: just flip the 'show' class
    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('show');
      content.classList.toggle('expanded');
    });

    // whenever any sidebar link is clicked, auto‐close on mobile
    sidebar.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        if (isMobile()) {
          sidebar.classList.remove('show');
          content.classList.remove('expanded');
        }
      });
    });
    });
</script>

</body>
</html>
