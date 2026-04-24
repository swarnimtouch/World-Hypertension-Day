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
                        <td>
                            <div class="action-btns">
                                <a href="{{ $emp->banner_path }}" class="act-btn banner-btn download-link"
                                   title="Download Banner">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            </div>
                        </td>
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
                @endforelse
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

            // ── DataTable init ────────────────────────────────────
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

            $('.dataTables_filter').hide();

            // ── Select2 ───────────────────────────────────────────
            $('#employeeSearch').select2({
                placeholder: '-- All Employees --',
                allowClear: true,
                width: '100%'
            });

            // ── Filter variables ──────────────────────────────────
            let selectedDate = '';
            let selectedEmployee = '';

            // ── Load data from server ─────────────────────────────
            function loadTableData() {
                $.ajax({
                    url: "{{ url('/admin/all-banners') }}",
                    type: "GET",
                    data: {
                        date: selectedDate,
                        employee: selectedEmployee
                    },
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
                                `<a href="${item.banner_path}" class="act-btn banner-btn download-link" title="Download">
                                    <i class="fa-solid fa-download"></i>
                                </a>`,
                                `<div class="action-btns">
                                    <form action="/doctor/delete/${item.id}" method="POST" class="delete-form">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="button" class="act-btn del btn-delete" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>`
                            ]);
                        });
                        table.draw();
                    }
                });
            }

            // ── Filters ───────────────────────────────────────────
            $('#customSearchBox').on('keyup', function () {
                table.search(this.value).draw();
            });

            $('#dateSearch').on('change', function () {
                selectedDate = $(this).val();
                loadTableData();
            });

            $('#employeeSearch').on('change', function () {
                selectedEmployee = $(this).val() || '';
                loadTableData();
            });


            // ── Export Excel ──────────────────────────────────────
            $('#exportExcelBtn').on('click', function () {
                fetch("{{ url('/admin/all-banners') }}?date=" + selectedDate + "&employee=" + encodeURIComponent(selectedEmployee))
                    .then(r => r.json())
                    .then(data => {
                        const formattedData = data.map(d => ({
                            'Employee Name': d.employee_name ?? 'N/A',
                            'Employee Code': d.user_code ?? 'N/A',
                            'Doctor Name': d.name,
                            'Degree': d.degree,
                            'Language': d.language,
                            'Day': d.day,
                            'Banner Path': d.banner_path,
                        }));
                        const wb = XLSX.utils.book_new();
                        const ws = XLSX.utils.json_to_sheet(formattedData);
                        XLSX.utils.book_append_sheet(wb, ws, "Banners");
                        XLSX.writeFile(wb, "banners.xlsx");
                    })
                    .catch(err => console.error("Export error:", err));
            });

            // ── Download ──────────────────────────────────────────
            $(document).on('click', '.download-link', async function (e) {
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

            // ── Delete confirm ────────────────────────────────────
            $(document).on('click', '.btn-delete', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                Swal.fire({
                    title: 'Delete Banner?',
                    text: "Are you sure? This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e53e3e',
                    cancelButtonColor: '#1D507B',
                    confirmButtonText: '<i class="fas fa-trash-alt"></i> Yes, Delete',
                    cancelButtonText: '<i class="fas fa-times"></i> Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });

        });
    </script>
@endpush
