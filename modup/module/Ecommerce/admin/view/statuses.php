<script type="text/javascript">
$(function(){
    var type = $('input#type'),
        statuses = $('#status-types');
    $('> li > a', statuses)
        .click(function(){
            type.val($(this).text());
        });
    $('> li > ul > li > a', statuses)
        .click(function(){
            var el = $(this),
                list = el.closest('ul');
            $.post('/admin/rpc/Ecommerce/status/', { action: 'delete', id: el.data('id') }, function(data) {
                if (data.success)
                {
                    el.parent().remove();
                    if (!list.children().length)
                    {
                        list.parent().prev('li').remove();
                        list.parent().remove();
                    }
                }
            }, 'json');
        });
});
</script>

<style>
    #status-types li { font-size: 1.1em; position: relative; }
</style>

<form method="POST">
    <div class="group">
        <div class="row">
            <div class="label">Type</div>
            <div class="field">
                <input class="text" id="type" name="status[type]" type="text" value="" />
            </div>
        </div>
        <div class="row">
            <div class="label">Name</div>
            <div class="field">
                <input class="text" name="status[name]" type="text" value="" />
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
            <div class="label">Available Statuses</div>
            <div class="field">
                <ul id="status-types">
                <?php
                $statuses = EcommerceAPI::get_statuses();
                $tmp = array();
                foreach ($statuses as $status)
                {
                    $tmp[$status['type']][$status['id']] = $status['name'];
                }

                foreach ($tmp as $type => $stats)
                {
                    echo '<li><a href="javascript:;">'.$type.'</a></li>';
                    echo '<li><ul>';
                    foreach ($stats as $id => $stat)
                    {
                        echo '<li>'.$stat.' <a href="javascript:;" data-id="'.$id.'">&times;</a></li>';
                    }
                    echo '</ul></li>';
                }
                ?>
                </ul>
            </div>
        </div>
    </div>
</form>
