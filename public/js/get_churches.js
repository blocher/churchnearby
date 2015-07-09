$(document).ready(function() {

	var x = document.getElementById("demo");
	var latitude;
	var longitude;

	function initiate() {
	    if (navigator.geolocation) {
	        navigator.geolocation.getCurrentPosition(setLatLng,geoLocationError);
	    } else {
	        $('#content').html("Geolocation is not supported by this browser.");
	    }
	}

	function setLatLng(position) {
		latitude =  position.coords.latitude;
		longitude =  position.coords.longitude;
		listChurches();
	}

	function listChurches() {
		$.get("/api/nearbyChurchesView?latitude="+latitude+"&longitude="+longitude, function(data, status){
		     $('#content').html(data);
		 });
	}

	function listChurchesAddress(address) {
		$.get("/api/nearbyChurchesView?address="+address, function(data, status){
		     $('#content').html(data);
		});
	}

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

	initiate();
});