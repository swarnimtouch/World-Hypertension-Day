@extends('admin.layout')

@section('content')
  <div class="app">


    <main class="main-content">
      <!-- Dashboard view -->
      <section id="dashboard">
        <h1><i class="fas fa-users"></i> Total User</h1>
        <div class="summary-cards">
          <div class="summary-card">
            <div class="summary-text">
              <span>Total Employees</span>
              <h2 id="totalEmployeesCount">{{$totalEmployees}}</h2>
            </div>
            <div class="summary-icon">
              <i class="fas fa-user-check"></i>
            </div>
          </div>
          <div class="summary-card">
            <div class="summary-text">
              <span>Total Doctors</span>
              <h2 id="totalEmployeesCount">{{$totalDoctors}}</h2>
            </div>
            <div class="summary-icon">
              <i class="fas fa-user-md"></i>
            </div>
          </div>
          <div class="summary-card">
            <div class="summary-text">
              <span>Total Banner</span>
              <h2 id="totalEmployeesCount">{{$totalDoctors1}}</h2>
            </div>
            <div class="summary-icon">
              <i class="fas fa-user-check"></i>
            </div>
          </div>
        </div>

      </section>
    </main>
  </div>
 @endsection


