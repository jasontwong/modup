<script type="text/javascript">
$(function(){
    var menu = $('.menu'),
        auto_entry = $('.auto-entry', menu),
        control_delete = $('<a href="javascript:;">&times;</a>'),
        items = $('.items', menu);
    // {{{ control_delete
    control_delete
        .click(function(){
            $(this)
                .closest('div')
                .remove();
        });
    // }}}
    // {{{ items
    items
        .sortable()
        .bind ('sortupdate', function(){
            $(this)
                .children()
                .each(function(i){
                    $('input', this)
                        .each(function(){
                            var el = $(this),
                                name = el.attr('name');
                            el.attr('name', name.replace(/\d+/, i));
                        });
                });
        })
        .bind('update', function(){
            $(this)
                .sortable('refresh')
                .children()
                .each(function(){
                    var el = $(this);
                    $('span', el).css('cursor', 'move')
                    if (!$('a', el).length)
                    {
                        el.append(control_delete.clone(true));
                    }
                });
        })
        .trigger('update');
    // }}}
    // {{{ $('> a', auto_entry)
    $('> a', auto_entry)
        .click(function(){
            var el = $(this),
                mentry = el.parent(),
                mitem = $('<div />'),
                mitems = mentry.nextAll('.items'),
                keys = ['slug','name','id'],
                input = $('<input type="hidden" />'),
                key = el.closest('.menu').data('menu');
            mitem
                .html('<span>' + el.text() + ' </span>');
            for (i in keys)
            {
                mitem
                    .append(
                        input
                            .clone()
                            .val(el.data(keys[i].toLowerCase()))
                            .attr('name', 'menu[' + key + '][' + mitems.children().length + '][' + keys[i].toLowerCase() + ']')
                    );
            }
            mitems
                .append(mitem)
                .trigger('update');
        });
    // }}}
});
</script>

<form method="post" action="<?php echo URI_PATH; ?>">

