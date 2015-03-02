{{ Form::open(array('route' => 'route.name', 'method' => 'POST')) }}
	<ul>
		<li>
			{{ Form::label('externalid', 'Externalid:') }}
			{{ Form::text('externalid') }}
		</li>
		<li>
			{{ Form::label('region', 'Region:') }}
			{{ Form::text('region') }}
		</li>
		<li>
			{{ Form::label('leader', 'Leader:') }}
			{{ Form::text('leader') }}
		</li>
		<li>
			{{ Form::label('latitude', 'Latitude:') }}
			{{ Form::text('latitude') }}
		</li>
		<li>
			{{ Form::label('longitude', 'Longitude:') }}
			{{ Form::text('longitude') }}
		</li>
		<li>
			{{ Form::label('name', 'Name:') }}
			{{ Form::text('name') }}
		</li>
		<li>
			{{ Form::label('url', 'Url:') }}
			{{ Form::text('url') }}
		</li>
		<li>
			{{ Form::label('address', 'Address:') }}
			{{ Form::text('address') }}
		</li>
		<li>
			{{ Form::label('city', 'City:') }}
			{{ Form::text('city') }}
		</li>
		<li>
			{{ Form::label('state', 'State:') }}
			{{ Form::text('state') }}
		</li>
		<li>
			{{ Form::label('zip', 'Zip:') }}
			{{ Form::text('zip') }}
		</li>
		<li>
			{{ Form::label('email', 'Email:') }}
			{{ Form::text('email') }}
		</li>
		<li>
			{{ Form::label('phone', 'Phone:') }}
			{{ Form::text('phone') }}
		</li>
		<li>
			{{ Form::label('twitter', 'Twitter:') }}
			{{ Form::text('twitter') }}
		</li>
		<li>
			{{ Form::label('facebook', 'Facebook:') }}
			{{ Form::text('facebook') }}
		</li>
		<li>
			{{ Form::submit() }}
		</li>
	</ul>
{{ Form::close() }}