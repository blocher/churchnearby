$(document).ready(function() {

	var x = document.getElementById("demo");
	var latitude;
	var longitude;


	function setLatLng(lat,lng) {
		latitude =  lat;
		longitude =  lng;
	}

	function listChurchesLatLng() {
		$('#content').html('');
		$('#loader').removeClass('hidden');
		$.get("/api/nearbyChurchesView?latitude="+latitude+"&longitude="+longitude, function(data, status){
		     $('#content').html(data);
		     $('#loader').addClass('hidden');
		 });
	}

	function listChurchesAddress(address) {
		$('#content').html('');
		$('#loader').removeClass('hidden');
		$.get("/api/nearbyChurchesView?address="+address, function(data, status){
		     $('#loader').addClass('hidden');
		     $('#content').html(data);
		});
	}

	function setCurrentPosition(position) {
		
		latitude =  position.coords.latitude;
		longitude =  position.coords.longitude;

		setLatLng(latitude,longitude);
		listChurchesLatLng();
	}

	$("#nearby-button").click(function() {

		$('#content').html('');
		$('#loader').removeClass('hidden');
		if (navigator.geolocation) {
	        navigator.geolocation.getCurrentPosition(setCurrentPosition,geoLocationError);
	    } else {
	        $('#content').html("Geolocation is not supported by this browser.");
	        $('#loader').addClass('hidden');
	    }
	});

	$("#address-button").click(function() {
		listChurchesAddress($("#address-field").val());
	});

	$(".denomination-button").click(function() {

		$.get("/api/nearbyChurchesView?latitude="+latitude+"&longitude="+longitude+"&denomination="+$(this).data('denomination'), function(data, status){
		     $('#content').html(data);
		 });
	});

	function geoLocationError(position) {

		   $('#content').html('<div class="alert alert-danger"><h4>Oops</h4><p>You must allow us to check your current location in order to find the nearest church.</p></div>');
			//TODO: OFFER A FALLBACK, LIKE AN ADDRESS LOOKUP
	}

});