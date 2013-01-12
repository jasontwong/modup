<?php

class SystemData extends Doctrine_Record
{
    public function save()
    {
        $this->data = serialize($this->data);
        parent::save();
    }

    public function setTableDefinition()
    {
        $this->hasColumn(
            'type', 'string', 100
        );
        $this->hasColumn(
            'name', 'string', 100
        );
        $this->hasColumn(
            'data', 'clob', null,
            array(
                'default' => '',
                'notnull' => TRUE
            )
        );
        $this->hasColumn(
            'autoload', 'boolean', 1, 
            array(
                'default' => FALSE
            )
        );
        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
        $this->index(
            'autoload', 
            array(
                'fields' => array('autoload')
            )
        );
        $this->index(
            'type', 
            array(
                'fields' => array('type')
            )
        );
        $this->index(
            'type_name', 
            array(
                'fields' => array('type', 'name')
            )
        );
    }
}

?>
