@extends('app')

@section('content')
	<div class="container-fluid">

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


			

			<div class="col-md-9">

					
				<div class="row">
				  

				  <div class="col-md-6">
				      {!! Form::open() !!}
						{!! Form::button('View churches nearby',array('id'=>'nearby-button', 'class'=>'btn btn-success form-control')); !!}
					  {!! Form::close() !!}
				  </div><!-- /.col-md-6 -->
			
				  <div class="col-md-6">
				    <div class="input-group">
				      <input id="address-field" type="text" class="form-control" placeholder="Full address, city/state, OR zip code">
				      <span class="input-group-btn">
				        <button id='address-button' class="btn btn-success" type="button">Look up !</button>
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

			      <div class="alert alert-danger">
				  		<span id="denomination_note"></span>&nbsp;
				  		<a id='denomination_clear'>Clear</a>
				  </div>
				  <div class="panel-body" id="denominations">
						<i class="fa fa-cog fa-spin"></i>
				  </div>

			</div>

		

		</div> <!-- row -->


			
	</div> <!-- container -->
	
@endsection
