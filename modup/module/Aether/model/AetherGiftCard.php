<?php

class AetherGiftCard extends Doctrine_Record
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
            'order_id', 'integer', 4,
            array(
                'notnull' => TRUE,
            )
        );
        $this->hasColumn(
            'amount', 'integer', 4,
            array(
                'notnull' => TRUE,
            )
        );
        $this->hasColumn(
            'order_name', 'string', 100,
            array(
                'notnull' => TRUE,
            )
        );
        $this->hasColumn(
            'from_name', 'string', 255,
            array(
                'default' => '',
            )
        );
        $this->hasColumn(
            'from_email', 'string', 255,
            array(
                'default' => '',
            )
        );
        $this->hasColumn(
            'to_name', 'string', 255,
            array(
                'default' => '',
            )
        );
        $this->hasColumn(
            'to_email', 'string', 255,
            array(
                'default' => '',
            )
        );
        $this->hasColumn(
            'type', 'string', 255,
            array(
                'default' => '',
            )
        );
        $this->hasColumn(
            'message', 'text', null,
            array(
                'default' => '',
            )
        );
        $this->hasColumn(
            'is_sent', 'integer', 1,
            array(
                'default' => 0,
            )
        );
        $this->hasColumn('created_date', 'integer', 8);
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

    // {{{ public function preInsert($event)
    public function preInsert($event)
    {
        if (!is_numeric($this->created_date) || $this->created_date === 0)
        {
            $this->created_date = time();
        }
    }
    // }}}
}

?>
