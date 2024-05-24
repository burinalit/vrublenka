"use strict";

function WtLocationClass(){
    var self = this;

    var data = {}; // Текущий регион

    /* Получить текущий регион */
    this.getValue = function(type){
        if (self.data === undefined) return false;
        if (self.data[type] === undefined) return false;
        return self.data[type];
    }

    /* Присвоить новый регион в виде массива значений.  */
    this.setValues = function(values, redirect){
        document.body.style.cursor = 'wait';

        self.data = values;

        dataSaveInCookie();

        if (redirect == 'reload') location.reload(true);
        else if (redirect !== undefined) location.href = redirect;
        else document.body.style.cursor = 'auto';
    }

    /* Присвоить новый регион. Присваивая один тип, остальные типы обнуляются. */
    this.setValue = function(name, type, redirect){
        document.body.style.cursor = 'wait';

        self.data = {};

        if (isNumeric(name)){
            self.data['location_id'] = name;
            dataSaveInCookie();
        }else{
            self.data[type] = name;
            dataSaveInCookie();
        }

        if (redirect == 'reload') window.location.reload(true);
        else if (redirect !== undefined) location.href = redirect;
        else document.body.style.cursor = 'auto';
    }

    this.setCountry = function(name, redirect){
        self.setValue(name, 'country', redirect);
    }

    this.setDistrict = function(name, redirect){
        self.setValue(name, 'district', redirect);
    }

    this.setRegion = function(name, redirect){
        self.setValue(name, 'region', redirect);
    }

    this.setCity = function(name, redirect){
        self.setValue(name, 'city', redirect);
    }

    this.setAdministrativeDistrict = function(name, redirect){
        self.setValue(name, 'administrative_district', redirect);
    }

    /* Сохранить значения региона из cookie */
    function dataReloadOfCookie(){
        var data_json = getCookie('wt_geo_data');
        if (data_json === undefined) return false;

        // Устраняем конфликт с плагином "WooCommerce Customer Relationship Manager"
        data_json = decodeURIComponent(data_json);

        self.data = JSON.parse(data_json);
    }

    /* Сохранить текущие значения региона в cookie */
    function dataSaveInCookie(){
        var data_json = JSON.stringify(self.data);
        setCookie('wt_geo_data', data_json, {expires: 3600 * 24 * 7});
    }

    function isNumeric(num){
        return !isNaN(num)
    }

    dataReloadOfCookie();
}

var WtLocation;

jQuery(document).ready(function()
{
    WtLocation = new WtLocationClass();
});

/* Поиск города */
jQuery(document).ready(function(){
    jQuery(document).on("keypress", "#search_location_name", function(){ searchLocation(); });
    jQuery(document).on("click", "#search_location_button", function(){ searchLocation(); });
});

// Поле ввода города для поиска
function searchLocation() {
    var searchLocationName = jQuery("#search_location_name").val();

    if (searchLocationName.length < 2) return;

    jQuery.ajax({
            url: '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'search_location',
                value: searchLocationName,
                data_type: 'object'
            },
            beforeSend: function (xhr) {

            },
            success: function (data) {
                var search_location_result_html = '';

                var cities = JSON.parse(data);

                search_location_result_html += '<div class="' + column_class + '">';

                if (list_tag == 'ul') search_location_result_html += '<ul>';

                for (var key in cities) {
                    /* console.log(cities[key]); */

                    if (list_tag == 'ul') search_location_result_html += '<li>';

                    search_location_result_html += '<a href="https://';
                    search_location_result_html += cities[key].post_name;
                    search_location_result_html += '.' + wt_gt_domain + '/" class="">' + cities[key].post_title + '</a>';

                    if (list_tag == 'ul') search_location_result_html += '</li>';
                }

                if (list_tag == 'ul') search_location_result_html += '</ul>';
                else search_location_result_html += '</div>';

                jQuery("#search_location_result").html(search_location_result_html);
            }
        }
    );
}