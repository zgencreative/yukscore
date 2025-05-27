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
              @php $scores = [0, 0]; @endphp

              @foreach ($events as $event)
                @if (!empty($event['score']))
                  @php $scores = $event['score']; @endphp
                @endif
                <div class="py-2 text-white {{ $event['team'] == 1 ? 'text-start' : 'text-end' }}">
                  <div class="col-12">
                    @if ($event['team'] == 1)
                      @if ($half != '4') <span>{{ $event['min'] }}'</span> @endif
                      <small>
                        @if (!empty($event['score']) && $event['player'][0]['info'] == 'goal' && count($event['player']) > 1)
                          ({{ $event['player'][1]['pn'] }})
                        @endif
                        <b>{{ $event['player'][0]['pn'] }}</b>
                      </small>
                      <span class="p-1">
                        @include('partials.event_icon', ['player' => $event['player'][0]])
                      </span>
                      @if (!empty($event['score']))
                        <center>{{ $scores[0] }} - {{ $scores[1] }}</center>
                      @endif
                    @else
                      @if (!empty($event['score']))
                        <center>{{ $scores[0] }} - {{ $scores[1] }}</center>
                      @endif
                      <span class="p-1">
                        @include('partials.event_icon', ['player' => $event['player'][0]])
                      </span>
                      <small>
                        <b>{{ $event['player'][0]['pn'] }}</b>
                        @if (!empty($event['score']) && $event['player'][0]['info'] == 'goal' && count($event['player']) > 1)
                          ({{ $event['player'][1]['pn'] }})
                        @endif
                      </small>
                      @if ($half != '4')
                        @if ($event['minex'] != 0)
                          <span>{{ $event['min'] }}+{{ $event['minex'] }}'</span>
                        @else
                          <span>{{ $event['min'] }}'</span>
                        @endif
                      @endif
                    @endif
                  </div>
                </div>
              @endforeach

              <!-- Penutup Babak -->
              <div class="p-2 d-flex justify-content-between bg-secondary rounded text-white">
                <span>
                  @switch($half)
                    @case('1') HT @break
                    @case('2') FT @break
                    @case('3') AET @break
                    @case('4') PEN @break
                    @default {{ $half }}th Half
                  @endswitch
                </span>
                <span>{{ $scores[0] }} - {{ $scores[1] }}</span>
              </div>
            @endforeach
          @else
            <div class="info">
              <div class="card bg-black border-2 border-secondary-subtle">
                <div class="card-body">
                  <h4 class="text-center fw-semibold">No Data</h4>
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
<script src="{{asset("js/script.js")}}"></script>
@endsection
