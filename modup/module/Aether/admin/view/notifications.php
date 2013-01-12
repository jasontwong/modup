<?php if ($notes): ?>

    <table id='notification-registrations'>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Color</th>
                <th>Size</th>
                <th>E-mails</th>
                <th>Send Emails</th>
                <th>Delete?</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($notes as &$note): ?>
                <tr>
                    <td>
                        <?php echo hsc($note['product']['name']) ?>
                    </td>
                    <td>
                        <?php echo hsc($note['color']['name']) ?>
                    </td>
                    <td>
                        <?php echo hsc($note['size']['name']) ?>
                    </td>
                    <td>
                        <?php echo count($note['emails']) ?>
                    </td>
                    <td>
                        <a href="/admin/module/Aether/notifications-send/<?php echo $note['id'] ?>/">Send Emails</a>
                    </td>
                    <td>
                        <a href="/admin/module/Aether/notifications-delete/<?php echo $note['id'] ?>/">Delete</a>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>

<?php else: ?>

    <p>There are no notification registrations at this time.</p>

<?php endif ?>
