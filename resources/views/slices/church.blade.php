<li class="list-group-item">
	<h4 class="list-group-item-heading" id="list-group-item-heading">{{ $church->name }}</h4>
	<p class="list-group-item-text"><!--{{ $church->address}}<br>-->{{ $church->city }}, {{ $church->state }} {{ $church->zip}}<br></p>
	<p class="list-group-item-text">Leader: {{ $church->leader }}</p>
	<p class="list-group-item-text"><em>{{ round($church->distance_in_miles, 2) }} miles away</em></p>
	<p><a target="_blank" href="http://maps.google.com/maps?f=q&amp;hl=en&amp;saddr={{ $latitude }},{{ $longitude }}&amp;daddr={{ $church->latitude }},{{$church->longitude }}">Driving directions</a>
	<p></p>
</li>
