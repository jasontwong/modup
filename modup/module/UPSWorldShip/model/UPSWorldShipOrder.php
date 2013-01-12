<?php

class UPSWorldShipOrder extends Doctrine_Record
{
    //{{{ public function setTableDefinition()
    public function setTableDefinition()
    {
        $this->hasColumn(
            'id', 'integer', 8,
            array(
                'primary' => TRUE,
                'autoincrement' => TRUE,
            )
        );
        $this->hasColumn(
            'order_name', 'string', 100, 
            array(
                'type' => 'string', 
                'length' => '100',
                'notnull' => TRUE,
            )
        );
        $this->hasColumn(
            'tracking_number', 'string', 255, 
            array(
                'type' => 'string', 
                'length' => '255',
                'notnull' => TRUE,
            )
        );
        $this->hasColumn(
            'is_return', 'string', 100,
            array(
                'type' => 'string', 
                'length' => '100',
                'default' => '',
            )
        );

        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
        $this->option('type', 'MyISAM');
    }

    //}}}
    //{{{ public function setUp()
    public function setUp()
    {
    }

    //}}}
}

?>
