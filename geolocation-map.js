var map;
var pos;
var marker;
var geocoder;

maphelper = {
    mapElementId: null,
    useCountryLongName: true,
    useStateLongName: true,
    useCityLongName: true,
    initCallback: null,
    updateMapWithAddress: function(full_address, updatetext, callback) {
        geocoder.geocode({address: full_address}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                var locationinfo = results[0];
                if (locationinfo) {
                    pos = locationinfo.geometry.location;
                    maphelper.updateLocation(updatetext, callback);
                }
            }
        });
    },
    updateMapText: function(callback) {
        geocoder.geocode({
            location: pos
        }, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                var locationinfo = results[0];
                if (locationinfo) {
                    var country = '';
                    var state = '';
                    var city = '';
                    for (i = 0; i < locationinfo.address_components.length; i++) {
                        var addr = locationinfo.address_components[i];
                        if (addr.types.indexOf('country') > -1) {
                            if (maphelper.useCountryLongName) {
                                country = addr.long_name;
                            } else {
                                country = addr.short_name;
                            }
                        }
                        if (addr.types.indexOf('administrative_area_level_1') > -1) {
                            if (maphelper.useStateLongName) {
                                state = addr.long_name;
                            } else {
                                state = addr.short_name;
                            }
                        }
                        if (addr.types.indexOf('locality') > -1) {
                            if (maphelper.useCityLongName) {
                                city = addr.long_name;
                            } else {
                                city = addr.short_name;
                            }
                        }
                    }
                    if (typeof callback == 'function') {
                        callback({
                            country: country,
                            state: state,
                            city: city
                        });
                    }
                }
            }
        });
    },
    getMapPosition: function() {
        return pos.toString();
    },
    clientlocation_fallback: function(callback) {
        if (google.loader.ClientLocation) {
            pos = new google.maps.LatLng(
                    google.loader.ClientLocation.latitude,
                    google.loader.ClientLocation.longitude);
            maphelper.updateLocation(true, callback);
        } else {
            posset = false;
            maphelper.updateLocation(false, callback);
        }
    },
    updateLocation: function(updateText, callback) {
        if (posset) {
            map.setCenter(pos);
            map.setZoom(8);
            maphelper.updateMarker();
            if (updateText) {
                maphelper.updateMapText(callback);
            } else {
                if (typeof callback == 'function') {
                    callback();
                }
            }
        }
    },
    updateMarker: function() {
        if (marker) {
            marker.setPosition(pos);
            marker.setVisible(true);
        } else {
            marker = new google.maps.Marker({
                map: map,
                position: pos,
                visible: true
            });
        }
    },
    _mapinit: function() {

        posset = true;
        pos = new google.maps.LatLng(0, 0);
        map = null;
        geocoder = new google.maps.Geocoder();
        marker = null;

        var mapOptions = {
            zoom: 0,
            center: pos,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        map = new google.maps.Map(document.getElementById(maphelper.mapElementId),
                mapOptions);

        // Set location
        if (navigator.geolocation) {
            // Do HTML5 detection first
            navigator.geolocation.getCurrentPosition(function(position) {
                pos = new google.maps.LatLng(position.coords.latitude,
                        position.coords.longitude);
                maphelper.updateLocation(true, maphelper.initCallback);
            }, function() {
                // Detection failed
                maphelper.clientlocation_fallback(maphelper.initCallback);
            });
        } else {
            // Detection not possible
            maphelper.clientlocation_fallback(maphelper.initCallback);
        }
    },
    init: function() {
        google.maps.event.addDomListener(window, 'load', maphelper._mapinit);
    }
};
