mapform = {
    options: {
        addressSet: null,
        addressGet: null,
        mapElementId: null,
        formId: null,
        gpsElementName: null,
        autocompleteElementName: null,
        autocompleteWillUpdateText: true,
        updateFormOnSubmit: true
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
        if (mapform.options.autocompleteElementName) {
            var jqAutocomplete = '[name="' + mapform.options.autocompleteElementName + '"]';
            var prevAddressSet = mapform.options.addressSet;
            mapform.options.addressSet = function(address) {
                if (address) {
                    $(jqAutocomplete, $('#' + mapform.options.formId)).val(
                        (address.city ? address.city + ", " : "") 
                        + (address.state ? address.state + ", " : "")
                        + address.country);
                }
                if (prevAddressSet) {
                    prevAddressSet(address);
                }
            };
            mapform.options.addressGet = function() {
                return $(jqAutocomplete, $('#' + mapform.options.formId)).val();
            };
        }
        maphelper.initCallback = mapform.options.addressSet;
        maphelper.mapElementId = mapform.options.mapElementId;
        $(function() {
            var submitblock = true;
            var form = $('#' + mapform.options.formId);
            if (mapform.options.autocompleteElementName) {
                var addr_autocomplete = new google.maps.places.Autocomplete(
                    $(jqAutocomplete, $('#' + mapform.options.formId))[0],
                    {
                        types: ['(cities)']
                    }
                    );
                google.maps.event.addListener(addr_autocomplete, 'place_changed', function() {
                    mapform.update(mapform.options.autocompleteWillUpdateText);
                });
            }
            form.submit(function(e) {
                if (submitblock) {
                    e.preventDefault();
                    mapform.update(true, function(address) {
                        if (address) {
                            mapform.options.addressSet(address);
                        }
                        $('[name="' + mapform.options.gpsElementName + '"]'
                            , form).val(maphelper.getMapPosition());
                        submitblock = false;
                        form.submit();
                    });
                    return false;
                }
            });
            maphelper.init();
        });
    }
};
