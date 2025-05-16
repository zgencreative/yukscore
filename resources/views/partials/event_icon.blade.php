@php $info = $player['info'] ?? ''; @endphp

@switch($info)
  @case('goal')
    <img src="{{ asset('/img/ball.png') }}">
    @break
  @case('yellow card')
    <img src="{{ asset('/img/yellow_card.png') }}">
    @break
  @case('red-var')
    <img src="{{ asset('/img/red-var.png') }}">
    @break
  @case('yellow-var')
    <img src="{{ asset('/img/yellow-var.png') }}">
    @break
  @case('no-goal-var')
    <img src="{{ asset('/img/no-goal-var.png') }}">
    @break
  @case('red card')
    <img src="{{ asset('/img/red_card.png') }}">
    @break
  @case('yellow-red card')
    <img src="{{ asset('/img/double_card.png') }}">
    @break
  @case('pen-no-goal')
    <img src="{{ asset('/img/pen-no-goal.png') }}">
    @break
  @case('pen-goal')
    <img src="{{ asset('/img/pen-goal.png') }}">
    @break
  @default
    <span class="px-2 py-1 border rounded">VAR</span>
@endswitch
