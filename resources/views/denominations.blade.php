{{ Form::open(array('route' => 'route.name', 'method' => 'POST')) }}
	<ul>
		<li>
			{{ Form::label('name', 'Name:') }}
			{{ Form::text('name') }}
		</li>
		<li>
			{{ Form::label('url', 'Url:') }}
			{{ Form::text('url') }}
		</li>
		<li>
			{{ Form::label('region_name', 'Region_name:') }}
			{{ Form::text('region_name') }}
		</li>
		<li>
			{{ Form::submit() }}
		</li>
	</ul>
{{ Form::close() }}