<?php // {{{ info menu ?>
<div class="tabbed">
    <div class="label">Info Menu</div>
    <div data-menu="info" class="menu row">
        <div class="fields">
            <h2>Header</h2>
            <div class="auto-entry">
            <?php foreach ($pages as $entry): ?>
                <a 
                    data-id="<?php echo $entry['entry']['id']; ?>"
                    data-name="<?php echo $entry['entry']['title']; ?>"
                    data-slug="<?php echo $entry['entry']['slug']; ?>" 
                    href="javascript:;"><?php echo $entry['entry']['title']; ?></a>,
            <?php endforeach; ?>
            </div>
            <p>&nbsp;</p>
            <div class="items">
            <?php foreach ($nav['info'] as $k => $item): ?>
                <div>
                    <span><?php echo $item['name']; ?> </span>
                    <?php foreach ($item as $key => $v): ?>
                    <input type="hidden" name="menu[info][<?php echo $k; ?>][<?php echo $key; ?>]" value="<?php echo $v; ?>" />
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div data-menu="footer_info" class="menu row">
        <div class="fields">
            <h2>Footer</h2>
            <div class="auto-entry">
            <?php foreach ($pages as $entry): ?>
                <a 
                    data-id="<?php echo $entry['entry']['id']; ?>"
                    data-name="<?php echo $entry['entry']['title']; ?>"
                    data-slug="<?php echo $entry['entry']['slug']; ?>" 
                    href="javascript:;"><?php echo $entry['entry']['title']; ?></a>,
            <?php endforeach; ?>
            </div>
            <p>&nbsp;</p>
            <div class="items">
            <?php foreach ($nav['footer_info'] as $k => $item): ?>
                <div>
                    <span><?php echo $item['name']; ?> </span>
                    <?php foreach ($item as $key => $v): ?>
                    <input type="hidden" name="menu[footer_info][<?php echo $k; ?>][<?php echo $key; ?>]" value="<?php echo $v; ?>" />
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php // }}} ?>
<?php // {{{ contact menu ?>
<div class="tabbed">
    <div class="label">Contact Menu</div>
    <div data-menu="contact" class="menu row">
        <div class="fields">
            <div class="auto-entry">
            <?php foreach ($pages as $entry): ?>
                <a 
                    data-id="<?php echo $entry['entry']['id']; ?>" 
                    data-name="<?php echo $entry['entry']['title']; ?>"
                    data-slug="<?php echo $entry['entry']['slug']; ?>" 
                    href="javascript:;"><?php echo $entry['entry']['title']; ?></a>,
            <?php endforeach; ?>
            </div>
            <p>&nbsp;</p>
            <div class="items">
            <?php foreach ($nav['contact'] as $k => $item): ?>
                <div>
                    <span><?php echo $item['name']; ?> </span>
                    <?php foreach ($item as $key => $v): ?>
                    <input type="hidden" name="menu[contact][<?php echo $k; ?>][<?php echo $key; ?>]" value="<?php echo $v; ?>" />
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php // }}} ?>
<?php 
// {{{ cats menus 
foreach ($cats as $key => $cat): 
?>
<div class="tabbed">
    <div class="label"><?php echo ucwords($key); ?> Menus</div>
    <div data-menu="<?php echo $key; ?>-1" class="menu row">
        <div class="fields">
            <h2><?php echo ucwords($key); ?> 1</h2>
            <div class="field">
                <input placeholder="Subhead" type="text" class="text" name="menu[subhead][<?php echo $key; ?>-1]" value="<?php echo $nav['subhead'][$key.'-1']; ?>" />
            </div>
            <div class="auto-entry">
            <?php foreach ($entries[$key] as $entry): ?>
                <a 
                    data-id="<?php echo $entry['entry']['id']; ?>" 
                    data-name="<?php echo $entry['entry']['title']; ?>"
                    data-slug="<?php echo $entry['entry']['slug']; ?>" 
                    href="javascript:;"><?php echo $entry['entry']['title']; ?></a>,
            <?php endforeach; ?>
            </div>
            <p>&nbsp;</p>
            <div class="items">
            <?php foreach ($nav[$key.'-1'] as $k => $item): ?>
                <div>
                    <span><?php echo strlen($item['en']) ? $item['en'] : $item['name']; ?> </span>
                    <?php foreach ($item as $x => $v): ?>
                    <input type="hidden" name="menu[<?php echo $key; ?>-1][<?php echo $k; ?>][<?php echo $x; ?>]" value="<?php echo $v; ?>" />
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div data-menu="<?php echo $key; ?>-2" class="menu row">
        <div class="fields">
            <h2><?php echo ucwords($key); ?> 2</h2>
            <div class="field">
                <input placeholder="Subhead" type="text" class="text" name="menu[subhead][<?php echo $key; ?>-2]" value="<?php echo $nav['subhead'][$key.'-2']; ?>" />
            </div>
            <div class="auto-entry">
            <?php foreach ($entries[$key] as $entry): ?>
                <a 
                    data-id="<?php echo $entry['entry']['id']; ?>" 
                    data-name="<?php echo $entry['entry']['title']; ?>"
                    data-slug="<?php echo $entry['entry']['slug']; ?>" 
                    href="javascript:;"><?php echo $entry['entry']['title']; ?></a>,
            <?php endforeach; ?>
            </div>
            <p>&nbsp;</p>
            <div class="items">
            <?php foreach ($nav[$key.'-2'] as $k => $item): ?>
                <div>
                    <span><?php echo strlen($item['en']) ? $item['en'] : $item['name']; ?> </span>
                    <?php foreach ($item as $x => $v): ?>
                    <input type="hidden" name="menu[<?php echo $key; ?>-2][<?php echo $k; ?>][<?php echo $x; ?>]" value="<?php echo $v; ?>" />
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div data-menu="<?php echo $key; ?>-3" class="menu row">
        <div class="fields">
            <h2><?php echo ucwords($key); ?> 3</h2>
            <div class="field">
                <input placeholder="Subhead" type="text" class="text" name="menu[subhead][<?php echo $key; ?>-3]" value="<?php echo $nav['subhead'][$key.'-3']; ?>" />
            </div>
            <div class="auto-entry">
            <?php foreach ($entries[$key] as $entry): ?>
                <a 
                    data-id="<?php echo $entry['entry']['id']; ?>" 
                    data-name="<?php echo $entry['entry']['title']; ?>"
                    data-slug="<?php echo $entry['entry']['slug']; ?>" 
                    href="javascript:;"><?php echo $entry['entry']['title']; ?></a>,
            <?php endforeach; ?>
            </div>
            <p>&nbsp;</p>
            <div class="items">
            <?php foreach ($nav[$key.'-3'] as $k => $item): ?>
                <div>
                    <span><?php echo ake('en', $item) && strlen($item['en']) ? $item['en'] : $item['name']; ?> </span>
                    <?php foreach ($item as $x => $v): ?>
                    <input type="hidden" name="menu[<?php echo $key; ?>-3][<?php echo $k; ?>][<?php echo $x; ?>]" value="<?php echo $v; ?>" />
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div data-menu="<?php echo $key; ?>-4" class="menu row">
        <div class="fields">
            <h2><?php echo ucwords($key); ?> 4</h2>
            <div class="field">
                <input placeholder="Subhead" type="text" class="text" name="menu[subhead][<?php echo $key; ?>-4]" value="<?php echo $nav['subhead'][$key.'-4']; ?>" />
            </div>
            <div class="auto-entry">
            <?php foreach ($entries[$key] as $entry): ?>
                <a 
                    data-id="<?php echo $entry['entry']['id']; ?>" 
                    data-name="<?php echo $entry['entry']['title']; ?>"
                    data-slug="<?php echo $entry['entry']['slug']; ?>" 
                    href="javascript:;"><?php echo $entry['entry']['title']; ?></a>,
            <?php endforeach; ?>
            </div>
            <p>&nbsp;</p>
            <div class="items">
            <?php foreach ($nav[$key.'-4'] as $k => $item): ?>
                <div>
                    <span><?php echo strlen($item['en']) ? $item['en'] : $item['name']; ?> </span>
                    <?php foreach ($item as $x => $v): ?>
                    <input type="hidden" name="menu[<?php echo $key; ?>-4][<?php echo $k; ?>][<?php echo $x; ?>]" value="<?php echo $v; ?>" />
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php 
endforeach;
// }}} 
?>

<div class="controls">
    <button type="submit">Submit</button>
</div>

</form>
