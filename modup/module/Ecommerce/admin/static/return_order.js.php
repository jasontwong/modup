$(function(){
    var order_state = $('.order-state'),
        order_country = $('.order-country'),
        order_weight = $('.order-weight'),
        order_subtotal = $('.order-subtotal input'),
        order_tax = $('.order-tax input'),
        order_total = $('.order-total input'),
        order_discount = $('.order-discount input'),
        order_gc_discount = $('.order-gc-discount input'),
        discounted_subtotal = $('.discounted-subtotal'),
        product_add = $('.product-add'),
        product_index = $('.index', product_add),
        product_qty = $('.qty', product_add),
        product_select = $('.select', product_add),
        product_items = $('.product-item'),
        product_item = product_items.first(),
        form = $('form'),
        tax_rate = 0.0875;
    order_discount
        .focusout(function(){
            var discount = parseFloat(order_discount.val()),
                subtotal = parseFloat(order_subtotal.val()) + discount + parseFloat(order_gc_discount.val());
                total = subtotal + parseFloat(order_tax.val());
            order_total.val(total.toFixed(2));
            order_discount.val(discount.toFixed(2));
            discounted_subtotal.text('$' + subtotal.toFixed(2));
        });
    $('.subtract-quantity', product_items)
        .each(function(){
            var el = $(this);
            el.text('Subtract');
        })
        .click(function(){
            var el = $(this),
                item = el.closest('.product-item'),
                quantity = $('.product-quantity', item),
                tax = $('.product-tax', item),
                price = $('.product-price', item),
                total = $('.product-total', item),
                weight = $('.product-weight', item),
                inputs = $('input', item),
                quantity_input = $('input', quantity),
                tax_input = $('input', tax),
                price_input = $('input', price),
                total_input = $('input', total),
                weight_input = $('input', weight),
                o_quantity_val = Math.abs(parseFloat(quantity_input.val())),
                o_tax_val = parseFloat(tax_input.val()),
                o_price_val = parseFloat(price_input.val()),
                o_total_val = parseFloat(total_input.val()),
                o_weight_val = parseFloat(weight_input.val()),
                n_quantity_val = -(o_quantity_val - 1),
                n_tax_val = 0,
                n_total_val = 0,
                n_weight_val = 0,
                value = 0;
            n_tax_val = o_tax_val - (o_tax_val / o_quantity_val);
            n_total_val = o_total_val - (o_total_val / o_quantity_val);
            n_weight_val = o_weight_val - (o_weight_val / o_quantity_val);
            value = parseFloat($('input', order_weight).val()) - n_weight_val;
            $('input', order_weight).val(value);
            value = parseFloat(order_tax.val()) - n_tax_val;
            order_tax.val(value.toFixed(2));
            value = parseFloat(order_total.val()) - n_total_val;
            order_total.val(value.toFixed(2));
            value = parseFloat(order_subtotal.val()) + o_price_val;
            order_subtotal.val(value.toFixed(2));
            value = value > 0
                ? value - (parseFloat(order_discount.val()) + parseFloat(order_gc_discount.val()))
                : value + (parseFloat(order_discount.val()) + parseFloat(order_gc_discount.val()));
            discounted_subtotal.text('$' + value.toFixed(2));
            $('span', quantity).text(n_quantity_val);
            $('span', tax).text('$' + n_tax_val.toFixed(2));
            $('span', total).text('$' + n_total_val.toFixed(2));
            $('span', weight).text(n_weight_val.toFixed(2));
            quantity_input.val(n_quantity_val);
            tax_input.val(n_tax_val);
            total_input.val(n_total_val);
            weight_input.val(n_weight_val);
            if (n_quantity_val == 0)
            {
                el.prev('.omit').remove();
                el.remove();
            }
        });
    $('.omit', product_items)
        .each(function(){
            var el = $(this);
            el.text('Omit');
        })
        .click(function(){
            var el = $(this),
                item = el.closest('.product-item'),
                quantity = $('.product-quantity', item),
                tax = $('.product-tax', item),
                price = $('.product-price', item),
                total = $('.product-total', item),
                weight = $('.product-weight', item),
                inputs = $('input', item),
                quantity_input = $('input', quantity),
                tax_input = $('input', tax),
                price_input = $('input', price),
                total_input = $('input', total),
                weight_input = $('input', weight),
                value = 0;
            value = parseFloat($('input', order_weight).val()) - parseFloat(weight_input.val());
            $('input', order_weight).val(value);
            value = parseFloat(order_tax.val()) - parseFloat(tax_input.val());
            order_tax.val(value.toFixed(2));
            value = parseFloat(order_total.val()) - parseFloat(total_input.val());
            order_total.val(value.toFixed(2));
            value = parseFloat(order_subtotal.val()) + (parseFloat(price_input.val()) * Math.abs(parseFloat(quantity_input.val())));
            order_subtotal.val(value.toFixed(2));
            value = value > 0
                ? value - (parseFloat(order_discount.val()) + parseFloat(order_gc_discount.val()))
                : value + (parseFloat(order_discount.val()) + parseFloat(order_gc_discount.val()));
            discounted_subtotal.text('$' + value.toFixed(2));
            inputs.val('0');
            $('span', quantity).text('0');
            $('span', tax).text('$' + parseFloat(tax_input.val()).toFixed(2));
            $('span', total).text('$' + parseFloat(total_input.val()).toFixed(2));
            $('span', weight).text(parseFloat(weight_input.val()).toFixed(2));
            el.next('.subtract-quantity').remove();
            el.remove();
        });
    product_add
        .each(function(){
            var link = $('<a href="javascript:;">Add</a>');
            link.click(function(){
                var qty = parseInt(product_qty.val());
                if (!isNaN(qty) && product_select.val().length)
                {
                    var item = product_item.clone(),
                        option = $(':selected', product_select);
                    $('.omit', item).remove();
                    $('input', item)
                        .each(function(){
                            var input = $(this),
                                name = input.attr('name'),
                                number, col;
                            new_name = name.replace(/(\w+\[\w+\]\[)\d+(\].*)/, '$1' + product_index.val() + '$2');
                            input.attr('name', new_name);
                            if (name.search('Options') == -1)
                            {
                                if (name.search('name') > -1)
                                {
                                    col = $('.product-name', item);
                                    $('.title', col).text(option.data('name'));
                                    $('.color', col).text('color: ' + option.data('color'));
                                    $('.sku', col).text('SKU: ' + option.data('sku'));
                                    $('.size', col).text('size: ' + option.data('size'));
                                    input.val(option.data('name'));
                                }
                                else if (name.search('sku') > -1)
                                {
                                    input.val(option.data('sku'));
                                    input.prev('span').text(option.data('sku'));
                                }
                                else if (name.search('weight') > -1)
                                {
                                    number = option.data('weight');
                                    input.val(number.toFixed(2));
                                    input.prev('span').text(number.toFixed(2));
                                    number = parseFloat(order_weight.val()) + (number * qty);
                                    order_weight.val(number.toFixed(2));
                                }
                                else if (name.search('price') > -1)
                                {
                                    number = option.data('price');
                                    input.val(number.toFixed(2));
                                    input.prev('span').text('$' + number.toFixed(2));
                                }
                                else if (name.search('quantity') > -1)
                                {
                                    input.val(qty);
                                    input.prev('span').text(qty);
                                }
                                else if (name.search('shipping') > -1)
                                {
                                    input.val(0);
                                    input.prev('span').text('$' + 0.00);
                                }
                                else if (name.search('discount') > -1)
                                {
                                    input.val(0);
                                    input.prev('span').text('$' + 0.00);
                                }
                                else if (name.search('tax') > -1)
                                {
                                    number = 0;
                                    if (order_country.text() == 'US' && order_state.text() == 'CA')
                                    {
                                        number = parseFloat(option.data('price')) * qty * tax_rate;
                                    }
                                    input.val(number.toFixed(2));
                                    input.prev('span').text('$' + number.toFixed(2));
                                    number = parseFloat(order_tax.val()) + number;
                                    order_tax.val(number.toFixed(2));
                                }
                                else if (name.search('total') > -1)
                                {
                                    number = parseFloat(order_subtotal.val()) + (parseFloat(option.data('price')) * qty);
                                    order_subtotal.val(number.toFixed(2));
                                    number = number > 0
                                        ? number - (parseFloat(order_discount.val()) + parseFloat(order_gc_discount.val()))
                                        : number + (parseFloat(order_discount.val()) + parseFloat(order_gc_discount.val()));
                                    discounted_subtotal.text('$' + number.toFixed(2));
                                    number = 0;
                                    if (order_country.text() == 'US' && order_state.text() == 'CA')
                                    {
                                        number = parseFloat(option.data('price')) * qty * tax_rate;
                                    }
                                    number = (parseFloat(option.data('price')) * qty) + number;
                                    input.val(number.toFixed(2));
                                    input.prev('span').text('$' + number.toFixed(2));
                                    number = parseFloat(order_total.val()) + number;
                                    order_total.val(number.toFixed(2));
                                }
                            }
                            else
                            {
                                if (input.val() == 'color')
                                {
                                    input.next().val(option.data('color'));
                                }
                                else if (input.val() == 'size')
                                {
                                    input.next().val(option.data('size'));
                                }
                                else if (input.val() == 'image')
                                {
                                    input.next().val(option.data('image'));
                                }
                            }
                        });
                    product_index.val(parseInt(product_index.val()) + 1);
                    product_add.closest('tr').before(item);
                    product_qty.val('');
                    product_select.val('');
                }
            });
            $(this).append(link);
        });
    form
        .submit(function(){
            $('input', this).removeAttr('disabled');
        });
});
