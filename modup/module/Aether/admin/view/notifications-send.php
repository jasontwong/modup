<script type="text/javascript">
$(function(){
    var emails = $('.note-emails');
    $('a', emails)
        .click(function(){
            var el = $(this),
                info = { 
                    id: '<?php echo URI_PART_4; ?>',
                    emails: el.data('email')
                };
            $.post('/admin/rpc/Aether/update_notification/', { action: 'remove', notification: info }, function(data) {
                if (data.success)
                {
                    el.parent().remove();
                }
            }, 'json');
        });
});
</script>

<p>Product Name: <?php echo $note['product']['name'] ?></p>
<p>Color: <?php echo $note['color']['name'] ?></p>
<p>Size: <?php echo $note['size']['name'] ?></p>
<p>Emails:</p>
<ul class="note-emails">
    <?php foreach ($note['emails'] as $email): ?>
        <li><?php echo hsc($email) ?> <a href="javascript:;" data-email="<?php echo hsc($email); ?>">Delete</a></li>
    <?php endforeach ?>
</ul>

<br>

<form method='post' action='<?php echo URI_PATH ?>'>
    <div>
        <input type='hidden' name='do' value='send'>
        <input type='hidden' name='id' value='<?php echo URI_PART_4 ?>'>
        <button type='submit'>Send Emails</button>
    </div>
</form>

<br> 

<p>Note: Once emails are sent, this entry will be deleted.</p>
