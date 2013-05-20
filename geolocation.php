<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Untitled Document</title>
        <style type="text/css">
            #formcontainer {
                width: 300px;
            }
            #map-canvas {
                width: 100%;
                height: 300px;
            }
        </style>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true"></script>
        <script type="text/javascript" src="geolocation-map.js"></script>
        <script type="text/javascript" src="geolocation-map-formsubmit.js"></script>
        <script type="text/javascript">
            mapform.options.mapElementId = 'map-canvas';
            mapform.options.formId = 'theform';
            mapform.options.gpsElementName = 'latlng';
            maphelper.useCityLongName = true;
            maphelper.useCountryLongName = false;
            maphelper.useStateLongName = false;
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
//                            countrylist.val('');
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
            mapform.options.addressGet = function() {
                var city = $('#city').val();
                return (city ? city
                        + "," : "") + $('#state').val()
                        + "," + $('#country').val();
            };
            mapform.init();

            $(function() {
                $('#testupdate').click(function() {
                    mapform.update(true);
                });
                $('#country,#state').change(function() {
                   $('#city').val('');
                   mapform.update(false);
                });
                $('#city').on('change input', function() {
                    clearTimeout($(this).data('timeout'));
                    var wait = setTimeout(function () {
                        mapform.update(true);
                    }, 1000);
                    $(this).data('timeout', wait);
                });
            });
        </script>
    </head>

    <body>
        <div id="formcontainer">
            <form id="theform" name="theform" action="geolocation1-accept.php" method="POST" enctype="application/x-www-form-urlencoded">
                <div id="map-canvas"></div>
                <div class="formsection">
                    <?php
                    require_once('countrystate.php');
                    $countrystate_helper = new CountryState();
                    echo $countrystate_helper->outputJquery('countrystate-request.php');
                    ?>
                    <label for="country">Country: </label>
                    <?php echo $countrystate_helper->outputCountrySelectHtml(); ?><br />
                    <label for="state">State/Province: </label>
                    <?php echo $countrystate_helper->outputStateSelectHtml(); ?><br />
                    <label for="city">City:</label><input type="text" name="city" id="city" /><br />
                    <input type="hidden" name="latlng" id="latlng" />
                    <input type="hidden" name="fulllocation" id="fulllocation" />
                    <input type="hidden" name="testeverything" id="testeverything" />
                    <!--<input type="button" name="testupdate" id="testupdate" value="Test Map Update" />-->
                    <input type="submit" name="Submit" id="Submit" value="Submit" />
                </div>
            </form>
        </div>
    </body>
</html>
