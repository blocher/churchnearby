<div class="list-group">
  <div class="alert alert-success"><h4>Your closest church is ... <strong>{{ $churches[0]->name }}</strong></h4></div>
  <!-- <div class="alert alert-info"><p>Your are likely in the {{-- $diocese --}}</p></div> -->
  <h4>Closest churches...</h4>
  @foreach ($churches as $church)
    @include ('slices/church')
  @endforeach
</div>