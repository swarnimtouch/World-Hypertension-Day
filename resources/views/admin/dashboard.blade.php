@extends('admin.layout')

@section('content')
    <div class="page-header">
        <div class="page-title-group">
            <h4><i class="fas fa-chart-line"></i> Dashboard</h4>
            <p>Overview of World Hypertension Day</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        
        <!-- Total Employees Card -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="stat-card blue">
                <div class="stat-icon blue"><i class="fas fa-users"></i></div>
                <div>
                    <div class="stat-label">Total Employees</div>
                    <div class="stat-value">{{ $totalEmployees }}</div>
                </div>
            </div>
        </div>

        <!-- Total Doctors Card -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="stat-card blue">
                <div class="stat-icon blue"><i class="fas fa-user-md"></i></div>
                <div>
                    <div class="stat-label">Total Doctors</div>
                    <div class="stat-value">{{ $totalDoctors }}</div>
                </div>
            </div>
        </div>

        <!-- Total Banners Card -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="stat-card blue">
                <div class="stat-icon blue"><i class="fas fa-image"></i></div>
                <div>
                    <div class="stat-label">Total Banners</div>
                    <div class="stat-value">{{ $totalDoctors1 }}</div>
                </div>
            </div>
        </div>

    </div>
@endsection