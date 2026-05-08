@extends('admin.layout')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
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

    <div class="filter-bar">
        <div class="search-wrap">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="customSearchBox" class="filter-input"
                   placeholder="Search Banners..." autocomplete="off">
        </div>
        <div class="search-wrap">
            <i class="fas fa-calendar search-icon"></i>
            <input type="date" id="dateSearch" class="filter-input" autocomplete="off">
        </div>
        <div class="search-wrap">
            <select id="employeeSearch" class="filter-input">
                <option value="">-- All Employees --</option>
                @foreach($userList as $user)
                    <option value="{{ $user->name }}">
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
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
                    <th>Speciality</th>
                    <th>Language</th>
                    <th>Day</th>
                    <th>Banner</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script nonce="{{ $cspNonce ?? '' }}">
        $(document).ready(function () {

            $('#employeeSearch').select2({
                placeholder: '-- All Employees --',
                allowClear: true,
                width: '100%'
            });

            let selectedDate     = '';
            let selectedEmployee = '';

            // ✅ Server-side DataTable
            var table = $('#bannersTable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,   // 👈 KEY CHANGE
                ajax: {
                    url: "{{ url('/admin/all-banners') }}",
                    data: function (d) {
                        d.date     = selectedDate;
                        d.employee = selectedEmployee;
                    }
                },
                columns: [
                    { data: 'sr_no',         orderable: false },
                    { data: 'employee_name'  },
                    { data: 'user_code'      },
                    { data: 'name'           },
                    { data: 'degree'         },
                    { data: 'language'       },
                    { data: 'day'            },
                    {
                        data: 'banner_path',
                        orderable: false,
                        render: function (url) {
                            return `<a href="${url}" class="act-btn banner-btn download-link" title="Download">
                                <i class="fa-solid fa-download"></i>
                            </a>`;
                        }
                    },
                    {
                        data: 'id',
                        orderable: false,
                        render: function (id) {
                            return `<div class="action-btns">
                        <form action="/doctor/delete/${id}" method="POST" class="delete-form">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="button" class="act-btn del btn-delete" title="Delete">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>`;
                        }
                    }
                ],
                pageLength: 10,
                language: {
                    emptyTable: `<div class="empty-state">
                            <i class="fas fa-image"></i>
                            <h5>No Banners found</h5>
                         </div>`
                }
            });

            $('.dataTables_filter').hide();

            // Filters reload table (server re-queries)
            $('#customSearchBox').on('keyup', function () {
                table.search(this.value).draw();
            });

            $('#dateSearch').on('change', function () {
                selectedDate = $(this).val();
                table.ajax.reload();
            });

            $('#employeeSearch').on('change', function () {
                selectedEmployee = $(this).val() || '';
                table.ajax.reload();
            });

            // Export — fetches only filtered data (no draw param)
            $('#exportExcelBtn').on('click', function () {
                const params = new URLSearchParams({
                    date: selectedDate,
                    employee: selectedEmployee
                });
                fetch("{{ url('/admin/all-banners') }}?" + params)
                    .then(r => r.json())
                    .then(data => {
                        const rows = data.map(d => ({
                            'Employee Name': d.employee_name ?? 'N/A',
                            'Employee Code': d.user_code ?? 'N/A',
                            'Doctor Name':   d.name,
                            'Degree':        d.degree,
                            'Language':      d.language,
                            'Day':           d.day,
                            'Banner Path':   d.banner_path,
                        }));
                        const wb = XLSX.utils.book_new();
                        XLSX.utils.book_append_sheet(wb, XLSX.utils.json_to_sheet(rows), "Banners");
                        XLSX.writeFile(wb, "banners.xlsx");
                    });
            });

            // Download handler (unchanged)
            $(document).on('click', '.download-link', async function (e) {
                e.preventDefault();
                let url  = this.getAttribute('href');
                let icon = this.innerHTML;
                this.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
                try {
                    const res  = await fetch(url);
                    const blob = await res.blob();
                    const bUrl = URL.createObjectURL(blob);
                    let a = document.createElement('a');
                    a.href = bUrl; a.download = url.split('/').pop().split('?')[0] || 'poster.jpg';
                    document.body.appendChild(a); a.click(); a.remove();
                    URL.revokeObjectURL(bUrl);
                } catch { alert('Download failed'); }
                this.innerHTML = icon;
            });

            // Delete confirm (unchanged)
            $(document).on('click', '.btn-delete', function () {
                var form = $(this).closest('form');
                Swal.fire({
                    title: 'Delete Banner?',
                    text: "This cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e53e3e',
                    cancelButtonColor:  '#1D507B',
                    confirmButtonText:  '<i class="fas fa-trash-alt"></i> Yes, Delete',
                    cancelButtonText:   '<i class="fas fa-times"></i> Cancel',
                    reverseButtons: true
                }).then(r => { if (r.isConfirmed) form.submit(); });
            });
        });
    </script>
@endpush
