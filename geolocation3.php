<!doctype html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <title>Untitled Document</title>
        <style type="text/css">
            #formcontainer {
                width: 300px;
            }
            #map-canvas {
                width: 100%;
                height: 300px;
            }
            #city {
                width: 100%;
            }
        </style>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&sensor=true"></script>
        <script type="text/javascript" src="geolocation-map.js"></script>
        <script type="text/javascript" src="geolocation-map-formsubmit.js"></script>
        <script type="text/javascript">
            mapform.options.mapElementId = 'map-canvas';
            mapform.options.formId = 'theform';
            mapform.options.gpsElementName = 'latlng';
            mapform.options.autocompleteElementId = 'city';
            mapform.options.addressSet = function(address) {
                if (address) {
                    var countrylist = $('#country');
                    if ($('option[value="' + address.country + '"]', countrylist).size()) {
                        countrylist.val(address.country);
                    } else {
                        var optbyname = countrylist.children().filter(function () {
                            return $(this).text() === address.country; 
                        }).first();
                        if (optbyname.size()) {
                            countrylist.val(optbyname.attr('value'));
                        } else {
                            countrylist.val('');
                        }
                    }
                    updateStateList(function() {
                        var statelist = $('#state');
                        if ($('option[value="' + address.state + '"]', statelist).size()) {
                            statelist.val(address.state);
                        } else {
                            var optbyname = statelist.children().filter(function () {
                                return $(this).text() === address.state; 
                            }).first();
                            if (optbyname.size()) {
                                statelist.val(optbyname.attr('value'));
                            } else {
                                statelist.val('');
                            }
                        }
                    });
                    $('#city').val(address.city);
                    $('#fulllocation').val(address.locationinfo.formatted_address);
                    $('#testeverything').val(JSON.stringify(address.locationinfo));
                }
            };
            mapform.init();
            $(function() {
                $('#country').change(function() {
                    $('#state').val('');
                    $('#city').val(($('#state').val() ? $('#state').val() + ',': '') + $('#country').val())
                    mapform.update(true, function () {
                        $('#city').val('');
                    });
                });
                $('#state').change(function() {
                    $('#city').val(($('#state').val() ? $('#state').val() + ',': '') + $('#country').val())
                    mapform.update(true, function () {
                        $('#city').val('');
                    });
                });
                $('#city').on('change input', function () {
                });
                $('#city').focus(function () {
                    setTimeout(function () {
                        $('#city').select();
                    }, 200);
                });
            });
        </script>
    </head>

    <body>
        <div id="formcontainer">
            <form id="theform" name="theform" action="geolocation1-accept.php" method="POST" enctype="application/x-www-form-urlencoded">
                <div id="map-canvas"></div>
                <div class="formsection">
                    <label for="city">City:</label>
                    <input type="text" name="city" id="city" /><br />
                    <label for="state">State/Province: </label>
                    <?php
                    require_once('countrystate.php');
                    $countrystate_helper = new CountryState();
                    $countrystate_helper->useCodesForCountry = false;
                    $countrystate_helper->useCodesForState = false;
                    echo $countrystate_helper->outputJquery('countrystate-request.php');
                    ?>
                    <?php echo $countrystate_helper->outputStateSelectHtml(); ?><br />
                    <label for="country">Country: </label>
                    <?php echo $countrystate_helper->outputCountrySelectHtml(); ?><br />
                    <input type="hidden" name="latlng" id="latlng" />
                    <input type="hidden" name="fulllocation" id="fulllocation" />
                    <input type="hidden" name="testeverything" id="testeverything" />
                    <input type="submit" name="Submit" id="Submit" value="Submit" />
                </div>
            </form>
        </div>
    </body>
</html>
