@extends('layouts.templates')

@section('extra_head')
<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"
/>
<style>
    .match-card-alt {
        display: flex; /* Mengatur layout menjadi horizontal */
        background-color: #1e293b; /* Warna latar gelap sesuai tema Anda */
        border-radius: 8px;
        margin-bottom: 15px;
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        overflow: hidden; /* Penting untuk rounded corner di bagian ikon */
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .match-card-alt:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
    .match-card-alt .match-card-content {
        padding: 15px;
        flex-grow: 1; /* Konten mengambil sisa ruang */
    }
    .match-card-alt .match-name {
        color: #e2e8f0;
        font-size: 1rem;
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
    }
    .match-card-alt .countdown {
        color: #94a3b8;
        font-size: 0.9rem;
        font-weight: 500;
    }
    .match-card-alt .match-card-icon {
        flex-shrink: 0; /* Mencegah area ikon mengecil */
        width: 60px;
        background-color: #2563eb; /* Warna biru untuk area ikon */
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
</style>
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
    <div class="col-md-3 text-light" id="right-content">
        <h4>Segera Hadir</h4>
        
        <div class="match-card-list">
            @forelse ($upcomingMatches as $match)
                @if (!empty($match['id_match']))
                    <a href="{{ route('stream.play', ['matchId' => $match['id_match']]) }}" class="match-card-alt">
                        {{-- Area Konten Teks di Kiri --}}
                        <div class="match-card-content">
                            <span class="match-name">{{ Str::limit($match['match_name'], 30) }}</span>
                            <span class="countdown" data-starttime="{{ $match['start_time'] }}">
                                Memuat...
                            </span>
                        </div>
                        {{-- Area Ikon Berwarna di Kanan --}}
                        <div class="match-card-icon">
                            <i class="fas fa-play"></i> {{-- Contoh ikon Font Awesome --}}
                        </div>
                    </a>
                @else
                    <div class="match-card-alt" style="opacity: 0.6; cursor: not-allowed;">
                        <div class="match-card-content">
                            <span class="match-name">{{ Str::limit($match['match_name'], 30) }}</span>
                            <span class="countdown" data-starttime="{{ $match['start_time'] }}">
                                Link belum ada
                            </span>
                        </div>
                        <div class="match-card-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                @endif
            @empty
                <p class="text-light">Tidak ada pertandingan yang akan datang.</p>
            @endforelse
        </div>
    </div>
  </div>
</div>
@endsection

@section('extra_js')
<script>
  var currentPage = "{{ $page_name ?? 'home' }}";
</script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@endsection
