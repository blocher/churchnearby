@extends('app')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				<div class="page-header">
				  <h1><span class="fa fa-plus"></span>Nearest Churches</h1>
				</div>
				<div id="content">
				<h4>Loading...</h4>
				<div class="alert alert-warning">Please wait as we load the nearest church.  If you receive a dialogue asking to share your location, please click "Yes".</div>
				<i class="fa fa-spin fa-spinner fa-4x"></i>
				</div>
			</div>
		</div>
	</div>
@endsection
