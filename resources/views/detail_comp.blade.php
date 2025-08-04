@extends('layouts.templates')

@section('content')
<div class="container my-5">
  <div class="row">
    <!-- Sidebar Kiri -->
    <div class="col-md-3" id="main-search">
      <div class="text-light p-3 rounded" style="overflow-y: auto" id="leftSide"></div>
    </div>

    <!-- Main Content Tengah -->
    <div class="col-md-9" id="main-div">
      <div class="p-md-3 rounded shadow-sm text-light">
        <p>
          <a href="/" style="text-decoration: none">
            <i class="fas fa-arrow-left" style="color: white"></i>
          </a>
        </p>
        <div class="d-flex align-items-center custom-padding">
          <!-- Logo -->
          <div class="me-3">
            <img src="{{ $data['data']['badgeUrl'] }}" alt="{{ $data['data']['Scd'] }}" style="width: 30px; height: 30px" />
          </div>
          <!-- Text Content -->
          <div>
            <h5 class="m-0">{{ $data['data']['Snm'] }}</h5>
            <p class="m-0">{{ $data['data']['Cnm'] }}</p>
          </div>
        </div>

        <!-- Menu Section -->
        <div class="menu-bar">
          <div class="menu-items">
            <a href="#" id="overview-link" class="menu-link active">OVERVIEW</a>
            <p class="menu-link" id="matches-link">MATCHES</p>
            <p class="menu-link" id="table-link">TABLE</p>
          </div>
        </div>

        <!-- Dynamic Content Section -->
        <div class="container mt-4" id="dinamic-content"></div>
        <div class="container mt-4" id="dinamic-content-mobile"></div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('extra_js')
<script>
  const data = @json($data);
  var currentPage = "{{ $page_name }}";
</script>
@endsection
