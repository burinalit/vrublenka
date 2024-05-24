function product_add_location_price() {
    location_id = jQuery("select#product_add_price_location option:checked").val();
    location_name = jQuery("select#product_add_price_location option:checked").text();
    price_type = jQuery( "select#product_add_price_type_location option:checked" ).val();

    price_location_html = '<p class="form-field _regular_price_location_6_field">' +
        '<label for="_' + price_type + '_price_location_' + location_id + '">';

    if (price_type == 'regular') price_location_html = price_location_html + 'Базовая цена';
    if (price_type == 'sale') price_location_html = price_location_html + 'Цена распродажи';

    price_location_html = price_location_html + ' / ' + location_name + '</label>' +
        '<input type="text" class="short wc_input_price" style="" name="_' + price_type + '_price_location_' + location_id + '" ' +
        'id="_' + price_type + '_price_location_' + location_id + '" value="0" placeholder=""></p>';

    jQuery("#product_price_locations").append(price_location_html);

    //jQuery("select#product_add_price_location option:checked").remove();
}

function product_add_location_stock() {
    location_id = jQuery("select#product_add_stock_location option:checked").val();
    location_name = jQuery("select#product_add_stock_location option:checked").text();

    stock_location_html = '<p class="form-field _stock_location_6_field">' +
        '<label for="_stock_location_' + location_id + '">Запасы - ' + location_name + '</label>' +
        '<input type="number" class="short wc_input_stock" style="" name="_stock_location_' + location_id + '" ' +
        'id="_stock_location_' + location_id + '" value="0" placeholder=""></p>';

    jQuery("#product_stock_locations").append(stock_location_html);

    jQuery("select#product_add_stock_location option:checked").remove();
}