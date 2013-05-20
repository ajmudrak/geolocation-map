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
            #freeformaddress {
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
            mapform.options.autocompleteElementName = 'freeformaddress';
            mapform.init();
        </script>
    </head>

    <body>
        <div id="formcontainer">
            <form id="theform" name="theform" action="geolocation1-accept.php" method="POST" enctype="application/x-www-form-urlencoded">
                <div id="map-canvas"></div>
                <div class="formsection">
                    <label for="freeformaddress">City, State/Province, Country:</label><input type="text" name="freeformaddress" id="freeformaddress" /><br />
                    <input type="hidden" name="latlng" id="latlng" />
                    <input type="submit" name="Submit" id="Submit" value="Submit" />
                </div>
            </form>
        </div>
    </body>
</html>
