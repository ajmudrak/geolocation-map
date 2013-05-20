mapform = {
    options: {
        addressSet: null,
        addressGet: null,
        mapElementId: null,
        formId: null,
        gpsElementName: null,
        autocompleteElementName: null,
        autocompleteWillUpdateText: true
    },
    update: function(updatetext, callback) {
        maphelper.updateMapWithAddress(mapform.options.addressGet(), updatetext, function(address) {
            if (address) {
                mapform.options.addressSet(address);
            }
            if (typeof callback == 'function') {
                callback();
            }
        });
    },
    init: function() {
        $(function() {
            var submitblock = true;
            var form = $('#' + mapform.options.formId);
            form.submit(function(e) {
                if (submitblock) {
                    e.preventDefault();
                    maphelper.updateMapWithAddress(
                            mapform.options.addressGet(),
                            true,
                            function(address) {
                                if (address) {
                                    mapform.options.addressSet(address);
                                }
                                $('[name="' + mapform.options.gpsElementName + '"]'
                                        , form).val(maphelper.getMapPosition());
                                submitblock = false;
                                form.submit();
                            }
                    );
                    return false;
                }
            });
            if (mapform.options.autocompleteElementName) {
                var jqAutocomplete = '[name="' + mapform.options.autocompleteElementName + '"]';
                mapform.options.addressSet = function(address) {
                    if (address) {
                        $(jqAutocomplete, form).val(
                                (address.city ? address.city +
                                ", " : "") + address.state +
                                ", " + address.country);
                    }
                };
                mapform.options.addressGet = function() {
                    return $(jqAutocomplete, form).val();
                };
                var addr_autocomplete = new google.maps.places.Autocomplete(
                        $(jqAutocomplete, form)[0],
                        {
                            types: ['(cities)']
                        }
                );
                google.maps.event.addListener(addr_autocomplete, 'place_changed', function() {
                    mapform.update(mapform.options.autocompleteWillUpdateText);
                });
            }
            maphelper.initCallback = mapform.options.addressSet;
            maphelper.mapElementId = mapform.options.mapElementId;
            maphelper.init();
        });
    }
};
