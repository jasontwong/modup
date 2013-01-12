<?php

/**
 * RecordListener for all Doctrine Models
 * This inserts hooks to all Doctrine model methods. Each Doctrine hook will be
 * converted to a hook with the words separated with an underscore, starting
 * with model_
 * @package ModelHookListener
 */
class ModelHookListener extends Doctrine_Record_Listener
{
    //{{{ public function preSave(Doctrine_Event $e)
    /**
     * Executed before Doctrine_Record::save()
     * The hook is sent the event variable
     */
    public function preSave(Doctrine_Event $e)
    {
        Module::h('model_pre_save', module::TARGET_ALL, $e);
    }

    //}}}
    //{{{ public function postSave(Doctrine_Event $e)
    /**
     * Executed before Doctrine_Validator::validate()
     * The hook is sent the event variable
     */
    public function postSave(Doctrine_Event $e)
    {
        Module::h('model_post_save', module::TARGET_ALL, $e);
    }

    //}}}
    //{{{ public function preUpdate(Doctrine_Event $e)
    /**
     * Executed before Doctrine_Record::save()
     * The hook is sent the event variable
     */
    public function preUpdate(Doctrine_Event $e)
    {
        Module::h('model_pre_update', module::TARGET_ALL, $e);
    }

    //}}}
    //{{{ public function postUpdate(Doctrine_Event $e)
    /**
     * Executed before Doctrine_Validator::validate()
     * The hook is sent the event variable
     */
    public function postUpdate(Doctrine_Event $e)
    {
        Module::h('model_post_update', module::TARGET_ALL, $e);
    }

    //}}}
    //{{{ public function preInsert(Doctrine_Event $e)
    /**
     * Executed before Doctrine_Record::save()
     * The hook is sent the event variable
     */
    public function preInsert(Doctrine_Event $e)
    {
        Module::h('model_pre_insert', module::TARGET_ALL, $e);
    }

    //}}}
    //{{{ public function postInsert(Doctrine_Event $e)
    /**
     * Executed before Doctrine_Validator::validate()
     * The hook is sent the event variable
     */
    public function postInsert(Doctrine_Event $e)
    {
        Module::h('model_post_insert', module::TARGET_ALL, $e);
    }

    //}}}
    //{{{ public function preDelete(Doctrine_Event $e)
    /**
     * Executed before Doctrine_Record::save()
     * The hook is sent the event variable
     */
    public function preDelete(Doctrine_Event $e)
    {
        Module::h('model_pre_delete', module::TARGET_ALL, $e);
    }

    //}}}
    //{{{ public function postDelete(Doctrine_Event $e)
    /**
     * Executed before Doctrine_Validator::validate()
     * The hook is sent the event variable
     */
    public function postDelete(Doctrine_Event $e)
    {
        Module::h('model_post_delete', module::TARGET_ALL, $e);
    }

    //}}}
    //{{{ public function preValidate(Doctrine_Event $e)
    /**
     * Executed before Doctrine_Validator::validate()
     * The hook is sent the event variable
     */
    public function preValidate(Doctrine_Event $e)
    {
        Module::h('model_pre_validate', module::TARGET_ALL, $e);
    }

    //}}}
    //{{{ public function postValidate(Doctrine_Event $e)
    /**
     * Executed after Doctrine_Validator::validate()
     * The hook is sent an array of validation errors
     */
    public function postValidate(Doctrine_Event $e)
    {
        $errors = $e->getInvoker()->getErrorStack()->toArray();
        $a = array();
        if (ake('validate', $errors))
        {
            foreach ($errors['validate'] as $e)
            {
                $a = array_merge($a, (array)$e);
            }
        }
        Module::h('model_post_validate', module::TARGET_ALL, $a);
    }

    //}}}
}

?>
