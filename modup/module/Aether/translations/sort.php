<?php

include dirname(__FILE__).'/JP.php';

$d = array_change_key_case($d, CASE_LOWER);
ksort($d);


foreach ($d as $k => $v)
{
    echo '$d[\''.$k.'\'] = \''.$v.'\';'."\n";
}

?>
