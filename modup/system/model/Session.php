<?php

class Session extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->hasColumn(
            'id', 'string', 32, 
            array(
                'fixed' => TRUE, 
                'unique' => TRUE,
                'primary_key' => TRUE
            )
        );
        $this->hasColumn(
            'data', 'string', null
        );
        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
    }
}

?>
