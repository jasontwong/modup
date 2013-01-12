<?php

/**
 * Updates the revision and revisions column
 */
class RevisionListener extends Doctrine_Record_Listener
{
    public function preInsert(Doctrine_Event $e)
    {
        $invoker = &$e->getInvoker();
        if (property_exists($invoker, 'revision'))
        {
            $invoker->revision = 0;
        }
        if (property_exists($invoker, 'revisions'))
        {
            $invoker->revisions = 0;
        }
    }
}

?>
