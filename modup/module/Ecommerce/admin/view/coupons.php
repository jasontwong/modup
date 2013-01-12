<script type="text/javascript">
$(function(){
    var type = $('input#type'),
        coupons = $('.coupon-types');
    $('li', coupons)
        .hover(function(){
            $('div', this).show();
        }, function(){
            $('div', this).hide();
        });
    $('a.delete', coupons)
        .click(function(){
            var el = $(this);
            $.post('/admin/rpc/Ecommerce/coupon/', { action: 'delete', id: el.data('id') }, function(data) {
                if (data.success)
                {
                    el.parent().remove();
                }
            }, 'json');
        });
});
</script>

<style>
    .coupon-types li { font-size: 1.1em; position: relative; }
    .coupon-types div { background-color: #EEE; border: 1px solid #000; bottom: 15px; display: none; left: 15px; padding: 5px; position: absolute; }
</style>

<form method="POST">
    <div class="group">
        <div class="row">
            <div class="label">Code</div>
            <div class="field">
                <input class="text" name="coupon[code]" type="text" value="" /></label>
            </div>
        </div>
        <div class="row">
            <div class="label">Type</div>
            <div class="field">
                <select class="select" name="coupon[type]">
                    <option value="amount">Amount ($)</option>
                    <option value="rate">Rate (%)</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="label">Amount</div>
            <div class="field">
                <input class="text" name="coupon[amount]" placeholder="0.00" type="text" value="" />
            </div>
        </div>
        <div class="row">
            <div class="label">Qualifying Amount</div>
            <div class="field">
                <input class="text" name="coupon[qualifier]" placeholder="0.00" type="text" value="" />
            </div>
        </div>
        <div class="row">
            <div class="field">
                <label><input class="checkbox" name="coupon[free_shipping]" type="hidden" value="0" /><input name="coupon[free_shipping]" type="checkbox" value="1" /> Free Shipping?</label>
            </div>
        </div>
        <div class="row">
            <div class="label">Max uses</div>
            <div class="field">
                <input class="text" name="coupon[uses]" placeholder="-1" type="text" value="" />
            </div>
        </div>
        <div class="row">
            <div class="label">Date</div>
            <div class="field">
                <label>Start: <input class="date text" name="coupon[start_date]" type="text" value="" /></label>
                <label>End: <input class="date text" name="coupon[end_date]" type="text" value="" /></label>
            </div>
        </div>
        <div class="row">
            <div class="field">
                <button>Submit</button>
            </div>
        </div>
    </div>
    <div class="group">
        <div class="row">
            <div class="label">Available Coupons</div>
            <div class="field">
                <ul id="available" class="coupon-types">
                <?php
                $coupons['available'] = Ecommerce::get_available_coupons();
                $tmp = array();
                foreach ($coupons['available'] as $coupon)
                {
                        $amount = '';
                        if ($coupon['type'] === 'amount')
                        {
                            $amount = '$'.$coupon['amount'];
                        }
                        if ($coupon['type'] === 'rate')
                        {
                            $amount = $coupon['amount'].'%';
                        }
                        echo '<li>'.$coupon['code'].' - '.$amount.' <a class="delete" href="javascript:;" data-id="'.$coupon['id'].'">&times;</a> <a href="/admin/module/Ecommerce/coupon_lookup/?code='.$coupon['code'].'">Order Lookup</a><div><p>Uses: '.$coupon['uses'].'<br />Start Date: '.date('Y-m-d', $coupon['start_date']).'<br />End Date: '.date('Y-m-d', $coupon['end_date']).'<br />Free Shipping: '.$coupon['free_shipping'].'<br />Min. Amount: '.$coupon['qualifier'].'<br /></p></div></li>';
                }
                ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="group">
        <div class="row">
            <div class="label">Expired Coupons</div>
            <div class="field">
                <ul id="expired" class="coupon-types">
                <?php
                $coupons['expired'] = Ecommerce::get_expired_coupons();
                foreach ($coupons['expired'] as $coupon)
                {
                        $amount = '';
                        if ($coupon['type'] === 'amount')
                        {
                            $amount = '$'.$coupon['amount'];
                        }
                        if ($coupon['type'] === 'rate')
                        {
                            $amount = $coupon['amount'].'%';
                        }
                        echo '<li>'.$coupon['code'].' - '.$amount.' <a class="delete" href="javascript:;" data-id="'.$coupon['id'].'">&times;</a> <a href="/admin/module/Ecommerce/coupon_lookup/?code='.$coupon['code'].'">Order Lookup</a><div><p>Uses: '.$coupon['uses'].'<br />Start Date: '.date('Y-m-d', $coupon['start_date']).'<br />End Date: '.date('Y-m-d', $coupon['end_date']).'<br />Free Shipping: '.$coupon['free_shipping'].'<br />Min. Amount: '.$coupon['qualifier'].'<br /></p></div></li>';
                }

                ?>
                </ul>
            </div>
        </div>
    </div>
</form>
