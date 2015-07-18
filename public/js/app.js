
var app = function () {

    /*
     *  Init feed scripts
     */
    var init = function() {

    };

    /*
     * Render a list of churches
     */
    var render = function() {
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
                    console.log(data);
                } else {
                    console.log(data);
                }
            },
            error: function(data) {
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
            init();
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
    app().getChurchesByLocation(38.8137610,-77.1098310,2);
});


