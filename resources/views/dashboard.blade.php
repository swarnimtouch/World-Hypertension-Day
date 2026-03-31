<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title> World Hypertension Day</title>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="{{ asset('css/calendar.css') }}" />

  <style>
    /* Simple modal styling if not in CSS */
    .modal {
      display: none;
      position: fixed;
      z-index: 999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background: rgba(0, 0, 0, 0.6);
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      text-align: center;
      max-width: 400px;
      width: 90%;
      position: relative;
    }

    .modal .close {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 24px;
      cursor: pointer;
    }

    .day-box {
      background: #fff8f7;
      border: 2px solid #f0b4aa;
      border-radius: 12px;
      min-height: 96px;
      /* increase height so numbers are centered nicely */
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 12px 8px;
      /* bigger clickable area */
      font-weight: 700;
      color: #333;
      cursor: pointer;
      transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
      user-select: none;
      text-align: center;
    }

    .days-grid {
      display: grid;
      /* force 10 columns on larger screens */
      grid-template-columns: repeat(10, minmax(60px, 1fr));
      gap: 12px;
      align-items: stretch;
    }

    .day-box:hover {
      transform: translateY(-6px);
      box-shadow: 0 12px 24px rgba(229, 57, 53, 0.18);
      border-color: #e53935;
    }

    .day-box:active {
      transform: translateY(-2px);
      box-shadow: 0 6px 14px rgba(229, 57, 53, 0.12);
    }

    .day-box:focus {
      box-shadow: 0 0 0 4px rgba(229, 57, 53, 0.15);
      border-color: #e53935;
    }

    .day-box.selected {
      background: linear-gradient(135deg, #e53935, #d81b60);
      color: #fff;
      border: 2px solid rgba(0, 0, 0, 0.06);
      transform: translateY(-6px);
      box-shadow: 0 16px 40px rgba(216, 27, 96, 0.18);
    }

    /* inside label styling */
    .day-box .day-top {
      display: block;
      font-size: 13px;
      line-height: 1;
      opacity: 0.95;
    }

    .day-box .day-num {
      display: block;
      font-size: 20px;
      font-weight: 900;
      line-height: 1;
      margin-top: 6px;
    }

    /* animation (optional) */
    @keyframes cardPop {
      from {
        opacity: 0;
        transform: translateY(10px) scale(0.99);
      }

      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    .day-box {
      animation: cardPop 0.45s ease both;
      text-decoration:none;
    }

    /* RESPONSIVE: adjust columns on smaller screens */
    @media (max-width: 1200px) {
      .days-grid {
        grid-template-columns: repeat(10, minmax(64px, 1fr));
        gap: 14px;
      }

      .day-box {
        min-height: 86px;
        padding: 10px 6px;
      }
    }

    @media (max-width: 1100px) {
      .days-grid {
        grid-template-columns: repeat(8, minmax(64px, 1fr));
      }
    }

    @media (max-width: 900px) {
      .days-grid {
        grid-template-columns: repeat(6, minmax(56px, 1fr));
      }

      .day-box {
        min-height: 78px;
      }
    }

    @media (max-width: 600px) {
      .days-grid {
        grid-template-columns: repeat(4, minmax(48px, 1fr));
        gap: 10px;
      }

      .day-box {
        min-height: 70px;
      }
    }

    @media (max-width: 360px) {
      .days-grid {
        grid-template-columns: repeat(3, minmax(40px, 1fr));
      }

      .day-box {
        min-height: 64px;
        padding: 8px;
      }
    }

    @media (min-width: 1026px) {
      .card {
        max-width: 1100px;
      }
    }
    .left-side-line {
  position: fixed;
  top: 0;
  left: 0;
  height: 100vh;       /* full screen height */
  width: 80px;         /* adjust to your line’s thickness */
  z-index: 1;          /* stay behind topbar and content */
}

.left-side-line img {
  height: 100%;
  width: 100%;
  object-fit: cover;   /* stretch nicely */
}
.right-side-line {
  position: fixed;
  top: 0;
  right: 0;
  height: 100vh;
  width: 80px;
  z-index: 1;
}

.right-side-line img {
  height: 100%;
  width: 100%;
  object-fit: cover;
}


  </style>


</head>

<body>
  <div class="left-side-line">
  <img src="{{ asset('images/Left-Side.png') }}" alt="Left Line" />
</div>
<div class="right-side-line">
  <img src="{{ asset('images/Right-Side.png') }}" alt="Right Line">
</div>

  <header class="topbar">
    <div class="topbar-inner">
      <div class="brand">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="brand-logo" />
      </div>
      <div class="welcome">
        <h1>Welcome, <span class="username">{{ $employee->name ?? session('emp_code') }}</span></h1>
      </div>
      <div class="brand1">
        <img src="{{ asset('images/LIPITAS-LOGO.png') }}" alt="Logo" class="brand1-logo" />
      </div>
      <button id="logoutBtn" class="logout-btn desktop-only" type="button" aria-label="Logout">
        <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
        Logout
      </button>

    </div>
  </header>

  <h1 class="subtitle" style="text-align:center; margin: 20px 0;">Select a day to generate a doctor poster</h1>

  <main class="page-wrap">
    <section class="card">
      <div class="card-header">
        <h3>30-Day Calendar</h3>
      </div>
      <div class="days-grid" id="daysGrid" role="list">
        @for($i = 1; $i <= 30; $i++)
      <a href="{{ url('doctor/create/'.$i) }}" class="day-box">
    <span class="day-top">Day</span>
    <span class="day-num">{{ $i }}</span>
</a>

    @endfor
      </div>



      <!-- Hidden input in doctor form -->
      <input type="hidden" id="selected_day" name="day">

    </section>

    <section class="card recent-downloads" style="margin-top: 30px;">
      <div class="card-header">
        <h3>Recent Downloads</h3>

        <!-- Search (top-right inside card header) -->
        <div class="search-wrap">
          <label for="downloadsSearch" class="sr-only">Search Downloads</label>
          <div class="search-input-wrap">
            <i class="fa-solid fa-magnifying-glass search-icon" aria-hidden="true"></i>
            <input id="downloadsSearch" type="search" placeholder="Search " />
            <button id="clearSearchBtn" title="Clear search" aria-label="Clear search">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>
            
        </div>
      </div>
      <div class="table-wrap">
        <table class="downloads-table">
          <thead>
            <tr>
              <th>Poster Day</th>
              <th>Doctor Name</th>
              <th>Date Generated</th>
              <th>Language</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($recentPosters as $poster)
        <tr>
          <td>Day {{ $poster->day }}</td>
          <td>{{ !empty($poster->name) ? $poster->name : '-' }}</td>
          <td>{{ $poster->created_at->format('F d, Y') }}</td>
          <td>{{ucwords($poster->language ??'-')}}</td>
          <td>
          <a href="{{ asset($poster->banner_path) }}" class="download-link" target="_blank" download>
            <i class="fa-solid fa-download"></i> Download
          </a>

          </td>

        </tr>
      @endforeach
          </tbody>
        </table>
        <div class="table-footer">
          <div id="downloadsPagination" class="pagination" aria-label="Downloads pagination"></div>
                  
        </div>
        <div class="view-all" style="margin-top: 10px; text-align: right;">

        </div>
      </div>
    </section>
    <div class="mobile-only logout-wrapper">
  <button id="logoutBtnMobile" class="logout-btn" type="button" aria-label="Logout">
    <i class="fa-solid fa-right-from-bracket"></i> Logout
  </button>
</div>

  </main>

  <!-- Modal -->
  <div id="dayModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeDayModal">&times;</span>

        <p id="modalMessage" ></p>

      <h2 id="modalTitle" style="margin-top:20px;"></h2>
      <a id="doctorFormLink" href="#">
        <button style="padding:10px 20px; cursor:pointer;margin-top:20px;">Continue</button>
      </a>
    </div>
  </div>

  <script
      src="https://code.jquery.com/jquery-3.7.1.min.js"
      integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
      crossorigin="anonymous">
  </script>
  <script nonce="{{ $cspNonce }}">
    (function () {
      // CONFIG
      const pageSize = 10; // change this to show more/less rows per page

      // UTIL
      function debounce(fn, wait) {
        let t;
        return function (...args) {
          clearTimeout(t);
          t = setTimeout(() => fn.apply(this, args), wait);
        }
      }
      function normalize(text) { return (text || '').toString().trim().toLowerCase(); }

      // DOM refs
      const input = document.getElementById('downloadsSearch');
      const clearBtn = document.getElementById('clearSearchBtn');
      const tbody = document.querySelector('.downloads-table tbody');
      const paginationEl = document.getElementById('downloadsPagination');

      if (!tbody) return; // safety

      // Capture original data rows once (initial content)
      let baseRows = Array.from(tbody.querySelectorAll('tr')).filter(tr => !tr.classList.contains('no-results'));
      // internal state
      let filteredRows = baseRows.slice();
      let currentPage = 1;

      // Toggle custom clear button visibility
      function toggleClearBtn() {
        if (!clearBtn) return;
        clearBtn.style.display = input && input.value.trim().length ? 'inline-flex' : 'none';
      }

      // Render a page from filteredRows
      function renderPage(page) {
        currentPage = Math.max(1, Math.min(page, Math.ceil(filteredRows.length / pageSize) || 1));
        // hide all base rows initially
        baseRows.forEach(r => r.style.display = 'none');

        // remove existing no-results marker
        const existingNo = tbody.querySelector('.no-results');
        if (existingNo) existingNo.remove();

        if (filteredRows.length === 0) {
          // show no result
          const colCount = document.querySelectorAll('.downloads-table thead th').length || 4;
          const noRow = document.createElement('tr');
          noRow.className = 'no-results';
          const td = document.createElement('td');
          td.colSpan = colCount;
          td.innerText = 'No result found';
          noRow.appendChild(td);
          tbody.appendChild(noRow);
          // empty pagination
          renderPagination();
          return;
        }

        // calculate slice for current page
        const start = (currentPage - 1) * pageSize;
        const end = start + pageSize;
        const pageRows = filteredRows.slice(start, end);

        // show rows for this page in order
        pageRows.forEach(r => {
          r.style.display = '';
          // ensure if row was previously moved or altered it's present in tbody
          if (r.parentNode !== tbody) tbody.appendChild(r);
        });

        // ensure rows not in current page remain hidden (they're in baseRows so already hidden)
        renderPagination();
      }

      // Create pagination controls
      function renderPagination() {
        paginationEl.innerHTML = '';
        const total = filteredRows.length;
        const totalPages = Math.max(1, Math.ceil(total / pageSize));

        // If only one page and small dataset, optionally hide pagination
        paginationEl.style.display = 'inline-flex';

        function makeBtn(label, cls, disabled, onClick) {
          const b = document.createElement('button');
          b.type = 'button';
          b.className = 'page-btn' + (cls ? ' ' + cls : '');
          if (disabled) b.classList.add('disabled');
          b.innerHTML = label;
          if (!disabled) b.addEventListener('click', onClick);
          return b;
        }

        // Prev
        const prevBtn = makeBtn('<i class="fa-solid fa-chevron-left"></i>', 'icon', currentPage <= 1, () => goToPage(currentPage - 1));
        paginationEl.appendChild(prevBtn);

        // Page numbers: show a compact range (first, maybe ellipsis, some numbers, ellipsis, last)
        const maxButtons = 5; // how many numeric buttons to show
        let startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
        let endPage = startPage + maxButtons - 1;
        if (endPage > totalPages) { endPage = totalPages; startPage = Math.max(1, endPage - maxButtons + 1); }

        if (startPage > 1) {
          paginationEl.appendChild(makeBtn('1', '', false, () => goToPage(1)));
          if (startPage > 2) {
            const e = document.createElement('span'); e.className = 'ellipsis'; e.innerText = '…'; paginationEl.appendChild(e);
          }
        }

        for (let p = startPage; p <= endPage; p++) {
          const btn = makeBtn(p, (p === currentPage ? 'active' : ''), false, () => goToPage(p));
          paginationEl.appendChild(btn);
        }

        if (endPage < totalPages) {
          if (endPage < totalPages - 1) {
            const e = document.createElement('span'); e.className = 'ellipsis'; e.innerText = '…'; paginationEl.appendChild(e);
          }
          paginationEl.appendChild(makeBtn(totalPages, '', false, () => goToPage(totalPages)));
        }

        // Next
        const nextBtn = makeBtn('<i class="fa-solid fa-chevron-right"></i>', 'icon', currentPage >= totalPages, () => goToPage(currentPage + 1));
        paginationEl.appendChild(nextBtn);
      }

      // Navigate to page
      function goToPage(p) {
        renderPage(p);
        // scroll table view a bit into view (optional, keeps focus)
        // document.querySelector('.recent-downloads .table-wrap').scrollIntoView({behavior:'smooth', block:'nearest'});
      }

      // Perform filtering based on input
      function performSearchImmediate() {
        const q = input ? normalize(input.value) : '';
        // remove existing no-results if any
        const existingNo = tbody.querySelector('.no-results');
        if (existingNo) existingNo.remove();

        filteredRows = baseRows.filter(tr => {
          // skip any non-data rows (ensure there are td's)
          const cells = tr.querySelectorAll('td');
          if (!cells || cells.length === 0) return false;
          const poster = normalize(cells[0]?.innerText);
          const docname = normalize(cells[1]?.innerText);
          const dategen = normalize(cells[2]?.innerText);
          const hay = poster + ' ' + docname + ' ' + dategen;
          return !q || hay.indexOf(q) !== -1;
        });

        // if current page would be beyond last page after filtering, reset to 1
        currentPage = 1;
        renderPage(currentPage);
      }

      const performSearchDebounced = debounce(() => {
        performSearchImmediate();
        toggleClearBtn();
      }, 200);

      // Hook events
      if (input) {
        input.addEventListener('input', performSearchDebounced);

        // ESC clears
        input.addEventListener('keydown', function (e) {
          if (e.key === 'Escape' || e.key === 'Esc') {
            input.value = '';
            performSearchImmediate();
            toggleClearBtn();
            input.blur();
          }
        });
      }

      if (clearBtn) {
        clearBtn.addEventListener('click', function (e) {
          e.preventDefault();
          if (input) input.value = '';
          performSearchImmediate();
          toggleClearBtn();
          if (input) input.focus();
        });
      }

      // initialization
      toggleClearBtn();
      // initial render (first page)
      performSearchImmediate();

      // If rows may be added dynamically in future, call refreshBaseRows() to rebuild baseRows:
      function refreshBaseRows() {
        baseRows = Array.from(tbody.querySelectorAll('tr')).filter(tr => !tr.classList.contains('no-results'));
        filteredRows = baseRows.slice();
        currentPage = 1;
        performSearchImmediate();
      }
      // expose function on window for debugging if needed
      window.refreshDownloadsRows = refreshBaseRows;

    })();
  </script>

  <script nonce="{{ $cspNonce }}">
    document.addEventListener('DOMContentLoaded', function () {
      const backBtn = document.getElementById('backBtn');
      const logoutBtn = document.getElementById('logoutBtn');
      const logoutBtnMobile = document.getElementById('logoutBtnMobile'); // 👈 new

      if (backBtn) {
        backBtn.addEventListener('click', function () {
          window.location.href = "{{ route('dashboard') }}";
        });
      }

      if (logoutBtn) {
        logoutBtn.addEventListener('click', function () {
          window.location.href = "{{ route('logout') }}";
        });
      }
          if (logoutBtnMobile) { // 👈 new
      logoutBtnMobile.addEventListener('click', function () {
        window.location.href = "{{ route('logout') }}";
      });
    }

    });
  </script>

  <script nonce="{{ $cspNonce }}">
    $(document).ready(function () {
      // Tooltip on hover
      $('.day-box').hover(function () {
        let msg = $(this).data('message');
        $(this).attr('title', msg);  // Bootstrap/Browser tooltip
      });

      // Function when user clicks a day
      window.selectDay = function (day) {
        // Store in hidden input
        $('#selected_day').val(day);

        // Show in display field
        $('#selected_day_display').val("Day " + day);

        // Highlight selected box
        $('.day-box').removeClass('active');
        $(`.day-box[data-day='${day}']`).addClass('active');
      }
    });
  </script>
  <script nonce="{{ $cspNonce }}">
      document.addEventListener('DOMContentLoaded', function () {

          const modal = document.getElementById('dayModal');
          const closeBtn = document.getElementById('closeDayModal');

          // Close button
          closeBtn?.addEventListener('click', closeModal);

          // Click outside modal-content to close
          modal?.addEventListener('click', function (e) {
              if (e.target === modal) {
                  closeModal();
              }
          });

      });

      function openModal(day) {
          document.getElementById('modalMessage').innerText = "You have selected.";
          document.getElementById('modalTitle').innerText = "Day " + day;
          document.getElementById('doctorFormLink').href =
              "https://streamgo.in/digital/world-heart-day/doctor/create/" + day;

          document.getElementById('dayModal').style.display = "flex";
      }

      function closeModal() {
          document.getElementById('dayModal').style.display = "none";
      }
  </script>

</body>

</html>
