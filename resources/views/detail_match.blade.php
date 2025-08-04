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

        @php
          $teks1 = '';
          $teks2 = '';
          if (!empty($data['data']['Score1'])) {
              $teks1 = $data['data']['time_start'];
              $teks2 = $data['data']['Score1'] . ' - ' . $data['data']['Score2'];
          } else {
              [$teks1, $teks2] = explode(' ', $data['data']['time_start']);
          }
        @endphp

        <div class="background-container">
          <div class="match-details">
            <div class="match-info-detail">
              <div class="match-date">{{ $teks1 }}</div>
              <div class="match-time-wrapper">
                <div class="teams-container">
                  <div class="d-flex align-items-center justify-content-center">
                    <div class="team-info" id="team1-info">
                      <img loading="lazy" src="{{ $data['data']['Team1']['IMGTeam'] }}"
                           alt="{{ $data['data']['Team1']['NMTeam'] }}"
                           class="team-badge" style="width: 50px; height: 50px"/>
                      <div class="team-nameMatch">{{ $data['data']['Team1']['NMTeam'] }}</div>
                    </div>
                    <div class="match-time">{{ $teks2 }}</div>
                    <div class="team-info" id="team2-info">
                      <img loading="lazy" src="{{ $data['data']['Team2']['IMGTeam'] }}"
                           alt="{{ $data['data']['Team2']['NMTeam'] }}"
                           class="team-badge"/>
                      <div class="team-nameMatch">{{ $data['data']['Team2']['NMTeam'] }}</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="detail-status">{{ $data['data']['Status_Match'] }}</div>
            </div>
          </div>
        </div>

        <!-- Menu Section -->
        <div class="menu-bar">
          <div class="menu-items">
            <p class="menu-link" id="info-link">INFO</p>
            <a href="#" id="summary-link" class="menu-link active">SUMMARY</a>
            <p class="menu-link" id="stats-link">STATS</p>
            <p class="menu-link" id="lineups-link">LINE-UPS</p>
            <p class="menu-link" id="table-link">TABLE</p>
            <p class="menu-link" id="news-link">NEWS</p>
          </div>
        </div>

        <!-- Dynamic Content Section -->
        <div class="container mt-4" id="dinamic-content">
            @if (!empty($data['data']['timeline']))
                @foreach ($data['data']['timeline'] as $half => $events)
                    
                    {{-- Inisialisasi variabel untuk menyimpan skor terakhir yang valid di setiap babak --}}
                    @php $last_valid_scores = [0, 0]; @endphp

                    @foreach ($events as $event)
                        
                        {{-- Perbarui skor terakhir HANYA JIKA skor baru yang valid ditemukan --}}
                        @if (isset($event['score'][1])) {{-- Pengecekan yang lebih aman --}}
                            @php $last_valid_scores = $event['score']; @endphp
                        @endif

                        {{-- Wadah utama dengan class 'event-row' yang menggunakan Flexbox --}}
                        <div class="event-row">

                            {{-- KOLOM KIRI: Akan diisi jika pencetak gol dari tim 1 --}}
                            <div class="event-left">
                                @if ($event['team'] == 1)
                                    @if ($half != '4') <span class="me-2">{{ $event['min'] }}'</span> @endif
                                    <span class="p-1">
                                        @include('partials.event_icon', ['player' => $event['player'][0]])
                                    </span>
                                    <small>
                                        <b>{{ $event['player'][0]['pn'] }}</b>
                                        {{-- Gunakan pengecekan yang sama di sini --}}
                                        @if (isset($event['score'][1]) && $event['player'][0]['info'] == 'goal' && count($event['player']) > 1)
                                            ({{ $event['player'][1]['pn'] }})
                                        @endif
                                    </small>
                                @endif
                            </div>

                            {{-- KOLOM TENGAH: Hanya tampilkan skor jika event ini adalah gol dengan data skor yang valid --}}
                            <div class="event-center">
                                @if (isset($event['score'][1])) {{-- Pengecekan yang lebih aman --}}
                                    <span>{{ $event['score'][0] }} - {{ $event['score'][1] }}</span>
                                @endif
                            </div>

                            {{-- KOLOM KANAN: Akan diisi jika pencetak gol dari tim 2 --}}
                            <div class="event-right">
                                @if ($event['team'] != 1)
                                    <small>
                                        {{-- Gunakan pengecekan yang sama di sini --}}
                                        @if (isset($event['score'][1]) && $event['player'][0]['info'] == 'goal' && count($event['player']) > 1)
                                            ({{ $event['player'][1]['pn'] }})
                                        @endif
                                        <b>{{ $event['player'][0]['pn'] }}</b>
                                    </small>
                                    <span class="p-1">
                                        @include('partials.event_icon', ['player' => $event['player'][0]])
                                    </span>
                                    @if ($half != '4')
                                        @if ($event['minex'] != 0)
                                            <span class="ms-2">{{ $event['min'] }}+{{ $event['minex'] }}'</span>
                                        @else
                                            <span class="ms-2">{{ $event['min'] }}'</span>
                                        @endif
                                    @endif
                                @endif
                            </div>
                            
                        </div>
                    @endforeach

                    {{-- Penutup Babak (Menggunakan skor valid terakhir) --}}
                    <div class="p-2 d-flex justify-content-between bg-secondary rounded text-white mt-3">
                        <span>
                            @switch($half)
                                @case('1') Half Time @break
                                @case('2') Full Time @break
                                @case('3') After Extra Time @break
                                @case('4') Penalties @break
                                @default {{ $half }}th Half
                            @endswitch
                        </span>
                        {{-- Variabel $last_valid_scores dijamin aman karena diinisialisasi sebagai [0,0] --}}
                        <span>{{ $last_valid_scores[0] }} - {{ $last_valid_scores[1] }}</span>
                    </div>

                @endforeach
            @else
                <div class="info mt-3">
                    <div class="card bg-dark border-secondary">
                        <div class="card-body">
                            <h4 class="text-center fw-semibold text-white-50">No Timeline Data Available</h4>
                        </div>
                    </div>
                </div>
            @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('extra_js')
<script>
  var currentPage = "{{ $page_name }}";
</script>
@endsection
