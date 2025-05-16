<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use DateTimeZone;

class ApiController extends Controller
{
    public function detailCountry($country)
    {
        $img_badge = 'https://static.lsmedia1.com/competition/high/';
        $img_team = 'https://lsm-static-prod.lsmedia1.com/medium/';
        $url = "https://prod-cdn-public-api.lsmedia1.com/v1/api/app/category-s/soccer/{$country}?locale=ID";

        try {
            $response = Http::acceptJson()->get($url);

            if (!$response->successful()) {
                return response()->json([
                    'code' => $response->status(),
                    'message' => 'Error fetching data from source',
                ], $response->status());
            }

            $res = $response->json();
            $data = [];

            if (isset($res['Stages'])) {
                foreach ($res['Stages'] as $stage) {
                    $badge_url = isset($stage['badgeUrl'])
                        ? $img_badge . $stage['badgeUrl']
                        : "https://static.lsmedia1.com/i2/fh/" . ($stage['Ccd'] ?? '') . ".jpg";

                    $existing_snm = array_column($data, 'CompN');

                    if (!in_array($stage['CompN'] ?? '', $existing_snm)) {
                        $data[] = [
                            'Sid'     => $stage['Sid'] ?? '',
                            'Snm'     => $stage['Snm'] ?? '',
                            'Scd'     => $stage['Scd'] ?? '',
                            'Cnm'     => $stage['Cnm'] ?? '',
                            'badgeUrl'=> $badge_url,
                            'urlComp' => ($stage['Ccd'] ?? '') . '.' . ($stage['Scd'] ?? ''),
                            'CompId'  => $stage['CompId'] ?? '',
                            'CompN'   => $stage['CompN'] ?? '',
                            'CompST'  => $stage['CompST'] ?? '',
                        ];
                    }
                }
            }

            return response()->json([
                'code' => 200,
                'message' => 'success',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Error fetching data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function footballByDate($date)
    {
        $img_badge = 'https://static.lsmedia1.com/competition/high/';
        $img_team = 'https://lsm-static-prod.lsmedia1.com/medium/';
        $default_img = url('img/default_team.png');
        $default_badge = url('img/friendlist.jpg');

        $url = "https://prod-cdn-public-api.lsmedia1.com/v1/api/app/date/soccer/{$date}/7?countryCode=ID&locale=en&MD=1";

        try {
            $response = Http::get($url);

            if (!$response->successful()) {
                return response()->json([
                    'code' => $response->status(),
                    'message' => 'Failed to fetch data from API',
                    'data' => [],
                ], $response->status());
            }

            $data = $response->json();

            $response_data = [
                'code' => 200,
                'message' => 'success',
                'data' => []
            ];

            foreach ($data['Stages'] ?? [] as $stage_data) {
                $badge_url = (str_contains($stage_data['Snm'] ?? '', 'Friendlies'))
                    ? $default_badge
                    : (
                        !empty($stage_data['badgeUrl'])
                            ? $img_badge . $stage_data['badgeUrl']
                            : "https://static.lsmedia1.com/i2/fh/" . ($stage_data['Ccd'] ?? '') . ".jpg"
                    );

                $stage = [
                    "Sid"     => $stage_data['Sid'] ?? null,
                    "Snm"     => $stage_data['Snm'] ?? null,
                    "Scd"     => $stage_data['Scd'] ?? null,
                    "Cnm"     => $stage_data['Cnm'] ?? null,
                    "CnmT"    => $stage_data['CnmT'] ?? null,
                    "Csnm"    => $stage_data['Csnm'] ?? '',
                    "Ccd"     => $stage_data['Ccd'] ?? null,
                    "CompID"  => $stage_data['CompId'] ?? '',
                    "CompN"   => $stage_data['CompN'] ?? '',
                    "urlComp" => ($stage_data['CnmT'] ?? '') . '.' . ($stage_data['Scd'] ?? ''),
                    "badgeUrl"=> $badge_url,
                    "Events"  => []
                ];

                foreach ($stage_data['Events'] ?? [] as $event_data) {
                    $team1_img = $event_data['T1'][0]['Img'] ?? null;
                    $team2_img = $event_data['T2'][0]['Img'] ?? null;

                    $event = [
                        'IDMatch'     => $event_data['Eid'] ?? null,
                        'Team1'       => [
                            'NMTeam' => $event_data['T1'][0]['Nm'] ?? '',
                            'IDTeam' => $event_data['T1'][0]['ID'] ?? '',
                            'IMGTeam'=> $team1_img ? $img_team . $team1_img : $default_img,
                        ],
                        'Team2'       => [
                            'NMTeam' => $event_data['T2'][0]['Nm'] ?? '',
                            'IDTeam' => $event_data['T2'][0]['ID'] ?? '',
                            'IMGTeam'=> $team2_img ? $img_team . $team2_img : $default_img,
                        ],
                        'Status_Match'=> $event_data['Eps'] ?? '',
                        'time_start'  => $event_data['Esd'] ?? ''
                    ];

                    if (!in_array($event_data['Eps'] ?? '', ['NS', 'Canc.', 'Postp.'])) {
                        $event['Score1'] = $event_data['Tr1'] ?? null;
                        $event['Score2'] = $event_data['Tr2'] ?? null;
                    }

                    $stage['Events'][] = $event;
                }

                $response_data['data'][] = $stage;
            }

            return $response_data;

        } catch (\Exception $e) {
            return [
                'code' => 500,
                'message' => 'Failed to fetch data: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    public function sortedData($date = null)
    {
        if (!$date) {
            $date = now()->format('Ymd');
        }

        // Panggil method footballByDate namun sebagai fungsi, bukan response JSON
        $response = $this->footballByDate($date);

        if ($response['code'] !== 200) {
            return response()->json($response, 500);
        }

        $index_data = $response;
        $liga_tertentu = [
            '18173', '18418', '18176', '18420', '18227', '20106', '18483',
            '18546', '17004', '19232', '19243', '19244', '20222', '18181', '18599',
            '18307', '20510', '18213', '20202', '18179', '18822'
        ];

        $sorted_data = [
            'live'     => [],
            'previous' => [],
            'next'     => []
        ];

        foreach ($index_data['data'] as $match) {
            $events = $match['Events'] ?? [];

            if (!in_array($match['Sid'], $liga_tertentu)) {
                continue;
            }

            $live_events = [];
            $previous_events = [];
            $next_events = [];

            foreach ($events as $event) {
                $status = $event['Status_Match'] ?? '';
                if (in_array($status, ['FT', 'AP', 'AET', 'Aband.', 'AW'])) {
                    $previous_events[] = $event;
                } elseif (in_array($status, ['NS', 'Postp.', 'Canc.'])) {
                    $next_events[] = $event;
                } else {
                    $live_events[] = $event;
                }
            }

            $match_data = [
                'Sid'      => $match['Sid'] ?? '',
                'Snm'      => $match['Sds'] ?? $match['Snm'] ?? '',
                'Scd'      => $match['Scd'] ?? '',
                'Cnm'      => $match['Cnm'] ?? '',
                'CnmT'     => $match['CnmT'] ?? '',
                'Csnm'     => $match['Csnm'] ?? '',
                'Ccd'      => $match['Ccd'] ?? '',
                'CompID'   => $match['CompID'] ?? '',
                'CompN'    => $match['CompN'] ?? '',
                'urlComp'  => ($match['CnmT'] ?? '') . '/' . ($match['Scd'] ?? ''),
                'badgeUrl' => $match['badgeUrl'] ?? '',
            ];

            if (!empty($live_events)) {
                $match_data['events'] = $live_events;
                $sorted_data['live'][] = $match_data;
            }

            if (!empty($previous_events)) {
                $match_data['events'] = $previous_events;
                $sorted_data['previous'][] = $match_data;
            }

            if (!empty($next_events)) {
                $match_data['events'] = $next_events;
                $sorted_data['next'][] = $match_data;
            }
        }

        return response()->json($sorted_data);
    }

    public function search($keyword = null)
    {
        $img_badge = 'https://static.lsmedia1.com/competition/high/';
        $img_team = 'https://lsm-static-prod.lsmedia1.com/medium/';
        $default_team_img = asset('img/default_team.png');
        $default_badge_img = asset('img/friendlist.jpg');

        $response_data = [
            'code' => 200,
            'message' => 'success',
            'data' => []
        ];

        try {
            $url = $keyword
                ? "https://prod-cdn-search-api.lsmedia1.com/api/v2/search/soccer?query={$keyword}&limit=10&locale=ID&teams=true&stages=true&categories=true&countryCode=ID"
                : "https://prod-cdn-search-api.lsmedia1.com/api/v2/search/soccer?locale=ID&limit=5&teams=true&stages=true&categories=true&countryCode=ID";

            $res = Http::get($url)->json();

            $data = [
                'teams' => [],
                'stages' => [],
                'players' => [],
                'categories' => [],
            ];

            // Teams
            foreach ($res['Teams'] ?? [] as $team) {
                $data['teams'][] = [
                    'IDTeam'  => $team['ID'] ?? '',
                    'NMTeam'  => $team['Nm'] ?? '',
                    'IMGTeam' => isset($team['Img']) ? $img_team . $team['Img'] : $default_team_img,
                    'CoNm'    => $team['CoNm'] ?? '',
                    'CoId'    => $team['CoId'] ?? '',
                    'Abr'     => $team['Abr'] ?? '',
                ];
            }

            // Stages
            foreach ($res['Stages'] ?? [] as $stage) {
                $badge_url = str_contains($stage['Cnm'] ?? '', 'Friendlies')
                    ? $default_badge_img
                    : (
                        !empty($stage['badgeUrl'])
                            ? $img_badge . $stage['badgeUrl']
                            : "https://static.lsmedia1.com/i2/fh/" . ($stage['Ccd'] ?? '') . ".jpg"
                    );

                $data['stages'][] = [
                    'Sid'      => $stage['Sid'] ?? '',
                    'Snm'      => $stage['Sds'] ?? $stage['Snm'] ?? '',
                    'Scd'      => $stage['Scd'] ?? '',
                    'Cnm'      => $stage['Cnm'] ?? '',
                    'badgeUrl' => $badge_url,
                    'urlComp'  => ($stage['Ccd'] ?? '') . '.' . ($stage['Scd'] ?? ''),
                    'CompId'   => $stage['CompId'] ?? '',
                    'CompN'    => $stage['CompN'] ?? '',
                    'CompST'   => $stage['CompST'] ?? '',
                ];
            }

            // Categories
            foreach ($res['Categories'] ?? [] as $category) {
                $badge_url = str_contains($category['Cnm'] ?? '', 'Friendlies')
                    ? $default_badge_img
                    : (
                        !empty($category['badgeUrl'])
                            ? $img_badge . $category['badgeUrl']
                            : "https://static.lsmedia1.com/i2/fh/" . ($category['Ccd'] ?? '') . ".jpg"
                    );

                $data['categories'][] = [
                    'Cid'      => $category['Cid'] ?? '',
                    'Cnm'      => $category['Cnm'] ?? '',
                    'badgeUrl' => $badge_url,
                    'Ccd'      => $category['Ccd'] ?? '',
                    'CompId'   => $category['CompId'] ?? '',
                ];
            }

            $response_data['data'] = $data;
            return response()->json($response_data);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Failed to fetch data: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function getDetailMatch($idMatch)
    {
        $response = [
            'code' => 200,
            'message' => 'success',
            'data' => []
        ];

        $summary_url = "https://prod-cdn-public-api.lsmedia1.com/v1/api/app/incidents/soccer/{$idMatch}";
        $match_url = "https://prod-cdn-public-api.lsmedia1.com/v1/api/app/scoreboard/soccer/{$idMatch}?locale=ID";
        $img_team = 'https://lsm-static-prod.lsmedia1.com/medium/';
        $default_img = asset('img/default_team.png');

        try {
            $match_data = Http::get($match_url)->json();

            $datas = [
                'IDMatch' => $match_data['Eid'],
                'Score1' => $match_data['Tr1'] ?? '',
                'Score2' => $match_data['Tr2'] ?? '',
                'Team1' => [
                    'NMTeam' => $match_data['T1'][0]['Nm'],
                    'IDTeam' => $match_data['T1'][0]['ID'],
                    'IMGTeam' => isset($match_data['T1'][0]['Img']) ? $img_team . $match_data['T1'][0]['Img'] : $default_img,
                ],
                'Team2' => [
                    'NMTeam' => $match_data['T2'][0]['Nm'],
                    'IDTeam' => $match_data['T2'][0]['ID'],
                    'IMGTeam' => isset($match_data['T2'][0]['Img']) ? $img_team . $match_data['T2'][0]['Img'] : $default_img,
                ],
                'Status_Match' => $match_data['Eps'],
                'time_start' => $match_data['Esd'],
                'timeline' => []
            ];

            $summary_data = Http::get($summary_url)->json();

            $not_started = ['NS', 'Canc.', 'Postp.'];

            if (!in_array($match_data['Eps'], $not_started) && isset($summary_data['Incs'])) {
                $keys = array_map('intval', array_keys($summary_data['Incs']));
                $max_key = max($keys);

                for ($i = 1; $i <= $max_key; $i++) {
                    $datas['timeline']["{$i}"] = [];
                }

                foreach ($summary_data['Incs'] as $key => $incidents) {
                    foreach ($incidents as $incident) {
                        $timeline_entry = [
                            'team' => $incident['Nm'],
                            'min' => in_array($key, ["1", "2", "3"]) ? $incident['Min'] : null,
                            'minex' => $incident['MinEx'] ?? 0,
                            'score' => $incident['Sc'] ?? [],
                            'player' => []
                        ];

                        // Ada pemain dalam satu incident (multi-player)
                        if (!empty($incident['Incs'])) {
                            foreach ($incident['Incs'] as $idx => $player_incident) {
                                $info = self::parseIncidentType($incident);
                                $timeline_entry['player'][] = [
                                    'status' => $idx == 0 ? 'goal' : (isset($player_incident['Sc']) ? 'assist' : 'foul'),
                                    'info' => $info,
                                    'IDPlayer' => $player_incident['ID'],
                                    'fn' => $player_incident['Fn'] ?? '',
                                    'ln' => $player_incident['Ln'] ?? '',
                                    'pn' => $player_incident['Pn'] ?? '',
                                ];
                            }
                        } else {
                            $info = self::parseIncidentType($incident);
                            $timeline_entry['player'][] = [
                                'status' => isset($incident['Sc']) ? 'goal' : 'foul',
                                'info' => $info,
                                'IDPlayer' => $incident['ID'] ?? '',
                                'fn' => $incident['Fn'] ?? '',
                                'ln' => $incident['Ln'] ?? '',
                                'pn' => $incident['Pn'] ?? '',
                            ];
                        }

                        $datas['timeline'][$key][] = $timeline_entry;
                    }
                }
            }

            $response['data'] = $datas;
            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Error fetching data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private static function parseIncidentType($incident)
    {
        if (!empty($incident['IR']) && str_contains($incident['IR'], 'changed_to_red')) {
            return 'red-var';
        } elseif (!empty($incident['IR']) && str_contains($incident['IR'], 'changed_to_yellow')) {
            return 'yellow-var';
        } elseif (!empty($incident['IR']) && str_contains($incident['IR'], 'disallowed_offside')) {
            return 'no-goal-var';
        } else {
            return match ($incident['IT'] ?? 0) {
                63 => 'assists',
                62 => $incident['IR'] ?? 'unknown',
                40 => 'pen-no-goal',
                41, 37 => 'pen-goal',
                38 => 'pen-no-goal',
                43 => 'yellow card',
                44 => 'yellow-red card',
                45 => 'red card',
                39 => 'penalty',
                default => 'goal',
            };
        }
    }

    public function getMatchStat($idMatch)
    {
        $response = [
            'code' => 200,
            'message' => 'success',
            'data' => []
        ];

        $url = "https://prod-cdn-public-api.lsmedia1.com/v1/api/app/statistics/soccer/{$idMatch}";

        try {
            $res_data = Http::get($url)->json();

            if (empty($res_data)) {
                return response()->json($response);
            }

            $sum_stat = [];
            foreach ($res_data['Stat'] ?? [] as $stat) {
                $sum_stat[] = [
                    'shon' => $stat['Shon'] ?? null,
                    'shof' => $stat['Shof'] ?? null,
                    'blsh' => $stat['Shbl'] ?? null,
                    'pss'  => $stat['Pss'] ?? null,
                    'crs'  => $stat['Cos'] ?? null,
                    'ofs'  => $stat['Ofs'] ?? null,
                    'fls'  => $stat['Fls'] ?? null,
                    'ths'  => $stat['Ths'] ?? null,
                    'ycs'  => $stat['Ycs'] ?? null,
                    'catt' => $stat['Att'] ?? null,
                    'gks'  => $stat['Gks'] ?? null,
                    'goa'  => $stat['Goa'] ?? null,
                    'rcs'  => $stat['Rcs'] ?? null,
                ];
            }

            $response['data'][] = [
                'IDMatch' => $res_data['Eid'] ?? null,
                'sum_stat' => $sum_stat
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Error fetching data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getMatchLineup($idMatch)
    {
        $url = "https://prod-cdn-public-api.lsmedia1.com/v1/api/app/lineups/soccer/{$idMatch}";

        try {
            $res = Http::get($url)->json();

            if (empty($res)) {
                return response()->json([
                    'code' => 200,
                    'message' => 'success',
                    'data' => []
                ]);
            }

            $team1 = $res['Lu'][0] ?? [];
            $team2 = $res['Lu'][1] ?? [];

            $data = [
                'MatchID' => $res['Eid'] ?? null,
                'Team1' => [
                    'Pos' => [],
                    'IS' => [],
                    'Fo' => $team1['Fo'] ?? '',
                    'Subs' => []
                ],
                'Team2' => [
                    'Pos' => [],
                    'IS' => [],
                    'Fo' => $team2['Fo'] ?? '',
                    'Subs' => []
                ]
            ];

            // Posisi pemain Team 1 dan 2
            foreach ($team1['Ps'] ?? [] as $player) {
                $data['Team1']['Pos'][] = $this->mapPlayerData($player);
            }

            foreach ($team2['Ps'] ?? [] as $player) {
                $data['Team2']['Pos'][] = $this->mapPlayerData($player);
            }

            // Injured/Suspended
            foreach ($team1['IS'] ?? [] as $player) {
                $data['Team1']['IS'][] = $this->mapInjuredSuspendedData($player);
            }

            foreach ($team2['IS'] ?? [] as $player) {
                $data['Team2']['IS'][] = $this->mapInjuredSuspendedData($player);
            }

            // Substitutions
            $this->processSubstitutions($res['Subs']['1'] ?? [], $data['Team1'], $data['Team2']);
            $this->processSubstitutions($res['Subs']['2'] ?? [], $data['Team1'], $data['Team2']);

            return response()->json([
                'code' => 200,
                'message' => 'success',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'error',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    private function mapPlayerData($player)
    {
        return [
            'PlayerID' => $player['Pid'] ?? null,
            'Fn' => $player['Fn'] ?? '',
            'Ln' => $player['Ln'] ?? '',
            'Pn' => $player['Pn'] ?? '',
            'Np' => $player['Snu'] ?? '',
            'Pon' => $player['Pon'] ?? '',
            'Fp' => $player['Fp'] ?? '',
            'Rate' => $player['Rate'] ?? ''
        ];
    }

    private function mapInjuredSuspendedData($player)
    {
        return [
            'PlayerID' => $player['Pid'] ?? null,
            'Fn' => $player['Fn'] ?? '',
            'Ln' => $player['Ln'] ?? '',
            'Pn' => $player['Pnt'] ?? '',
            'Pon' => $player['Pon'] ?? '',
            'Rs' => $player['Rs'] ?? '',
            'Rton' => $player['RtonS'] ?? ''
        ];
    }

    private function processSubstitutions($subs, &$team1, &$team2)
    {
        foreach ($subs as $sub) {
            $playerData = [
                'PlayerID' => $sub['ID'] ?? null,
                'Fn' => $sub['Fn'] ?? '',
                'Ln' => $sub['Ln'] ?? '',
                'Pn' => $sub['Pn'] ?? '',
                'Min' => $sub['Min'] ?? null
            ];

            if (($sub['Nm'] ?? null) == 1) {
                $team1['Subs'][] = $playerData;
            } else {
                $team2['Subs'][] = $playerData;
            }
        }
    }

    public function getLeagueTable($urlComp)
    {
        $modifiedUrl = str_replace('.', '/', $urlComp);
        $url = "https://prod-cdn-public-api.lsmedia1.com/v1/api/app/leagueTable-s/soccer/{$modifiedUrl}?locale=ID";
        $imgBadge = 'https://static.lsmedia1.com/competition/high/';
        $imgTeam = 'https://lsm-static-prod.lsmedia1.com/medium/';

        try {
            $response = Http::get($url);

            if (!$response->ok()) {
                return response()->json([
                    'code' => $response->status(),
                    'message' => 'Failed to fetch data',
                    'data' => []
                ]);
            }

            $res = $response->json();

            $data = [
                'Sid' => $res['Sid'] ?? '',
                'Snm' => $res['Snm'] ?? '',
                'Scd' => $res['Scd'] ?? '',
                'Cnm' => $res['Cnm'] ?? '',
                'badgeUrl' => $imgBadge . ($res['badgeUrl'] ?? ''),
                'CompId' => $res['CompId'] ?? '',
                'CompN' => $res['CompN'] ?? '',
                'CompST' => $res['CompST'] ?? '',
                'LeagueTable' => []
            ];

            if (!empty($res['LeagueTable']['L'][0]['Tables'][0]['team'])) {
                foreach ($res['LeagueTable']['L'][0]['Tables'][0]['team'] as $team) {
                    $data['LeagueTable'][] = [
                        'rank' => $team['rnk'] ?? '',
                        'teamID' => $team['Tid'] ?? '',
                        'teamNM' => $team['Tnm'] ?? '',
                        'teamIMG' => $imgTeam . ($team['Img'] ?? ''),
                        'p' => $team['pld'] ?? '',
                        'gd' => $team['gd'] ?? '',
                        'pts' => $team['pts'] ?? '',
                        'w' => $team['win'] ?? '',
                        'd' => $team['drw'] ?? '',
                        'l' => $team['lst'] ?? '',
                        'f' => $team['gf'] ?? '',
                        'a' => $team['ga'] ?? '',
                    ];
                }
            }

            return response()->json([
                'code' => 200,
                'message' => 'success',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal server error',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    public function getMatchNews($idMatch)
    {
        $response = [
            'code' => 200,
            'message' => 'success',
            'data' => []
        ];

        $matchUrl = "https://prod-cdn-public-api.lsmedia1.com/v1/api/app/scoreboard/soccer/{$idMatch}?locale=ID";
        $key = 'JDJhJDEyJFVLNUJ3d2c1MnFCaktnU0w0dzJMNU9GWEJQT0FaSTcwa0ZZUWpIbXF0YVRNMEU3aHZwQkF1';

        try {
            // Ambil data pertandingan
            $matchResponse = Http::get($matchUrl);
            if (!$matchResponse->ok()) {
                return response()->json([
                    'code' => $matchResponse->status(),
                    'message' => 'Failed to fetch match data',
                    'data' => []
                ]);
            }

            $matchData = $matchResponse->json();

            $team1 = strtolower(str_replace(' ', '-', $matchData['T1'][0]['Nm'] ?? ''));
            $team2 = strtolower(str_replace(' ', '-', $matchData['T2'][0]['Nm'] ?? ''));
            $slug = "{$team1},{$team2}";

            $apiUrl = "https://mansionsportsfc.com/api/news/tag/more?key={$key}&slug={$slug}";
            $siteUrl = "https://mansionsportsfc.com/";

            $newsResponse = Http::get($apiUrl);
            $newsData = $newsResponse->json();

            $datas = [
                'URL' => '',
                'News' => '',
            ];

            if (($newsData['status'] ?? 400) != 400) {
                $datas = [
                    'URL' => $siteUrl,
                    'News' => $newsData['news'] ?? [],
                ];
            }

            $response['data'] = $datas;
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Error fetching data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCompetitionDetail($urlComp)
    {
        $response = [
            'code' => 200,
            'message' => 'success',
            'data' => []
        ];

        $modifiedUrl = str_replace('.', '/', $urlComp);
        $urlCompFull = "https://prod-cdn-public-api.lsmedia1.com/v1/api/app/stage/soccer/{$modifiedUrl}/7?countryCode=ID&locale=en&MD=1";
        $imgTeam = 'https://lsm-static-prod.lsmedia1.com/medium/';
        $imgBadge = 'https://static.lsmedia1.com/competition/high/';

        try {
            $res = Http::get($urlCompFull);

            if (!$res->ok()) {
                return response()->json([
                    'code' => $res->status(),
                    'message' => 'Failed to fetch competition data',
                    'data' => []
                ]);
            }

            $jsonData = $res->json();
            $stages = $jsonData['Stages'][0] ?? [];

            $data = [
                'Sid' => $stages['Sid'] ?? '',
                'Snm' => $stages['Snm'] ?? '',
                'Scd' => $stages['Scd'] ?? '',
                'Cnm' => $stages['Cnm'] ?? '',
                'badgeUrl' => $imgBadge . ($stages['badgeUrl'] ?? ''),
                'CompId' => $stages['CompId'] ?? '',
                'CompN' => $stages['CompN'] ?? '',
                'CompST' => $stages['CompST'] ?? '',
                'urlComp' => $modifiedUrl,
                'Events' => [],
                'LeagueTable' => []
            ];

            foreach ($stages['Events'] ?? [] as $eventData) {
                $event = [
                    'IDMatch' => $eventData['Eid'],
                    'Team1' => [
                        'NMTeam' => $eventData['T1'][0]['Nm'] ?? '',
                        'IDTeam' => $eventData['T1'][0]['ID'] ?? '',
                        'IMGTeam' => $imgTeam . ($eventData['T1'][0]['Img'] ?? '')
                    ],
                    'Team2' => [
                        'NMTeam' => $eventData['T2'][0]['Nm'] ?? '',
                        'IDTeam' => $eventData['T2'][0]['ID'] ?? '',
                        'IMGTeam' => $imgTeam . ($eventData['T2'][0]['Img'] ?? '')
                    ],
                    'Status_Match' => $eventData['Eps'],
                    'time_start' => $eventData['Esd']
                ];

                if (!in_array($eventData['Eps'], ['NS', 'Canc.', 'Postp.'])) {
                    $event['Score1'] = $eventData['Tr1'] ?? null;
                    $event['Score2'] = $eventData['Tr2'] ?? null;
                }

                $data['Events'][] = $event;
            }

            if (!empty($stages['LeagueTable']['L'][0]['Tables'][0]['team'])) {
                foreach ($stages['LeagueTable']['L'][0]['Tables'][0]['team'] as $team) {
                    $data['LeagueTable'][] = [
                        'rank' => $team['rnk'] ?? '',
                        'teamID' => $team['Tid'] ?? '',
                        'teamNM' => $team['Tnm'] ?? '',
                        'teamIMG' => $imgTeam . ($team['Img'] ?? ''),
                        'p' => $team['pld'] ?? '',
                        'gd' => $team['gd'] ?? '',
                        'pts' => $team['pts'] ?? '',
                        'w' => $team['win'] ?? '',
                        'd' => $team['drw'] ?? '',
                        'l' => $team['lst'] ?? '',
                        'f' => $team['gf'] ?? '',
                        'a' => $team['ga'] ?? ''
                    ];
                }
            }

            $response['data'] = $data;
            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Error fetching data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getTeamDetail($idTeam)
{
    $imgTeam = 'https://lsm-static-prod.lsmedia1.com/medium/';
    $url = "https://prod-cdn-team-api.lsmedia1.com/v1/api/app/team/{$idTeam}/details?locale=ID&MD=1";
    $urlNext = "https://prod-cdn-team-api.lsmedia1.com/v1/api/app/team/{$idTeam}/nextevent?locale=ID&MD=1";

    try {
        $res = Http::get($url);
        $resNext = Http::get($urlNext);

        if (!$res->ok() || !$resNext->ok()) {
            return response()->json([
                'code' => 500,
                'message' => 'Failed to fetch team data'
            ]);
        }

        $resData = $res->json();
        $resNextData = $resNext->json();

        $data = [
            'NMTeam' => $resData['Nm'] ?? '',
            'IDTeam' => $resData['ID'] ?? '',
            'IMGTeam' => isset($resData['Img']) ? $imgTeam . $resData['Img'] : '/img/default_team.png',
            'Abr' => $resData['Abr'] ?? '',
            'CoNm' => $resData['CoNm'] ?? '',
            'CoId' => $resData['CoId'] ?? '',
            'NextEvent' => [],
            'Stages' => []
        ];

        foreach ($resData['Stages'] ?? [] as $stage) {
            $stageData = [
                'Sid' => $stage['Sid'] ?? '',
                'Snm' => $stage['Snm'] ?? '',
                'Scd' => $stage['Scd'] ?? '',
                'Cid' => $stage['Cid'] ?? '',
                'Cnm' => $stage['Cnm'] ?? '',
                'CnmT' => $stage['CnmT'] ?? '',
                'Csnm' => $stage['Csnm'] ?? '',
                'Ccd' => $stage['Ccd'] ?? '',
                'urlComp' => "{$stage['CnmT']}.{$stage['Scd']}",
                'events' => []
            ];

            foreach ($stage['Events'] ?? [] as $event) {
                $eventData = [
                    'IDMatch' => $event['Eid'],
                    'Status_Match' => $event['Eps'],
                    'time_start' => $event['Esd'],
                    'Team1' => [
                        'NMTeam' => $event['T1'][0]['Nm'],
                        'IDTeam' => $event['T1'][0]['ID'],
                        'IMGTeam' => isset($event['T1'][0]['Img']) ? $imgTeam . $event['T1'][0]['Img'] : '/img/default_team.png',
                        'Abr' => $event['T1'][0]['Abr']
                    ],
                    'Team2' => [
                        'NMTeam' => $event['T2'][0]['Nm'],
                        'IDTeam' => $event['T2'][0]['ID'],
                        'IMGTeam' => isset($event['T2'][0]['Img']) ? $imgTeam . $event['T2'][0]['Img'] : '/img/default_team.png',
                        'Abr' => $event['T2'][0]['Abr']
                    ]
                ];

                if (!in_array($event['Eps'], ['NS', 'Canc.', 'Postp.', 'Aband.'])) {
                    $eventData['Score1'] = $event['Tr1'];
                    $eventData['Score2'] = $event['Tr2'];
                }

                $stageData['events'][] = $eventData;
            }

            $data['Stages'][] = $stageData;
        }

        // Handle Next Event
        $event = $resNextData['Event'];
        $form = $resNextData['Form'];
        $team1 = $event['T1'][0];
        $team2 = $event['T2'][0];

        $team1Last = collect($form['T1'][0]['EL'] ?? [])->map(function ($match) use ($form) {
            $isTeam1 = $match['T1'][0]['ID'] == $form['T1'][0]['ID'];
            $score1 = $match['Tr1'];
            $score2 = $match['Tr2'];
            return $score1 > $score2 ? ($isTeam1 ? 1 : 2) :
                   ($score1 < $score2 ? ($isTeam1 ? 2 : 1) : 3);
        });

        $team2Last = collect($form['T2'][0]['EL'] ?? [])->map(function ($match) use ($form) {
            $isTeam2 = $match['T2'][0]['ID'] == $form['T2'][0]['ID'];
            $score1 = $match['Tr1'];
            $score2 = $match['Tr2'];
            return $score2 > $score1 ? ($isTeam2 ? 1 : 2) :
                   ($score2 < $score1 ? ($isTeam2 ? 2 : 1) : 3);
        });

        // Format waktu dari Esd
        $timeStr = strval($event['Esd']);
        $carbonTime = Carbon::createFromFormat('YmdHis', $timeStr, 'UTC')->setTimezone('Asia/Bangkok');
        $formattedTime = $carbonTime->format('YmdHis');

        $data['NextEvent'] = [
            'matchID' => $event['Eid'],
            'time_start' => $formattedTime,
            'team1' => [
                'teamNM' => $team1['Nm'],
                'teamID' => $team1['ID'],
                'teamIMG' => isset($team1['Img']) ? $imgTeam . $team1['Img'] : '/img/default_team.png',
                'CoNm' => $team1['CoNm'] ?? '',
                'CoId' => $team1['CoId'] ?? '',
                'lastMt' => $team1Last->toArray()
            ],
            'team2' => [
                'teamNM' => $team2['Nm'],
                'teamID' => $team2['ID'],
                'teamIMG' => isset($team2['Img']) ? $imgTeam . $team2['Img'] : '/img/default_team.png',
                'CoNm' => $team2['CoNm'] ?? '',
                'CoId' => $team2['CoId'] ?? '',
                'lastMt' => $team2Last->toArray()
            ],
            'urlComp' => "{$event['Stg']['Ccd']}.{$event['Stg']['Scd']}"
        ];

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $data
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'code' => 500,
            'message' => 'Error fetching data: ' . $e->getMessage()
        ], 500);
    }
}

public function getPlayerStat($teamId, $eventId = null)
{
    $imgBadge = 'https://static.lsmedia1.com/competition/high/';
    $baseUrl = 'https://prod-cdn-team-api.lsmedia1.com/v1/api/app/team/';

    // Step 1: Ambil data tim
    $urlTeamDetail = "{$baseUrl}{$teamId}/details?locale=en&MD=1";
    $resTeam = Http::get($urlTeamDetail);

    if (!$resTeam->ok()) {
        return response()->json([
            'code' => 500,
            'message' => 'Failed to fetch team detail'
        ]);
    }

    $teamData = $resTeam->json();

    $datas = [
        'teamNM' => $teamData['Nm'] ?? '',
        'teamID' => $teamData['ID'] ?? '',
        'teamIMG' => $teamData['Img'] ?? '',
        'Abr' => $teamData['Abr'] ?? '',
        'CoNm' => $teamData['CoNm'] ?? '',
        'CoId' => $teamData['CoId'] ?? '',
        'Events' => [],
        'PlayerStat' => []
    ];

    // Step 2: Proses daftar kompetisi (Events)
    foreach ($teamData['Stages'] ?? [] as $stage) {
        $datas['Events'][] = [
            'Sid' => $stage['Sid'] ?? '',
            'Snm' => $stage['Snm'] ?? '',
            'Cnm' => $stage['Cnm'] ?? '',
            'CompN' => $stage['CompN'] ?? '',
            'CompST' => $stage['CompST'] ?? '',
            'badgeUrl' => $stage['badgeUrl'] ?? ''
        ];
    }

    // Step 3: Tentukan event ID (kalau tidak disediakan, ambil dari event pertama)
    if (!$eventId && !empty($datas['Events'])) {
        $eventId = $datas['Events'][0]['Sid'];
    }

    // Step 4: Ambil statistik pemain
    $urlStat = "{$baseUrl}{$teamId}/playersstat/{$eventId}?limit=20";
    $resStat = Http::get($urlStat);

    if (!$resStat->ok()) {
        return response()->json([
            'code' => 500,
            'message' => 'Failed to fetch player statistics'
        ]);
    }

    $statData = $resStat->json();

    foreach ($statData['Stat'] ?? [] as $statItem) {
        $typ = $statItem['Typ'] ?? '';
        $players = [];

        foreach ($statItem['Plrs'] ?? [] as $player) {
            $players[] = [
                'Aid' => $player['Aid'] ?? '',
                'Rnk' => $player['Rnk'] ?? '',
                'Fn' => $player['Fn'] ?? '',
                'Ln' => $player['Ln'] ?? '',
                'Pnm' => $player['Pnm'] ?? '',
                'Scr' => $player['Scrs'][strval($typ)] ?? '',
                'Tnm' => $player['Tnm'] ?? '',
                'Tid' => $player['Tid'] ?? ''
            ];
        }

        $datas['PlayerStat'][] = [
            'Typ' => $typ,
            'Plrs' => $players
        ];
    }

    return response()->json([
        'code' => 200,
        'message' => 'success',
        'data' => $datas
    ]);
}

public function getTeamSquad($id)
{
    $url = "https://prod-cdn-team-api.lsmedia1.com/v1/api/app/team/{$id}/squad";

    $response = Http::get($url);

    if (!$response->ok()) {
        return response()->json([
            'code' => 500,
            'message' => 'Failed to fetch squad data',
            'data' => []
        ]);
    }

    $res = $response->json();

    $data = [
        'teamID' => $res['ID'] ?? '',
        'teamNM' => $res['Nm'] ?? '',
        'CoNm' => $res['CoNm'] ?? '',
        'teamIMG' => $res['Img'] ?? '',
        'player' => []
    ];

    $posMapping = [
        1 => 'GOALKEEPERS',
        2 => 'DEFENDERS',
        3 => 'MIDFIELDERS',
        4 => 'ATTACKERS'
    ];

    foreach ($res['Ps'] ?? [] as $player) {
        $data['player'][] = [
            'playerID' => $player['Pid'] ?? '',
            'Pn' => $player['Pnm'] ?? '',
            'Fn' => $player['Fn'] ?? '',
            'Ln' => $player['Ln'] ?? '',
            'CoNm' => $player['CoNm'] ?? '',
            'RegImg' => isset($player['RegImg']) ? "https://static.lsmedia1.com/i2/fh/{$player['RegImg']}.jpg" : '',
            'Np' => $player['Snu'] ?? '',
            'Pos' => $posMapping[$player['Pos']] ?? 'COACH'
        ];
    }

    return response()->json([
        'code' => 200,
        'message' => 'success',
        'data' => $data
    ]);
}

public function getTeamStat($teamId, $eventId = null)
{
    // URL untuk detail tim
    $urlDetails = "https://prod-cdn-team-api.lsmedia1.com/v1/api/app/team/{$teamId}/details?locale=en&MD=1";
    $response = Http::get($urlDetails);

    if (!$response->ok()) {
        return response()->json([
            'code' => 500,
            'message' => 'Failed to fetch team details',
            'data' => []
        ]);
    }

    $res = $response->json();

    $datas = [
        'teamNM' => $res['Nm'] ?? '',
        'teamID' => $res['ID'] ?? '',
        'teamIMG' => $res['Img'] ?? '',
        'Abr' => $res['Abr'] ?? '',
        'CoNm' => $res['CoNm'] ?? '',
        'CoId' => $res['CoId'] ?? '',
        'Events' => [],
        'TeamStat' => []
    ];

    foreach ($res['Stages'] ?? [] as $stage) {
        $datas['Events'][] = [
            'Sid' => $stage['Sid'] ?? '',
            'Snm' => $stage['Snm'] ?? '',
            'Cnm' => $stage['Cnm'] ?? '',
            'CompN' => $stage['CompN'] ?? '',
            'CompST' => $stage['CompST'] ?? '',
            'badgeUrl' => $stage['badgeUrl'] ?? ''
        ];
    }

    // Tentukan eventId
    if ($eventId === null && !empty($datas['Events'])) {
        $eventId = $datas['Events'][0]['Sid'];
    }

    // Jika eventId tetap kosong, hentikan
    if (!$eventId) {
        return response()->json([
            'code' => 404,
            'message' => 'Event ID not found',
            'data' => $datas
        ]);
    }

    // Request untuk statistik tim
    $urlStat = "https://prod-cdn-team-api.lsmedia1.com/v1/api/app/team/{$teamId}/teamstat/{$eventId}";
    $statResponse = Http::get($urlStat);

    if ($statResponse->ok()) {
        $statRes = $statResponse->json();
        $datas['TeamStat'][] = $statRes['statsGroup'] ?? [];
    }

    return response()->json([
        'code' => 200,
        'message' => 'success',
        'data' => $datas
    ]);
}

public function getTeamTable($id)
{
    $url = "https://prod-cdn-team-api.lsmedia1.com/v1/api/app/team/{$id}/league-table?type=full&locale=ID";
    $img_badge = 'https://static.lsmedia1.com/competition/high/';
    $img_team = 'https://lsm-static-prod.lsmedia1.com/medium/';

    $response = Http::get($url);

    if (!$response->ok()) {
        return response()->json([
            'code' => 500,
            'message' => 'Failed to fetch league table',
            'data' => []
        ]);
    }

    $res = $response->json();

    $data = [
        'Sid' => $res['Sid'] ?? '',
        'Snm' => $res['Snm'] ?? '',
        'Scd' => $res['Scd'] ?? '',
        'badgeUrl' => $img_badge . ($res['badgeUrl'] ?? ''),
        'CompId' => $res['CompId'] ?? '',
        'CompN' => $res['CompN'] ?? '',
        'CompST' => $res['CompST'] ?? '',
        'LeagueTable' => []
    ];

    $teams = $res['LeagueTable']['L'][0]['Tables'][0]['team'] ?? [];

    foreach ($teams as $team) {
        $data['LeagueTable'][] = [
            'rank' => $team['rnk'] ?? '',
            'teamID' => $team['Tid'] ?? '',
            'teamNM' => $team['Tnm'] ?? '',
            'teamIMG' => $img_team . ($team['Img'] ?? ''),
            'p' => $team['pld'] ?? '',
            'gd' => $team['gd'] ?? '',
            'pts' => $team['pts'] ?? '',
            'w' => $team['win'] ?? '',
            'd' => $team['drw'] ?? '',
            'l' => $team['lst'] ?? '',
            'f' => $team['gf'] ?? '',
            'a' => $team['ga'] ?? ''
        ];
    }

    return response()->json([
        'code' => 200,
        'message' => 'success',
        'data' => $data
    ]);
}

public function getTeamNews($idTeam)
{
    $key = 'JDJhJDEyJFVLNUJ3d2c1MnFCaktnU0w0dzJMNU9GWEJQT0FaSTcwa0ZZUWpIbXF0YVRNMEU3aHZwQkF1';
    $teamDetailUrl = "https://prod-cdn-team-api.lsmedia1.com/v1/api/app/team/{$idTeam}/details?locale=en&MD=1";

    try {
        /* ---------- Ambil data tim ---------- */
        $teamResponse = Http::get($teamDetailUrl);
        if (!$teamResponse->ok()) {
            return response()->json([
                'code'    => $teamResponse->status(),
                'message' => 'Failed to fetch team details',
                'data'    => []
            ], $teamResponse->status());
        }

        $teamData  = $teamResponse->json();
        $teamName  = $teamData['Nm'] ?? '';
        $slug      = strtolower(str_replace(' ', '-', $teamName));

        /* ---------- Ambil berita ---------- */
        $newsApi   = "https://mansionsportsfc.com/api/news/tag?key={$key}&slug={$slug}";
        $newsResp  = Http::get($newsApi);
        $newsData  = $newsResp->ok() ? $newsResp->json() : ['news' => []];

        /* ---------- Susun response ---------- */
        $data = [
            'NMTeam' => $teamName,
            'News'   => $newsData['news'] ?? [],
            'URL'    => 'https://mansionsportsfc.com/'
        ];

        return response()->json([
            'code'    => 200,
            'message' => 'success',
            'data'    => $data
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'code'    => 500,
            'message' => 'Error fetching data',
            'error'   => $e->getMessage()
        ], 500);
    }
}

public function getDetailMatchInfo($id)
{
    try {
        // Ambil detail pertandingan
        $urlMatch = "https://prod-cdn-public-api.lsmedia1.com/v1/api/app/scoreboard/soccer/{$id}?locale=ID";
        $matchRes = Http::get($urlMatch);

        if (!$matchRes->ok()) {
            return response()->json([
                'code' => $matchRes->status(),
                'message' => 'Failed to fetch match data',
                'data' => []
            ]);
        }

        $res = $matchRes->json();

        // Cek file SVG jersey
        $imageFiles = File::files(public_path('img/jersey'));
        $imageNames = collect($imageFiles)->map(fn($file) => strtolower($file->getFilename()))->toArray();

        // Format waktu ke zona Asia/Bangkok
        $timestampStr = (string) $res['Esd'];
        $datetime = Carbon::createFromFormat('YmdHis', $timestampStr, 'UTC')->setTimezone(new DateTimeZone('Asia/Bangkok'));
        $formattedTime = $datetime->format('YmdHis');

        // Ambil data vote dari API eksternal
        $voteKey = 'JDJhJDEyJFVLNUJ3d2c1MnFCaktnU0w0dzJMNU9GWEJQT0FaSTcwa0ZZUWpIbXF0YVRNMEU3aHZwQkF1';
        $voteUrl = "https://mansionsportsfc.com/api/total-vote?key={$voteKey}&id_match={$id}";
        $voteRes = Http::get($voteUrl)->json();

        // Perhitungan persentase vote
        $totalVote = $voteRes['match']['total_vote'] ?? 0;
        $getVote = fn($val) => [
            $val,
            $this->hitungPersen($val, $totalVote)
        ];

        $voteData = [
            'team1' => isset($voteRes['match']) ? $getVote($voteRes['match']['team1']['total_votes']) : [0, 0],
            'team2' => isset($voteRes['match']) ? $getVote($voteRes['match']['team2']['total_votes']) : [0, 0],
            'draw'  => isset($voteRes['match']) ? $getVote($voteRes['match']['draw']) : [0, 0],
            'total_vote' => $totalVote
        ];

        // Jersey path
        $team1ID = $res['T1'][0]['ID'];
        $team2ID = $res['T2'][0]['ID'];
        $jersey1 = in_array(strtolower("{$team1ID}.svg"), $imageNames) ? "img/jersey/{$team1ID}.svg" : "img/jersey/team-1.svg";
        $jersey2 = in_array(strtolower("{$team2ID}.svg"), $imageNames) ? "img/jersey/{$team2ID}.svg" : "img/jersey/team-2.svg";

        $data = [
            'time_start' => $formattedTime,
            'stadium'    => $res['Venue']['Vnm'] ?? '',
            'views'      => $res['Venue']['Vsp'] ?? '',
            'score1'     => $res['Tr1'] ?? '',
            'score2'     => $res['Tr2'] ?? '',
            'match' => [
                'idMatch' => $res['Eid'],
                'team1' => [
                    'IDTeam'   => $team1ID,
                    'NMTeam'   => $res['T1'][0]['Nm'],
                    'IMGJersey'=> $jersey1
                ],
                'team2' => [
                    'IDTeam'   => $team2ID,
                    'NMTeam'   => $res['T2'][0]['Nm'],
                    'IMGJersey'=> $jersey2
                ]
            ],
            'vote' => $voteData
        ];

        return response()->json([
            'code'    => 200,
            'message' => 'success',
            'data'    => $data
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'code' => 500,
            'message' => 'Internal server error',
            'error' => $e->getMessage()
        ], 500);
    }
}

private function hitungPersen($bagian, $total)
{
    if ($total == 0 || $bagian == 0) return 0.0;
    return round(($bagian / $total) * 100, 2);
}

}
