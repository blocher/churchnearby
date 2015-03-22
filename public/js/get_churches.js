$(document).ready(function() {

	var x = document.getElementById("demo");

	function getLocation() {
	    if (navigator.geolocation) {
	        navigator.geolocation.getCurrentPosition(listChurches);
	    } else {
	        x.innerHTML = "Geolocation is not supported by this browser.";
	    }
	}

	function showPosition(position) {
	    x.innerHTML = "Latitude: " + position.coords.latitude + 
	    "<br>Longitude: " + position.coords.longitude; 
	}

	function listChurches(position) {

		var latitude =  position.coords.latitude;
		var longitude =  position.coords.longitude;


		$.get("/api/nearbyChurchesView?latitude="+latitude+"&longitude="+longitude, function(data, status){
		     $('#content').html(data);
		 });
	}


	getLocation();
});