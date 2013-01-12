<?php

class CacheData extends Doctrine_Record
{

    public function setTableDefinition()
    {
        $this->hasColumn(
            'namespace', 'string', 50,
            array(
                'default' => NULL,
                'notnull' => FALSE
            )
        );
        $this->hasColumn(
            'name', 'string', 50,
            array(
                'default' => '',
                'notnull' => TRUE
            )
        );
        $this->hasColumn(
            'data', 'clob', null,
            array(
                'default' => NULL,
                'notnull' => FALSE
            )
        );
        // expiration time
        $this->hasColumn(
            'expire', 'integer', 8, 
            array(
                'default' => 0
            )
        );
        // locked out time
        $this->hasColumn(
            'lockout', 'integer', 8,
            array(
                'default' => NULL,
                'notnull' => FALSE
            )
        );
        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
        $this->index(
            'lookup_name', 
            array(
                'fields' => array('name')
            )
        );
        $this->index(
            'lookup_namespace_name', 
            array(
                'fields' => array('namespace', 'name')
            )
        );
        $this->index(
            'ts_expire', 
            array(
                'fields' => array('expire')
            )
        );
        $this->index(
            'ts_lockout', 
            array(
                'fields' => array('lockout')
            )
        );
    }
}

?>
