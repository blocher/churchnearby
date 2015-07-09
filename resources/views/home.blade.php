@extends('app')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
				  <div class="panel-heading">Lookup churches by address</div>
				  <div class="panel-body">
						{!! Form::open() !!}
						    {!! Form::text('address', '', array('id'=>'address-field')) !!}
						    {!! Form::button('Look up churches',array('id'=>'address-button')); !!}
						{!! Form::close() !!}
				  </div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3">
				<div class="panel panel-default">
				  <div class="panel-heading">Denominations</div>
				  <div class="panel-body">
						@foreach ($denominations as $denomination)
					
							<button type="button" class="btn btn-primary denomination-button" data-denomination="{{ $denomination->id }}">
							{{ $denomination->tag_name }}
							</button>
				
						@endforeach
				  </div>
				</div>
			</div>
			<div class="col-md-9">
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
