(function ($) {
    $(".carsearch-form select, .tiresearch-form select").ready(function () {
        console.log('ready');
        $(".carsearch-form select")
            .selectToAutocomplete();

        $(".tiresearch-form select")
            .selectToAutocomplete();

        $('.carsearch-form, .tiresearch-form')
            .on('focus', '.ui-autocomplete-input', function () {
                $(this).data()
                    .uiAutocomplete
                    .search($(this).val());
            });

        $(document)
            .on('change', '.carsearch-form #car-make', function () {
                if (!$(this).val())
                    return;

                $('.carsearch-form #car-model').parent('fieldset').addClass('loading');
//, CarMakeID: $(this).val()
                 $.ajax({
                     url: 'https://'  +  window.location.hostname  + '/wp-admin/admin-ajax.php',
                     data: {action: "my_user_vote", CarMakeID: $(this).val()},
                     dataType: "json",
                           success: function(models) {
                               
                        if (!models.length)
                            return;

                         var carModels = "<option value=''>Изберете модел</option>";

                         for (var i in models) {
                            carModels += '<option value="' + models[i]['CarModelID'] + '">' + models[i]['CarModel'] + '</option>';
                        }

                         $('.carsearch-form #car-model')
                            .html(carModels)
                             .selectToAutocomplete('destroy')
                            .selectToAutocomplete('init')
                            .parent('fieldset')
                            .removeClass('loading');

                     $('.carsearch-form #car-model').siblings('.ui-autocomplete-input').trigger('focus');
                     
                    }
                 })
                
            })
            .on('change', '.carsearch-form #car-model', function () {
                if (!$(this).val())
                    return;

                $('.carsearch-form #car-year').parent('fieldset').addClass('loading');

                // $.ajax({
                //     method: "POST",
                //     url: "http://www.pitlane.bg/bg/pitlane/ajax/carsearch/",
                //     data: { CarModelID: $(this).val() },
                //     dataType: "json"
                // })
                //     .done(function (carYear) {
                //         if (!carYear.length)
                //             return;
                var carYear = [{ YearFrom: 2000 }]

                var carYears = "<option value=''>Изберете година</option>";

                for (var i = (new Date()).getFullYear(); i >= carYear[0]['YearFrom']; i--) {
                    carYears += '<option value="' + i + '">' + i + '</option>';
                }

                $('.carsearch-form #car-year')
                    .html(carYears)
                    .selectToAutocomplete('destroy')
                    .selectToAutocomplete('init')
                    .parent('fieldset')
                    .removeClass('loading');

                $('.carsearch-form #car-year').siblings('.ui-autocomplete-input').trigger('focus');
                // });
            })
    
            .on('change', '.carsearch-form #car-year', function () {
                if (!$(this).val())
                    return;

                $('.carsearch-form #car-modification').parent('fieldset').addClass('loading');

                  $.ajax({
                     url: 'https://'  +  window.location.hostname  + '/wp-admin/admin-ajax.php',
                     data: {action: "car_modification", CarModelID: $('.carsearch-form #car-model').val(), CarYear: $(this).val()},
                     dataType: "json",
                           success: function(modifications) {
                                if (!modifications.length)
                            return;

                        var carModification = "<option value=''>Изберете двигател</option>";

                        for (var i in modifications) {
                            carModification += '<option value="' + modifications[i]['CarModificationID'] + '">' + modifications[i]['Modification'] + '</option>';
                        }

                        $('.carsearch-form #car-modification')
                            .html(carModification)
                            .selectToAutocomplete('destroy')
                            .selectToAutocomplete('init')
                            .parent('fieldset')
                            .removeClass('loading');

                        $('.carsearch-form #car-modification').siblings('.ui-autocomplete-input').trigger('focus');
                        
                         }
                  });
               /* $.ajax({
                    method: "POST",
                    url: "http://www.pitlane.bg/bg/pitlane/ajax/carsearch/",
                    data: { CarModelID: $('.carsearch-form #car-model').val(), CarYear: $(this).val() },
                    dataType: "json"
                })
                    .done(function (modifications) {
                        if (!modifications.length)
                            return;

                        var carModification = "<option value=''>Изберете двигател</option>";

                        for (var i in modifications) {
                            carModification += '<option value="' + modifications[i]['CarModificationID'] + '">' + modifications[i]['Modification'] + '</option>';
                        }

                        $('.carsearch-form #car-modification')
                            .html(carModification)
                            .selectToAutocomplete('destroy')
                            .selectToAutocomplete('init')
                            .parent('fieldset')
                            .removeClass('loading');

                        $('.carsearch-form #car-modification').siblings('.ui-autocomplete-input').trigger('focus');
                    });*/
            })
            .on('change', '.carsearch-form #car-modification', function () {
                if (!$(this).val())
                    return;

                $('.carsearch-form #tire-sizes').parent('fieldset').addClass('loading');

                     $.ajax({
                     url: 'https://'  +  window.location.hostname  + '/wp-admin/admin-ajax.php',
                     data: {action: "car_tyres", CarModificationID: $('.carsearch-form #car-modification').val()},
                     dataType: "json",
                           success: function(tireSizes) {
                                if (!tireSizes.length)
                            return;

                        var tireSize = "<option value=''>Изберете размер</option>";

                        for (var i in tireSizes) {
                            tireSize += '<option value="' + tireSizes[i]['TyreSize'] + '">' + tireSizes[i]['TyreSize'] + '</option>';
                        }

                        $('.carsearch-form #tire-sizes')
                            .html(tireSize)
                            .selectToAutocomplete('destroy')
                            .selectToAutocomplete('init')
                            .parent('fieldset')
                            .removeClass('loading');

                        $('.carsearch-form #tire-sizes').siblings('.ui-autocomplete-input').trigger('focus');
                           }
                  });
                 /* 
                $.ajax({
                    method: "POST",
                    url: "http://www.pitlane.bg/bg/pitlane/ajax/carsearch/",
                    data: { CarModificationID: $('.carsearch-form #car-modification').val() },
                    dataType: "json"
                })
                    .done(function (tireSizes) {
                        if (!tireSizes.length)
                            return;

                        var tireSize = "<option value=''>Изберете размер</option>";

                        for (var i in tireSizes) {
                            tireSize += '<option value="' + tireSizes[i]['TyreSize'] + '">' + tireSizes[i]['TyreSize'] + '</option>';
                        }

                        $('.carsearch-form #tire-sizes')
                            .html(tireSize)
                            .selectToAutocomplete('destroy')
                            .selectToAutocomplete('init')
                            .parent('fieldset')
                            .removeClass('loading');

                        $('.carsearch-form #tire-sizes').siblings('.ui-autocomplete-input').trigger('focus');
                    });*/
            })
            .on('change', '.carsearch-form #tire-sizes', function () {
                $('.carsearch-form .btn[disabled]').addClass('primary').prop('disabled', '');
            });

        function carTireSearch(e) {
            e.preventDefault();

            var tireSizeRegex = /(\d{3}|\d{1,2}.\d{1,2})[\/X-]*(\d{2})*\s*R(\d{2})/g;
            var tireSize = tireSizeRegex.exec($('.carsearch-form  #tire-sizes').val());

            if (tireSize.length) {
                var paramData = $(":input", this)
                .filter(function () {
                    return $.trim($(this).val()).length > 0 && $(this).val() != 0;
                }).serialize();


                window.location = "/porachka?tire_width=" + tireSize[1] + "&tire_profile=" + (tireSize[2] ? tireSize[2] : 82) + "&rim_size=" + tireSize[3] + paramData;
            }
        }

        $(document)
            .on('submit', '.carsearch-form', carTireSearch)
            //.on('click', '.carsearch-form .btn.primary', carTireSearch);

        $('.tiresearch-form').on('submit', function (e) {
            e.preventDefault();
            var paramData = $(":input", this)
                .filter(function () {
                    return $.trim($(this).val()).length > 0 && $(this).val() != 0;
                }).serialize();

            window.location = "/porachka?" + paramData;
        });

        $('.radio-group input:radio').on('click change', function () {
            $(this)
                .parents('.radio-group')
                .find('label.active')
                .removeClass('active')
                .end()
                .find('label[for=' + $(this).attr('id') + ']')
                .addClass('active');
        });

        $('.radio-group input:radio:checked').each(function () {
            $(this)
                .parents('.radio-group')
                .find('label.active')
                .removeClass('active')
                .end()
                .find('label[for=' + $(this).attr('id') + ']')
                .addClass('active');
        });

        $('.options-group input:checkbox').on('change', function () {
            $(this)
                .parents('.options-group')
                .find('label[for=' + $(this).attr('id') + ']')
                .toggleClass('active');
        });

        function fetchTireSizes(fetch, selector, width, profile, rim, speed) {
            $.ajax({
                method: "POST",
                url: "http://www.pitlane.bg/bg/pitlane/ajax/tiresearch/",
                data: {
                    'fetch': fetch,
                    'TireWidth': width,
                    'TireProfile': profile,
                    'RimSize': rim,
                    'SpeedIndex': speed
                },
                dataType: "json"
            })
                .done(function (data) {
                    var items = '<option value="">Всички</option>';

                    for (var i in data) {
                        items += '<option value="' + data[i]['value'] + '">' + data[i]['value'] + '</option>';
                    }

                    $(selector).html(items);
                });
        }
    });
})(jQuery);
