<h2>Product Information</h2>

<p>Product Name: <?php echo $note['product']['name'] ?></p>
<p>Color: <?php echo $note['color']['name'] ?></p>
<p>Size: <?php echo $note['size']['name'] ?></p>
<p>Emails:</p>
<ul>
    <?php foreach ($note['emails'] as $email): ?>
        <li><?php echo hsc($email) ?></li>
    <?php endforeach ?>
</ul>

<br>

<form method='post' action='<?php echo URI_PATH ?>'>
    <div>
        <input type='hidden' name='do' value='delete'>
        <input type='hidden' name='id' value='<?php echo URI_PART_4 ?>'>
        <button type='submit'>Delete This Entry</button>
    </div>
</form>

