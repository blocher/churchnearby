
var app = function () {

    /** Handlebars templates **/
    var church_listings;
    var church_summary;

    var current_latitude;
    var current_longitude;

    /*
     *  Init feed scripts
     */
    var init = function() {

        var source = '';

        // Init Handlebars templates
        source   = $("#church-listings").html();

        church_listings = Handlebars.compile(source);

        source = $("#church-summary").html();
        church_summary = Handlebars.compile(source);
        Handlebars.registerPartial('church_summary',church_summary);

        if (navigator.geolocation) {
          position =  navigator.geolocation.getCurrentPosition(setChurches);
        } else {
          //  x.innerHTML = "Geolocation is not supported by this browser.";
        }

    };

    var setChurches = function(position) {
     
        current_latitude = position.coords.latitude;
        current_longitude = position.coords.longitude;

        if (current_latitude && current_longitude) {
            var churches = getChurchesByLocation(current_latitude,current_longitude);
        }

    }

    /*
     * Render a list of churches
     */
    var render = function(churches,element) {
        
        if (!element) {
            element = '#content';
        } else {
            element = '#'+element;
        }
        $(element).html(church_listings(churches));
    };

    var getChurchesByAddress = function(address,denomination) {
        return getChurches(address,'','',denomination);
    }

    var getChurchesByLocation = function(latitude,longitude,denomination) {
        return getChurches('',latitude,longitude,denomination);
    }

    var getChurches = function(address,latitude,longitude,denomination) {

        var endpoint = '/api/nearbyChurches';
        var request_type = 'GET';

        if (latitude && longitude) {
            var data = {
                'latitude': latitude,
                'longitude': longitude,
                'denomination' : denomination,
            }
        } else {
            var data = {
                'address': address,
                'denomination': denomination,
            };
        }
        $.ajax({
            url: endpoint,
            type: request_type,
            data: data,
            success:  function(data, status){
                if (status=='success' && data.status=="ok") {
                    render(data);
                } else {
                    console.log('b');
                    console.log(data);
                }
            },
            error: function(data) {
                console.log('c');
               console.log(data);
            }
        });
    }


    // Add a new spinner to the page
    var addSpinner = function(element) {
        var spinnerImg = $('<img class="feed_spinner" src="" />');
        if (element) {
            spinnerImg.prependTo(element);
        }
        else {
            spinnerImg.prependTo(feedDiv);
        }
    };

    // Remove all spinners
    var removeSpinner = function() {
        var spinner = $('.feed_spinner');

        if (spinner) {
            spinner.remove();
        }
    };

    return {
        init: function() {
            init();
        },
        render: function() {
            render();
        },
        getChurchesByAddress: function(church,denomination) {
            getChurchesByAddress(church,denomination);
        },
        getChurchesByLocation: function(latitude,longitude,denomination) {
            getChurchesByLocation(latitude,longitude,denomination);
        }
    };

};

$(document).ready(function() {
    app().init();
    
});


