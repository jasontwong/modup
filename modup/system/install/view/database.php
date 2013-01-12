
<?php if (!is_null($is_valid)): ?>

    <?php if ($is_valid): ?>

            <a href='/install/settings/'>proceed: site settings</a>

    <?php endif ?>

<?php endif ?>

<?php if (is_writeable($dbfile)) echo $fh; ?>
<!--
<form method='post' action='/install/database/'>
    <div>
        <div class='row'>
            <div class='label'>
                <label for='database_type'>Type</label>
            </div>
            <div class='field'>
                <select name='database[type]' id='database_type'>
                    <?php 
                        foreach ($dbtypes as $dbtype)
                        {
                            $selected = $dbtype === $db['type'] ? ' selected="selected"' : '';
                            echo '<option name="'.$dbtype.'"'.$selected.'>'.$dbtype.'</option>';
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class='row'>
            <div class='label'>
                <label for='database_host'>Host</label>
            </div>
            <div class='field'>
                <input type='text' class='text' name='database[host]' value='<?php echo $db['host'] ?>' id='database_host'>
            </div>
        </div>
        <div class='row'>
            <div class='label'>
                <label for='database_user'>Username</label>
            </div>
            <div class='field'>
                <input type='text' class='text' name='database[user]' value='<?php echo $db['user'] ?>' id='database_user'>
            </div>
        </div>
        <div class='row'>
            <div class='label'>
                <label for='database_pass'>Password</label>
            </div>
            <div class='field'>
                <input type='password' class='password' name='database[pass]' value='<?php echo $db['pass'] ?>' id='database_pass'>
            </div>
        </div>
        <div class='row'>
            <div class='label'>
                <label for='database_name'>Database Name</label>
            </div>
            <div class='field'>
                <input type='text' class='text' name='database[name]' value='<?php echo $db['name'] ?>' id='database_name'>
            </div>
        </div>
        <div class='row'>
            <div class='field'>
                <button type='submit'>submit</button>
                <button type='reset'>reset</button>
            </div>
        </div>
    </div>
</form>
-->
