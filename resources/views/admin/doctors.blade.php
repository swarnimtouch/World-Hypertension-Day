@extends('admin.layout')

@section('content')

    <main class="main-content">
        <section id="table-view">
            <form method="GET" action="{{ route('admin.doctors') }}" id="searchForm">
                <div class="search-container">
                    <i class="fa fa-search"></i>
                    <input type="text"
                           name="search"
                           id="searchInput"
                           value="{{ request('search') }}"
                           class="search-box"
                           placeholder="Search by Name, Code, ID"
                           autocomplete="off">
                </div>
            </form>

            <h2>All Doctors</h2>
            <button id="exportExcelBtn" class="btn btn-success mb-2">Export Excel</button>

            <div class="table-container">
                <table class="table" id="employeeTable">
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
                    @forelse($employees as $emp)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $emp->employee_code }}</td>
                            <td>{{ $emp->user ? $emp->user->name : 'N/A' }}</td>
                            <td>{{ $emp->name }}</td>
                            <td>{{ $emp->msl_code }}</td>
                            <td>{{ $emp->city }}</td>
                            <td>{{ $emp->degree }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No Doctor found</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center mt-3">
                    {{ $employees->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </section>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>

    <script nonce="{{ $cspNonce }}">
        // Auto-submit search form after typing stops
        let typingTimer;
        const delay = 500; // 0.5 second delay

        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');

        searchInput.addEventListener('input', function() {
            clearTimeout(typingTimer);

            typingTimer = setTimeout(() => {
                searchForm.submit();
            }, delay);
        });

        searchInput.addEventListener('keydown', function() {
            clearTimeout(typingTimer);
        });

        // Export Excel functionality
        document.getElementById('exportExcelBtn').addEventListener('click', function () {
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
    </script>

@endsection
