<?php

class GoogleAnalytics
{
    public function hook_admin_nav()
    {
        return array(
            'Google Analytics' => array(
                'Show report' => '<a href="/admin/module/GoogleAnalytics/data/">Show report</a>'
            )
        );
    }
    public function hook_admin_dashboard()
    {
        return array(
            array(
                'title' => 'Google Analytics',
                'content' => $this->_admin_dashboard_overview()
            )
        );
    }
    public function hook_admin_module_page($page){}
    
    private function _admin_dashboard_overview()
    {
      /*  $uac = Doctrine_Query::create()
               ->select('COUNT(id)')
               ->from('UserAccount')
               ->fetchOne(array(), Doctrine::HYDRATE_ARRAY);
        $ugc = Doctrine_Query::create()
               ->select('COUNT(id)')
               ->from('UserGroup')
               ->fetchOne(array(), Doctrine::HYDRATE_ARRAY);
        $o = '
            <ul>
                <li>Total User Accounts: '.$uac['COUNT'].'</li>
                <li>Total User Groups: '.$ugc['COUNT'].'</li>
            </ul>';
        return $o;*/
    }
    
    /*public function hook_user_perm()
    {
        return array(
            'Hello, World' => array(
                'can say hi'
            ),
            'Goodbye, World' => array(
                'can say bye'
            )
        );
    }*/
}

?>
