@extends('app')

@section('content')



	<div class="container">

			 <div class="jumbotron" style="background-image: url('img/cover/{{ $cover_photo }}')">
		      <div class="container">
		        <h1><span class="fa fa-location-arrow"> Nearest Church</h1>
		        <p>Quickly find the closest churches where you are now or where you plan to go.</p>
		      </div>
		    </div>

		    <div class="col-md-12">

		    	<div class="row">
		    		&nbsp;
		    	</div>
		    </div>

			<div class="col-md-9">

					
				<div class="row">
				  

				  <div class="col-md-6">
				      {!! Form::open() !!}
						{!! Form::button('<span class="fa fa-location-arrow"> Lookup your current location',array('id'=>'nearby-button', 'class'=>'btn btn-success form-control')); !!}
					  {!! Form::close() !!}
				  </div><!-- /.col-md-6 -->
			
				  <div class="col-md-6">
				    <div class="input-group">
				      <input id="address-field" type="text" class="form-control" placeholder="Full address, city/state, OR zip code">
				      <span class="input-group-btn">
				        <button id='address-button' class="btn btn-success" type="button">Look up</button>
				      </span>
				    </div><!-- /input-group -->
				  </div><!-- /.col-md-6 -->
				


				</div><!-- /.row -->

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

			</div>


			<div class="col-md-3">

				  	<div class="panel panel-success">
				  		<div class="panel-heading">Filter by denomination</div>
			
					 	 <div class="panel-body">
							<div id="denominations">
								<i class="fa fa-cog fa-spin"></i>
							</div>
							 <div id='denomination_clear' class="alert alert-warning">
						  		<a><span class='fa fa-remove'> Remove filters</a>
					  		</div>
					  	</div>

					 </div>


			</div>

		

		</div> <!-- row -->


			
	</div> <!-- container -->
	
@endsection
