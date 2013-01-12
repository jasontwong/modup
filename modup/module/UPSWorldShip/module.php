<?php

class UPSWorldShip
{
    //{{{ constants 
    const MODULE_AUTHOR = 'Jason T. Wong';
    const MODULE_DESCRIPTION = 'UPS World Ship module';
    const MODULE_WEBSITE = '';
    const MODULE_DEPENDENCY = '';
    //}}}
    //{{{ constructor
    /**
     * @param int $state current state of module manager
     */
    public function __construct()
    {
    }

    //}}}
    //{{{ public function hook_active()
    public function hook_active()
    {
    }

    //}}}
    //{{{ public function hook_admin_css()
    public function hook_admin_css()
    {
        $css = array();
        return $css;
    }
    //}}}
    //{{{ public function hook_admin_js()
    public function hook_admin_js()
    {
        $js = array();
        return $js;
    }
    //}}}
    //{{{ public function hook_admin_module_page($page)
    public function hook_admin_module_page($page)
    {
    }
    
    //}}}
    //{{{ public function hook_admin_start()
    public function hook_admin_start()
    {
        $tnums = Doctrine_Query::create()
            ->from('UPSWorldShipOrder wso')
            ->where('wso.is_return IS NOT NULL')
            ->execute();
        $eot = Doctrine::getTable('EcommerceOrder');
        foreach ($tnums as $tnum)
        {
            if (strlen($tnum['order_name']) && $tnum['is_return'] === 'N')
            {
                $order = $eot->findOneByOrderName($tnum['order_name']);
                if ($order !== FALSE && !strlen($order->tracking_number))
                { 
                    $order->tracking_number = $tnum['tracking_number'];
                    if ($order->isValid())
                    {
                        $order->save();
                        $order->free();
                        $tnum->delete();
                    }
                }
            }
        }
    }
    //}}}
    //{{{ public function hook_rpc($action, $params = NULL)
    /**
     * Implementation of hook_rpc
     *
     * This looks at the action and checks for the method _rpc_<action> and
     * passes the parameters to that. There is no limit on parameters.
     *
     * @param string $action action name
     * @return string
     */
    public function hook_rpc($action)
    {
        $method = '_rpc_'.$action;
        $caller = array($this, $method);
        $args = array_slice(func_get_args(), 1);
        return method_exists($this, $method) 
            ? call_user_func_array($caller, $args)
            : '';
    }

    //}}}
}

?>
