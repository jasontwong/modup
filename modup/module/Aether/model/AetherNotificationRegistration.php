<?php

class AetherNotificationRegistration extends Doctrine_Record
{
    //{{{ public function setTableDefinition()
    public function setTableDefinition()
    {
        $this->hasColumn(
            'id', 'integer', 4,
            array(
                'primary' => TRUE,
                'autoincrement' => TRUE,
                'notnull' => TRUE
            )
        );
        $this->hasColumn(
            'product_id', 'integer', 4,
            array(
                'notnull' => TRUE,
            )
        );
        $this->hasColumn(
            'color_id', 'integer', 4,
            array(
                'notnull' => TRUE,
            )
        );
        $this->hasColumn(
            'size_id', 'integer', 4,
            array(
                'notnull' => TRUE,
            )
        );
        $this->hasColumn(
            'emails', 'text', null,
            array(
                'default' => '',
                'notnull' => TRUE,
            )
        );
        $this->index(
            'general_lookup', 
            array(
                'fields' => array('product_id', 'color_id', 'size_id')
            )
        );
        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
        $this->option('type', 'InnoDB');
    }
    //}}}
    //{{{ public function setUp()
    public function setUp()
    {
    }
    //}}}
}

?>
