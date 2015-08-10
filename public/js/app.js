
var app = function() {

    /** Handlebars templates **/
    var church_listings;
    var church_summary;

    /** Current query **/
    var curent_address;
    var current_latitude;
    var current_longitude;
    var current_denomination;

    var popstate = false;

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

        source   = $("#denomination-listings").html();
        denomination_listings = Handlebars.compile(source);

        source = $("#denomination-summary").html();
        denomination_summary = Handlebars.compile(source);
        Handlebars.registerPartial('denomination-summary',denomination_summary);

        getDenominations();

        //Bind buttons


        $('#address-button').click(function() {
            lookupAddress();
        });

        $('#nearby-button').click(function() {
            lookupNearest();
        });

        var pathArray = window.location.pathname.split( '/' );
        if (pathArray[1]) {
            current_denomination = pathArray[1];
        } else {
            current_denomination = '';
        }

        current_latitude = getParameterByName('latitude');
        current_longitude = getParameterByName('longitude');
        current_address = getParameterByName('address');

        display();

        //Bind history change
        window.onpopstate = function(event) {
          current_address = event.state.current_address;
          current_latitude = event.state.current_latitude;
          current_longitude = event.state.current_longitude;
          current_denomination = event.state.current_denomination;
          popstate = true;
          display();
        };

        //set initial church list
        //lookupNearest();
    };

    var getDenominations = function() {

        var endpoint = '/api/denominations';
        var request_type = 'GET';
        $.ajax({
            url: endpoint,
            type: request_type,
            success:  function(data, status){
                if (status=='success' && data.status=="ok") {
                    $('#denominations').html(denomination_listings(data));
                    $('.denomination-button').click(function() {
                        changeDenomination($(this).data('denomination'));
                    });
                } else {
                    error("We couldn't find the denomiation list.  Please try again.");
                }
            },
            error: function(data) {
               error("We couldn't find the denomiation list.   Please try again.");
            }
        });

    }


    var getParameterByName = function(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }

    var changeDenomination = function(denomination) {
        $('.denomination-button').removeClass('btn-danger').addClass('btn-primary');
        $('button.denomination-button[data-denomination="' + denomination + '"]').removeClass('btn-primary').addClass('btn-danger');
        current_denomination = denomination;
        display();
    }

    var display = function() {

        addSpinner();

        if (current_address) {
            getChurchesByAddress(current_address,current_denomination);
        } else if (current_latitude && current_longitude) {
            getChurchesByLocation(current_latitude, current_longitude, current_denomination);
        } else {
            error('Please click "View Nearest Churches" or enter an address above.');
        }

    }

    var lookupAddress = function() {
       addSpinner();
       var address =  $('#address-field').val();
       getChurchesByAddress(address,current_denomination);
    }

    var changeURL = function() {

         var title;
         var url;
         if (current_denomination) {
            title = "Churches Nearby: " + current_denomination;
            url = current_denomination;
         } else {
            title = "Churches Nearby: ";
            url = '';
         }

         sendGAPageview(url,title);

         if (popstate) {
            popstate = false;
            return;
         }

         var stateObj = {
            current_address: current_address,
            current_denomination: current_denomination,
            current_latitude: current_latitude,
            current_longitude: current_longitude
         };
         history.pushState(stateObj,
            title,
            url
                + '?latitude=' + current_latitude + '&longitude=' + current_longitude  +
                  '&address=' + current_address
            );

    }

    var lookupNearest = function() {
        addSpinner();
        //Set initial results to closest churches
        if (navigator.geolocation) {
          position =  navigator.geolocation.getCurrentPosition(setChurches,geolocationError);
        } else {
          $('#content').html('Oops!  We can\'t automatically find the nearest churches because geolocation is not enabled on your deivce or browser.  Please enter your address instead.');
        }
    }

    var setChurches = function(position) {
     
        current_latitude = position.coords.latitude;
        current_longitude = position.coords.longitude;
        current_address = '';

        if (current_latitude && current_longitude) {
            var churches = getChurchesByLocation(current_latitude,current_longitude,current_denomination);
        }

    }

    var geolocationError = function(error) {
        if (error.code == error.PERMISSION_DENIED) {
            error("You have disabled location services for this page.  Please enter your address instead.");
        } else {
            error("We were unable to automatically detect your address.  Please enter your address instead.");
        }

    }

    var error = function(message) {
          $('#content').html('<div class="alert alert-danger" role="alert">' + message + "</div>");
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
        if (churches.denomination && churches.denomination.name) {
            $('#denomination').html("<h3>" + churches.denomination.name + "</h3>");
        } else {
             $('#denomination').html("");
        }
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
            current_latitude = latitude;
            current_longitude = longitude;
            current_address = '';
        } else {
            var data = {
                'address': address,
                'denomination': denomination,
            };
            current_latitude = '';
            current_longitude = '';
            current_address = address;
        }

        current_denomination = denomination;
        $.ajax({
            url: endpoint,
            type: request_type,
            data: data,
            success:  function(data, status){
                if (status=='success' && data.status=="ok") {
                    render(data);
                } else {
                    error("We couldn't find that address.  Please try again.");
                }
                changeURL();
            },
            error: function(data) {
               error("We couldn't find that address.  Please try again.");
               changeURL();
            }
        });
    }

    var sendGAPageview = function(url,title) {

        ga('set', {
          page: '/' + url,
          title: title
        });
        ga('send', 'pageview');
    }


    // Add a new spinner to the page
    var addSpinner = function(element) {
        removeSpinner();
        if (!element) {
            element = '#content';
        } 

        element = $(element);
        var spinnerImg = $('<i class="spinner fa fa-spin fa-spinner fa-4x"></i>');
        spinnerImg.prependTo(element);
    
    };

    // Remove all spinners
    var removeSpinner = function() {
        var spinner = $('.spinner');


        if (spinner) {
            //TODO: why does content get removed first
            spinner.each(function() {
                $(this).parents('div').first().html('');
            });
            spinner.remove();
        }
    };

    return {
        init: function() {
            init();
        }
    };

};

$(document).ready(function() {

    app().init();
    
});


