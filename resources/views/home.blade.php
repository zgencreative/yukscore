@extends('layouts.templates')

@section('extra_head')
<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"
/>
@endsection

@section('content')
<div class="container my-5">
  <div class="row">
    <!-- Sidebar Kiri -->
    <div class="col-md-3" id="main-search">
      <div
        class="text-light p-3 rounded"
        style="overflow-y: auto"
        id="leftSide"
      ></div>
    </div>

    <!-- Main Content Tengah -->
    <div class="col-md-6" id="main-div">
      <div class="py-3 rounded shadow-sm text-light">
        <!-- Calendar Header -->
        <div class="d-flex justify-content-between">
          <p class="mt-3 fw-bold" id="calendar-month"></p>
          <button class="btn text-light flatpickr" id="kt_datepicker_1">
            <img src="{{ asset('img/calendar.png') }}" />
          </button>
        </div>

        <!-- Calendar Grid -->
        <div class="calendar-container" id="calendar-grid"></div>

        <p class="mt-3"><b>LIVE</b> MATCHES</p>
        <div class="button-container" id="live"></div>

        <div class="scrollable-container"></div>
        <div class="d-flex justify-content-between">
          <div class="progress-dots">
            <button class="rounded-btn" id="prevBtn">
              <img src="{{ asset('img/arrow_left.png') }}" alt="Previous" />
            </button>
            <button class="rounded-btn" id="nextBtn">
              <img src="{{ asset('img/arrow_right.png') }}" alt="Next" />
            </button>
          </div>
          <p class="show-all" id="show-all-live">Show All</p>
        </div>

        <p class="mt-3"><b>UPCOMING</b> MATCHES</p>
        <div class="button-container" id="next"></div>

        <div class="scrollable-container"></div>
        <div class="d-flex justify-content-between">
          <div class="progress-dots">
            <button class="rounded-btn" id="prevBtn">
              <img src="{{ asset('img/arrow_left.png') }}" alt="Previous" />
            </button>
            <button class="rounded-btn" id="nextBtn">
              <img src="{{ asset('img/arrow_right.png') }}" alt="Next" />
            </button>
          </div>
          <p class="show-all" id="show-all-upcoming">Show All</p>
        </div>

        <p class="mt-3"><b>PREVIOUS</b> MATCHES</p>
        <div class="button-container" id="previous"></div>

        <div class="scrollable-container"></div>
        <div class="d-flex justify-content-between">
          <div class="progress-dots">
            <button class="rounded-btn" id="prevBtn">
              <img src="{{ asset('img/arrow_left.png') }}" alt="Previous" />
            </button>
            <button class="rounded-btn" id="nextBtn">
              <img src="{{ asset('img/arrow_right.png') }}" alt="Next" />
            </button>
          </div>
          <p class="show-all" id="show-all-previous">Show All</p>
        </div>
      </div>
    </div>

    <!-- Sidebar Kanan -->
    <div class="col-md-3" id="right-content">
      <!-- Optional sidebar kanan -->
    </div>
  </div>
</div>
@endsection

@section('extra_js')
<script>
  var currentPage = "{{ $page_name ?? 'home' }}";
</script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="{{asset("js/script.js")}}"></script>
@endsection
