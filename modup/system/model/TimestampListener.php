<?php

/**
 * Updates the created and modified columns if they exist
 */
class TimestampListener extends Doctrine_Record_Listener
{
    public function preInsert(Doctrine_Event $e)
    {
        $invoker = &$e->getInvoker();
        $columns = array_keys($invoker->toArray());
        if (in_array('created', $columns))
        {
            $invoker->created = time();
        }
        if (in_array('modified', $columns))
        {
            $invoker->modified = time();
        }
    }
    public function preUpdate(Doctrine_Event $e)
    {
        $invoker = &$e->getInvoker();
        $columns = array_keys($invoker->toArray());
        if (in_array('modified', $columns))
        {
            $invoker->modified = time();
        }
    }
}

?>
