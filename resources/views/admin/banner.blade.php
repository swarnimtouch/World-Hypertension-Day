@extends('admin.layout')

@section('content')

    <main class="main-content">
        <section id="table-view">
            <form method="GET" action="{{ route('admin.banner') }}" id="searchForm">
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

            <h2>All Banners</h2>
            <button id="exportExcelBtn" class="btn btn-success mb-2">Export Excel</button>

            <div class="table-container">
                <table class="table" id="employeeTable">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Employee Name</th>
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
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $emp->user ? $emp->user->name : 'N/A' }}</td>
                            <td>{{ $emp->user ? $emp->user->emp_code : 'N/A' }}</td>
                            <td>{{ $emp->name }}</td>
                            <td>{{ $emp->degree }}</td>
                            <td>{{ ucwords($emp->language) }}</td>
                            <td>{{ $emp->day }}</td>
                            <td>
                                <a href="{{ asset($emp->banner_path) }}" class="download-link" target="_blank" download>
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            </td>
                            <td>
                                <a href="{{ url('/admin/employees/delete/' . $emp->id) }}" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No Banner found</td>
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
                searchForm.submit(); // Auto submit after 0.5 seconds
            }, delay);
        });

        // Clear timer if user is still typing
        searchInput.addEventListener('keydown', function() {
            clearTimeout(typingTimer);
        });

        // Export Excel functionality
        document.getElementById('exportExcelBtn').addEventListener('click', function () {
            fetch("{{ url('/admin/all-banners') }}")
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
                    XLSX.writeFile(wb, "all_banners.xlsx");
                })
                .catch(error => {
                    console.error("Error fetching banners:", error);
                });
        });
    </script>

@endsection
