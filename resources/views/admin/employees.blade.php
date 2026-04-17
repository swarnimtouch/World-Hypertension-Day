@extends('admin.layout')

@push('styles')
    <!-- DataTables Bootstrap 5 & Responsive Extension CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@section('content')
    <div class="page-header">
        <div class="page-title-group">
            <h4>Employees</h4>
            <p>Manage all registered employees</p>
        </div>
        <button id="exportExcelBtn" class="btn-add btn-export">
            <i class="fas fa-file-excel"></i> Export Excel
        </button>
    </div>

    <!-- Custom Modern Search Bar -->
    <div class="filter-bar">
        <div class="search-wrap">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="customSearchBox" class="filter-input" placeholder="Search by Name, Code, Designation..." autocomplete="off">
        </div>
    </div>

    <div class="glass-card">
        <div class="table-wrap">
            <table class="doc-table dt-responsive nowrap" id="employeesTable" style="width:100%">
                <thead>
                    <tr>
                        <th class="all">Sr. No</th>
                        <th class="all">Employee Name</th>
                        <th>Employee Code</th>
                        <th>Position Code</th>
                        <th>Designation</th>
                        <th>Hq Name</th>
                        <th>Hq Code</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                        <tr>
                            <td class="serial-cell">{{ $loop->iteration }}</td>
                            <td>
                                <div class="doc-name-cell">
                                    <span class="doc-name-text">{{ $emp->name }}</span>
                                </div>
                            </td>
                            <td><span class="badge-mono emp">{{ $emp->emp_code }}</span></td>
                            <td>{{ $emp->position_code }}</td>
                            <td><span class="badge-mono">{{ $emp->designation }}</span></td>
                            <td>{{ $emp->hq_name }}</td>
                            <td>{{ $emp->hq_code }}</td>
                        </tr>
                    @empty
                        <!-- Empty state is handled beautifully by DataTables -->
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

    <script nonce="{{ $cspNonce ?? '' }}">
        $(document).ready(function() {
            // Initialize DataTable with Responsive plugin
            var table = $('#employeesTable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthChange: true,
                language: {
                    emptyTable: `<div class="empty-state">
                                    <i class="fas fa-users"></i>
                                    <h5>No Employees found</h5>
                                    <p>No employees have been added yet.</p>
                                </div>`
                }
            });

            // Hide default DataTables search box and bind our Custom Search Bar
            $('.dataTables_filter').hide();
            $('#customSearchBox').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Export Excel functionality
            $('#exportExcelBtn').on('click', function () {
                fetch("{{ url('/admin/all-employees') }}")
                    .then(response => response.json())
                    .then(data => {
                        const formattedData = data.map(employee => ({
                            'Employee Name': employee.name,
                            'Employee Code': employee.emp_code,
                            'Position Code': employee.position_code,
                            'Designation': employee.designation,
                            'Hq Name': employee.hq_name,
                            'Hq Code': employee.hq_code
                        }));

                        const wb = XLSX.utils.book_new();
                        const ws = XLSX.utils.json_to_sheet(formattedData);
                        XLSX.utils.book_append_sheet(wb, ws, "Employees");
                        XLSX.writeFile(wb, "all_employees.xlsx");
                    })
                    .catch(error => {
                        console.error("Error fetching employees:", error);
                    });
            });
        });
    </script>
@endpush