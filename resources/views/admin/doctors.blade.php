@extends('admin.layout')

@push('styles')
    <!-- DataTables Bootstrap 5 & Responsive Extension CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@section('content')
    <div class="page-header">
        <div class="page-title-group">
            <h4>Doctors</h4>
            <p>Manage all doctors</p>
        </div>
        <button id="exportExcelBtn" class="btn-add btn-export">
            <i class="fas fa-file-excel"></i> Export Excel
        </button>
    </div>

    <!-- Custom Modern Search Bar -->
    <div class="filter-bar">
        <div class="search-wrap">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="customSearchBox" class="filter-input" placeholder="Search by Name, Code, ID..." autocomplete="off">
        </div>
    </div>

    <div class="glass-card">
        <div class="table-wrap">
            <!-- Added DataTables Classes: dt-responsive nowrap -->
            <table class="doc-table dt-responsive nowrap" id="doctorsTable" style="width:100%">
                <thead>
                    <tr>
                        <th>Sr. No</th>
                        <th>Employee Code</th>
                        <th>Employee Name</th>
                        <th>Doctor Name</th>
                        <th>Msl Code</th>
                        <th>City</th>
                        <th>Specility</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTables AJAX ke through data yaha khud insert karega -->
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
            // Initialize DataTable with Server-Side Processing
            var table = $('#doctorsTable').DataTable({
                responsive: true,
                processing: true, 
                serverSide: true, 
                pageLength: 10,
                lengthChange: true,
                ajax: "{{ route('admin.doctors') }}", 
                columns: [
                    // 'all' class forces DataTable to never hide these columns
                    { data: 'sr_no', name: 'sr_no', orderable: false, searchable: false, className: 'serial-cell all' },
                    { data: 'emp_code', name: 'employee_code' },
                    { data: 'emp_name', name: 'user.name', className: 'all' },
                    { data: 'doc_name', name: 'name' },
                    { data: 'msl_code', name: 'msl_code' },
                    { data: 'city', name: 'city' },
                    { data: 'speciality', name: 'degree' }
                ],
                // ...
                language: {
                    emptyTable: `<div class="empty-state">
                                    <i class="fas fa-user-md"></i>
                                    <h5>No Doctors found</h5>
                                    <p>No doctors have been added yet.</p>
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
                fetch("{{ url('/admin/all-doctors') }}")
                    .then(response => response.json())
                    .then(data => {
                        const formattedData = data.map(doctor => ({
                            'Employee Name': doctor.employee_name,
                            'Employee Code': doctor.employee_code,
                            'Doctor Name': doctor.name,
                            'MSL Code': doctor.msl_code,
                            'City': doctor.city,
                            'Specility': doctor.degree,
                        }));

                        const wb = XLSX.utils.book_new();
                        const ws = XLSX.utils.json_to_sheet(formattedData);
                        XLSX.utils.book_append_sheet(wb, ws, "Doctors");
                        XLSX.writeFile(wb, "all_doctors.xlsx");
                    })
                    .catch(error => {
                        console.error("Error fetching doctors:", error);
                    });
            });
        });
    </script>
@endpush