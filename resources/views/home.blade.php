@extends('app')

@section('content')
	<div class="container">

		<div class="row">
			<div class="col-md-12">
				<div class="page-header">
					  <h1>
					  	Nearest Churches
					 </h1>
					 <div id="denomination"></div>
				</div>
			</div>
		</div> <!--row -->


		<div class="row">

			<div class="col-md-4">
				<div class="panel panel-default">
					<div class="panel-heading">Current Location</div>
					<div class="panel-body">
						{!! Form::open() !!}
							{!! Form::button('View nearest churches',array('id'=>'nearby-button')); !!}
						{!! Form::close() !!}
					</div>
				</div>

			</div>

			<div class="col-md-4">
				<div class="panel panel-default">
				  	<div class="panel-heading">Lookup address</div>
				  	<div class="panel-body">
						{!! Form::open() !!}
						    {!! Form::text('address', '', array('id'=>'address-field')) !!}
						    {!! Form::button('Look up',array('id'=>'address-button')); !!}
						{!! Form::close() !!}
				 	 </div>
				</div>

			</div>

			<div class="col-md-4">
				<div class="panel panel-default">
				  <div class="panel-heading">Denominations</div>
				  <div class="panel-body">
						@foreach ($denominations as $denomination)
					
							<button type="button" class="btn btn-primary denomination-button" data-denomination="{{ $denomination->slug }}">
							{{ $denomination->tag_name }}
							</button>
				
						@endforeach
				  </div>
				</div>
			</div>

		</div> <!-- row -->

		<div class="row">
			<div class="col-md-12">
				<div id="content">
					<div id="loader" class="loader hidden">
						<h4>Loading...</h4>
						<i class="fa fa-spin fa-spinner fa-4x"></i>
					</div>
				</div>
			</div>
		</div> <!-- row -->
			
	</div> <!-- container -->
	
@endsection
