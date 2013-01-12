<script type="text/javascript">
$(function(){
    var type = $('input#type'),
        gift_cards = $('.gift-card-types');
    $('li', gift_cards)
        .hover(function(){
            $('div', this).show();
        }, function(){
            $('div', this).hide();
        });
    $('a', gift_cards)
        .click(function(){
            var el = $(this);
            $.post('/admin/rpc/Ecommerce/gift_card/', { action: 'delete', id: el.data('id') }, function(data) {
                if (data.success)
                {
                    el.parent().remove();
                }
            }, 'json');
        });
});
</script>

<style>
    .gift-card-types li { font-size: 1.1em; position: relative; }
    .gift-card-types div { background-color: #EEE; border: 1px solid #000; bottom: 15px; display: none; left: 15px; padding: 5px; position: absolute; }
</style>

<form method="POST">
    <div class="group">
        <div class="row">
            <div class="label">Code</div>
            <div class="field">
                <input class="text" name="gift_card[code]" type="text" value="" />
            </div>
        </div>
        <div class="row">
            <div class="label">Amount</div>
            <div class="field">
                <input class="text" name="gift_card[amount]" placeholder="0.00" type="text" value="" />
            </div>
        </div>
        <div class="row">
            <div class="label">Balance</div>
            <div class="field">
                <input class="text" name="gift_card[balance]" placeholder="0.00" type="text" value="" />
            </div>
        </div>
        <div class="row">
            <div class="label">Max Uses</div>
            <div class="field">
                <input class="text" name="gift_card[uses]" placeholder="-1" type="text" value="" />
            </div>
        </div>
        <div class="row">
            <div class="label">End Date</div>
            <div class="field">
                <input class="text date" name="gift_card[end_date]" type="text" value="" />
            </div>
        </div>
        <div class="row">
            <div class="field">
                <button type="submit">Submit</button>
            </div>
        </div>
    </div>
    <div class="group">
        <div class="row">
            <div class="label">Available Gift Cards</div>
            <div class="field">
                <ul id="available" class="gift-card-types">
                <?php
                $gift_cards['available'] = Ecommerce::get_available_gift_cards();
                $tmp = array();
                foreach ($gift_cards['available'] as $gift_card)
                {
                        $amount = '$'.$gift_card['amount'];
                        echo '<li>'.$gift_card['code'].' - '.$amount.' <a href="javascript:;" data-id="'.$gift_card['id'].'">&times;</a><div><p>Uses: '.$gift_card['uses'].'<br />End Date: '.date('Y-m-d', $gift_card['end_date']).'<br />Balance: '.$gift_card['balance'].'<br /></p></div></li>';
                }
                ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="group">
        <div class="row">
            <div class="label">Expired Gift Cards</div>
            <div class="field">
                <ul id="expired" class="gift-card-types">
                <?php
                $gift_cards['expired'] = Ecommerce::get_expired_gift_cards();
                foreach ($gift_cards['expired'] as $gift_card)
                {
                        $amount = '$'.$gift_card['amount'];
                        echo '<li>'.$gift_card['code'].' - '.$amount.' <a href="javascript:;" data-id="'.$gift_card['id'].'">&times;</a><div><p>Uses: '.$gift_card['uses'].'<br />End Date: '.date('Y-m-d', $gift_card['end_date']).'<br />Balance: '.$gift_card['balance'].'<br /></p></div></li>';
                }

                ?>
                </ul>
            </div>
        </div>
    </div>
</form>
