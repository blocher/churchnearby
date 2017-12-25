{{ Form::open(array('route' => 'route.name', 'method' => 'POST')) }}
	<ul>
		<li>
			{{ Form::label('long_name', 'Long_name:') }}
			{{ Form::text('long_name') }}
		</li>
		<li>
			{{ Form::label('short_name', 'Short_name:') }}
			{{ Form::text('short_name') }}
		</li>
		<li>
			{{ Form::label('url', 'Url:') }}
			{{ Form::text('url') }}
		</li>
		<li>
			{{ Form::label('denomination', 'Denomination:') }}
			{{ Form::text('denomination') }}
		</li>
		<li>
			{{ Form::submit() }}
		</li>
	</ul>
{{ Form::close() }}