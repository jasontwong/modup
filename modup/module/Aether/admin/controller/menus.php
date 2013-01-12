<?php

Admin::set('title', 'Nav Menus');
Admin::set('header', 'Nav Menus');

$nav = Aether::get_nav_menus();

$pages = Content::get_entries_details_by_type_name('Page');

$cats = array(
    'mens' => 'Mens Category', 
    'womens' => 'Womens Category', 
    'gear' => 'Gear Category'
);
$entries = array_fill_keys(
    array('mens', 'womens', 'gear'),
    array()
);
foreach ($cats as $k => $cat)
{
    $tmp = array();
    $data = Content::get_entries_details_by_type_name($cat);
    $type = Content::get_entry_type_by_name($cat);
    $taxm = new TaxonomyManager('Content', $type['id']);
    foreach ($data as $entry)
    {
        $statuses = $taxm->get_entry_terms($entry['entry']['id'], 'status');
        $is_published = FALSE;
        foreach ($statuses as $status)
        {
            if ($status['term_name'] === 'Publish')
            {
                $is_published = TRUE;
            }
        }
        if ($is_published)
        {
            $tmp[] = $entry;
        }
    }
    $entries[$k] = $tmp;
    unset($tmp);
}

if (ake('menu', $_POST))
{
    $nav = $_POST['menu'];
    Data::update('Aether', 'nav_menus', $nav);
    Aether::create_footer();
    Aether::create_header();
}

?>
