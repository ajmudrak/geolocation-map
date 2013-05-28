mapform = {
    options: {
        addressSet: null,
        addressGet: null,
        mapElementId: null,
        formId: null,
        gpsElementName: null,
        autocompleteElementId: null,
        autocompleteWillUpdateText: true,
        updateFormOnSubmit: true,
        countryElementId: null,
        stateElementId: null,
        cityElementId: null,
        stateUpdateFunction: null,
        stateUpdateFunctionTakesCallback: true,
        formattedAddressElementId: null,
        fullLocationDataElementId: null
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
        var jqAutocomplete;
        if (mapform.options.autocompleteElementId) {
            jqAutocomplete = '#' + mapform.options.autocompleteElementId;
            var prevAddressSet = mapform.options.addressSet;
            mapform.options.addressSet = function(address) {
                if (address) {
                    $(jqAutocomplete, $('#' + mapform.options.formId)).val(
                        (address.city ? address.city + ", " : "") 
                        + (address.state ? address.state + ", " : "")
                        + address.country);
                    
                    if (mapform.options.countryElementId) {
                        var countrylist = $('#' + mapform.options.countryElementId);
                        if (countrylist.prop('tagName').toUpperCase() == 'SELECT') {
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
                        }
                    }
                    if (mapform.options.stateElementId) {
                        var stateUpdateCallback = function() {
                            var statelist = $('#' + mapform.options.stateElementId);
                            if (statelist.prop('tagName').toUpperCase() == 'SELECT') {
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
                            }
                        };
                        if (typeof mapform.options.stateUpdateFunction == 'function') {
                            if (mapform.options.stateUpdateFunctionTakesCallback) {
                                updateStateList(stateUpdateCallback);
                            } else {
                                stateUpdateCallback();
                            }
                        } else {
                            stateUpdateCallback();
                        }
                    }
                    
                    if (mapform.options.cityElementId) {
                        $('#' + mapform.options.cityElementId).val(address.city);
                    }
                    
                    if (mapform.options.formattedAddressElementId) {
                        $('#' + mapform.options.formattedAddressElementId).val(address.locationinfo.formatted_address);
                    }
                    if (mapform.options.fullLocationDataElementId) {
                        $('#' + mapform.options.fullLocationDataElementId).val(JSON.stringify(address.locationinfo));
                    }
                }
                if (address) {
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
            if (mapform.options.autocompleteElementId) {
                var addr_autocomplete = new google.maps.places.Autocomplete(
                    $(jqAutocomplete, $('#' + mapform.options.formId))[0],
                    {
                        types: ['(cities)']
                    }
                    );
                google.maps.event.addListener(addr_autocomplete, 'place_changed', function() {
                    mapform.update(mapform.options.autocompleteWillUpdateText);
                });
                $(jqAutocomplete).focus(function () {
                    setTimeout(function () {
                        $(jqAutocomplete).select();
                    }, 200);
                });
            }
            var updateCityCallback = function() {
                if (mapform.options.cityElementId && mapform.options.cityElementId == mapform.options.autocompleteElementId) {
                    $('#' + mapform.options.cityElementId).val('');
                }
            };
            if (mapform.options.countryElementId) {
                $('#' + mapform.options.countryElementId).change(function() {
                    if (mapform.options.stateElementId) {
                        $('#' + mapform.options.stateElementId).val('');
                        $(jqAutocomplete).val(
                            ($('#' + mapform.options.stateElementId).val() ? $('#' + mapform.options.stateElementId).val() + ',': '') 
                            + $('#' + mapform.options.countryElementId).val());
                    } else {
                        $(jqAutocomplete).val($('#' + mapform.options.countryElementId).val());
                    }
                    mapform.update(true, updateCityCallback);
                });
            }
            if (mapform.options.stateElementId) {
                $('#' + mapform.options.stateElementId).change(function() {
                    $(jqAutocomplete).val(
                        ($('#' + mapform.options.stateElementId).val() ? $('#' + mapform.options.stateElementId).val() + ',': '') 
                        + (mapform.options.countryElementId ? $('#' + mapform.options.countryElementId).val() : ''));
                    mapform.update(true, updateCityCallback);
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
try {
    if (typeof updateStateList == 'function') {
        mapform.options.stateUpdateFunction = updateStateList;
    }
} catch (ex) {}
