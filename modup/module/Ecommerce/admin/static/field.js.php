$(function(){
    // {{{ $('.EcommerceAddressCountry')
    $('.EcommerceAddressCountry')
        .each(function(){
            var el = $(this),
                state = $('.EcommerceAddressState', el.closest('.fields')),
                state_select = $('<select />'),
                province_select = $('<select />');
            <?php foreach (Ecommerce::get_us_states() as $k => $v): ?>
            state_select
                .append("<option value='<?php echo $k; ?>'><?php echo $v; ?></option>");
            <?php endforeach; ?>
            <?php foreach (Ecommerce::get_ca_provinces() as $k => $v): ?>
            province_select
                .append("<option value='<?php echo $k; ?>'><?php echo $v; ?></option>");
            <?php endforeach; ?>
            el.data('state_text', state.clone())
                .data('state_select', state_select.addClass('EcommerceAddressState').attr('name', state.attr('name')).clone())
                .data('province_select', province_select.addClass('EcommerceAddressState').attr('name', state.attr('name')).clone());
        })
        .change(function(){
            var el = $(this),
                state = $('.EcommerceAddressState', el.closest('.fields'));
            if (el.val() === 'US')
            {
                state.replaceWith(el.data('state_select').clone());
            }
            else if (el.val() === 'CA')
            {
                state.replaceWith(el.data('province_select').clone());
            }
            else
            {
                state.replaceWith(el.data('state_text').clone());
            }
        });
    // }}}
    // {{{ $('.EcommerceProductTotal')
    $('.EcommerceProductTotal')
        .each(function(){
            var calculator = $('<a href="javascript:;">Calculate Total</a>');
            calculator
                .click(function(){
                    var el = $(this),
                        fields = el.closest('.fields'),
                        price = $('.EcommerceProductPrice', fields),
                        quantity = $('.EcommerceProductQuantity', fields),
                        discount = $('.EcommerceProductDiscount', fields),
                        tax = $('.EcommerceProductTax', fields),
                        shipping = $('.EcommerceProductShipping', fields),
                        price_amt = price.length && !isNaN(parseFloat(price.val())) ? parseFloat(price.val()) : 0,
                        quantity_amt = quantity.length && !isNaN(parseInt(quantity.val())) ? parseInt(quantity.val()) : 1,
                        discount_amt = discount.length && !isNaN(parseFloat(discount.val())) ? parseFloat(discount.val()) : 0,
                        tax_amt = tax.length && !isNaN(parseFloat(tax.val())) ? parseFloat(tax.val()) : 0,
                        shipping_amt = shipping.length && !isNaN(parseFloat(shipping.val())) ? parseFloat(shipping.val()) : 0,
                        total = (price_amt * quantity_amt - discount_amt) + tax_amt + shipping_amt;
                    el.prev('.EcommerceProductTotal')
                        .val(total.toFixed(2));
                });
            $(this).after(calculator);
        });
    // }}}
    // {{{ $('.EcommerceOrderTotal')
    $('.EcommerceOrderTotal')
        .each(function(){
            var calculator = $('<a href="javascript:;">Calculate Total</a>');
            calculator
                .click(function(){
                    var el = $(this),
                        group = el.closest('.group'),
                        price = $('.EcommerceOrderSubtotal', group),
                        discount = $('.EcommerceOrderDiscount', group),
                        gift_card_discount = $('.EcommerceOrderGiftCardDiscount', group),
                        tax = $('.EcommerceOrderTax', group),
                        shipping = $('.EcommerceOrderShipping', group),
                        price_amt = price.length && !isNaN(parseFloat(price.val())) ? parseFloat(price.val()) : 0,
                        discount_amt = discount.length && !isNaN(parseFloat(discount.val())) ? parseFloat(discount.val()) : 0,
                        gift_card_discount_amt = gift_card_discount.length && !isNaN(parseFloat(gift_card_discount.val())) ? parseFloat(gift_card_discount.val()) : 0,
                        tax_amt = tax.length && !isNaN(parseFloat(tax.val())) ? parseFloat(tax.val()) : 0,
                        shipping_amt = shipping.length && !isNaN(parseFloat(shipping.val())) ? parseFloat(shipping.val()) : 0,
                        total = (price_amt - (discount_amt + gift_card_discount_amt)) + tax_amt + shipping_amt;
                    el.prev('.EcommerceOrderTotal')
                        .val(total.toFixed(2));
                });
            $(this).after(calculator);
        });
    // }}}
    // {{{ $('.EcommerceOrderSubtotal')
    $('.EcommerceOrderSubtotal')
        .each(function(){
            var calculator = $('<a href="javascript:;">Calculate Subtotal</a>');
            calculator
                .click(function(){
                    var el = $(this),
                        price = $('.EcommerceProductPrice'),
                        subtotal = 0;
                    price
                        .each(function(){
                            var price = $(this),
                                value = price.val(),
                                qty = $('.EcommerceProductQuantity' , price.closest('.fields')).val();
                            if (!isNaN(parseFloat(value)) && !isNaN(parseFloat(qty)))
                            {
                                subtotal += parseFloat(value * qty);
                            }
                        });
                    el.prev('.EcommerceOrderSubtotal').val(subtotal.toFixed(2));
                });
            $(this).after(calculator);
        });
    // }}}
    // {{{ $('.EcommerceOrderTax')
    $('.EcommerceOrderTax')
        .each(function(){
            var calculator = $('<a href="javascript:;">Calculate Tax</a>');
            calculator
                .click(function(){
                    var el = $(this),
                        price = $('.EcommerceProductTax'),
                        subtotal = 0;
                    price
                        .each(function(){
                            var value = $(this).val();
                            if (!isNaN(parseFloat(value)))
                            {
                                subtotal += parseFloat(value);
                            }
                        });
                    el.prev('.EcommerceOrderTax').val(subtotal.toFixed(2));
                });
            $(this).after(calculator);
        });
    // }}}
    // {{{ $('.EcommerceOrderShipping')
    $('.EcommerceOrderShipping')
        .each(function(){
            var calculator = $('<a href="javascript:;">Calculate Shipping</a>');
            calculator
                .click(function(){
                    var el = $(this),
                        price = $('.EcommerceProductShipping'),
                        subtotal = 0;
                    price
                        .each(function(){
                            var value = $(this).val();
                            if (!isNaN(parseFloat(value)))
                            {
                                subtotal += parseFloat(value);
                            }
                        });
                    el.prev('.EcommerceOrderShipping').val(subtotal.toFixed(2));
                });
            $(this).after(calculator);
        });
    // }}}
});
