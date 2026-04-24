@extends('admin.layout')

@push('styles')
    <!-- DataTables Bootstrap 5 & Responsive CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@section('content')
    <div class="page-header">
        <div class="page-title-group">
            <h4>Banners</h4>
            <p>Manage generated banners</p>
        </div>
        <button id="exportExcelBtn" class="btn-add btn-export">
            <i class="fas fa-file-excel"></i> Export Excel
        </button>
    </div>

    <!-- Custom Modern Search Bar -->
    <div class="filter-bar">
        <div class="search-wrap">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="customSearchBox" class="filter-input" placeholder="Search Banners..."
                   autocomplete="off">
        </div>
        <div class="search-wrap">
            <i class="fas fa-search search-icon"></i>
            <input type="date" id="dateSearch" class="filter-input" placeholder="Search Banners..."
                   autocomplete="off">
        </div>
    </div>

    <div class="glass-card">
        <div class="table-wrap">
            <table class="doc-table dt-responsive nowrap" id="bannersTable" style="width:100%">
                <thead>
                <tr>
                    <th class="all">ID</th>
                    <th class="all">Employee Name</th>
                    <th>Employee Code</th>
                    <th>Doctor Name</th>
                    <th>Specility</th>
                    <th>Language</th>
                    <th>Day</th>
                    <th>Banner</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse($employees as $emp)
                    <tr>
                        <td class="serial-cell">{{ $loop->iteration }}</td>
                        <td style="font-weight:500;">{{ $emp->user ? $emp->user->name : 'N/A' }}</td>
                        <td><span class="badge-mono emp">{{ $emp->user ? $emp->user->emp_code : 'N/A' }}</span></td>
                        <td>
                            <div class="doc-name-cell">
                                <span class="doc-name-text">{{ $emp->name }}</span>
                            </div>
                        </td>
                        <td><span class="badge-mono">{{ $emp->degree }}</span></td>
                        <td>{{ ucwords($emp->language) }}</td>
                        <td>{{ $emp->day }}</td>
                        <!-- Download Action -->
                        <td>
                            <div class="action-btns">
                                <a href="{{ $emp->banner_path }}" class="act-btn banner-btn download-link"
                                   title="Download Banner">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            </div>
                        </td>

                        <!-- Delete Action -->
                        <td>
                            <div class="action-btns">
                                <form action="{{ route('doctor.delete', $emp->id) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="act-btn del btn-delete" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <!-- Fallback empty state -->
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script nonce="{{ $cspNonce ?? '' }}">
        $(document).ready(function () {
            // Initialize DataTable
            var table = $('#bannersTable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthChange: true,
                language: {
                    emptyTable: `<div class="empty-state">
                                    <i class="fas fa-image"></i>
                                    <h5>No Banners found</h5>
                                    <p>No banners have been added yet.</p>
                                </div>`
                }
            });

            // Hide default DataTables search box and bind our Custom Search Bar
            $('.dataTables_filter').hide();
            $('#customSearchBox').on('keyup', function () {
                table.search(this.value).draw();
            });
            $('#dateSearch').on('change', function () {
                selectedDate = $(this).val();
                loadTableData();
            });
            let selectedDate = '';

            function loadTableData() {

                $.ajax({
                    url: "{{ url('/admin/all-banners') }}",
                    type: "GET",
                    data: {date: selectedDate},
                    success: function (data) {

                        table.clear();

                        data.forEach(function (item, index) {
                            table.row.add([
                                index + 1,
                                item.employee_name ?? 'N/A',
                                item.user_code ?? 'N/A',
                                item.name,
                                item.degree,
                                item.language,
                                item.day,
                                `<a href="${item.banner_path}" class="act-btn banner-btn download-link">
                        <i class="fa-solid fa-download"></i>
                    </a>`,
                                '', // created_at hidden hai
                                ''  // action blank (optional)
                            ]);
                        });

                        table.draw();
                    }
                });
            }

            // Export Excel functionality
            $('#exportExcelBtn').on('click', function () {

                fetch("{{ url('/admin/all-banners') }}?date=" + selectedDate)
                    .then(response => response.json())
                    .then(data => {

                        const formattedData = data.map(doctor => ({
                            'Doctor Name': doctor.name,
                            'Degree': doctor.degree,
                            'Banner Path': doctor.banner_path,
                            'Language': doctor.language,
                            'Day': doctor.day,
                            'Employee Name': doctor.employee_name,
                            'Employee Code': doctor.user_code
                        }));

                        const wb = XLSX.utils.book_new();
                        const ws = XLSX.utils.json_to_sheet(formattedData);
                        XLSX.utils.book_append_sheet(wb, ws, "Banners");

                        XLSX.writeFile(wb, "banners.xlsx");
                    })
                    .catch(error => {
                        console.error("Error fetching banners:", error);
                    });
            });

            // Custom Download logic (from original code)
            document.querySelectorAll('.download-link').forEach(link => {
                link.addEventListener('click', async function (e) {
                    e.preventDefault();

                    let url = this.getAttribute('href');
                    let icon = this.innerHTML;

                    this.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

                    try {
                        const res = await fetch(url);
                        if (!res.ok) throw new Error('Download failed');

                        const blob = await res.blob();
                        const blobUrl = window.URL.createObjectURL(blob);

                        let fileName = url.split('/').pop().split('?')[0] || 'poster.jpg';

                        let a = document.createElement('a');
                        a.href = blobUrl;
                        a.download = fileName;

                        document.body.appendChild(a);
                        a.click();
                        a.remove();

                        window.URL.revokeObjectURL(blobUrl);

                    } catch (err) {
                        console.error(err);
                        alert('Download failed');
                    }

                    this.innerHTML = icon;
                });
            });

            // SweetAlert2 Delete Confirmation Logic
            $(document).on('click', '.btn-delete', function (e) {
                e.preventDefault();

                // Jis form ke andar button press hua hai, us form ko pakdo
                var form = $(this).closest('form');

                Swal.fire({
                    title: 'Delete Banner?',
                    text: "Are you sure you want to delete this banner? This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e53e3e', // Red color for confirm (match your theme)
                    cancelButtonColor: '#1D507B',  // Navy color for cancel
                    confirmButtonText: '<i class="fas fa-trash-alt"></i> Yes, Delete',
                    cancelButtonText: '<i class="fas fa-times"></i> Cancel',
                    reverseButtons: true // Cancel pehle, Delete baad me (industry standard)
                }).then((result) => {
                    if (result.isConfirmed) {
                        // User ne yes dabaya, toh ab form submit kardo
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
