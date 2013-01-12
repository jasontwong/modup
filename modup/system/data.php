<?php

/**
 * Class that manages all configurations and settings from database
 * Aside from a few hard coded configurations and constants, everything else
 * will live in the data table and managed by this class.
 * @package data
 */
class Data
{
    //{{{ properties
    /**
     * Associative array of all data table rows
     */
    public static $data = NULL;
    public static $id = NULL;
    public static $autoload = NULL;
    /**
     * updates
     */
    private static $updates;
    /**
     * new data
     */
    private static $adds;
    /**
     * Flag notifying data has been changed and needs updating on cleanup
     */
    private static $changed = FALSE;
    //}}}
    //{{{ public static function init()
    /**
     * Gets all data entries from database
     *
     * @return void
     */
    public static function init()
    {
        if (is_null(self::$data))
        {
            self::$data = array();
            self::$id = array();
            self::$autoload = array();
            self::$updates = array();
            self::$adds = array();
            try
            {
                $rows = Doctrine_Query::create()
                        ->from('SystemData')
                        ->where('autoload = ?', TRUE)
                        ->execute()
                        ->toArray();
                foreach ($rows as $row)
                {
                    $row['data'] = unserialize($row['data']);
                    $row['autoload'] = TRUE;
                    self::register($row);
                }
            }
            catch (Exception $e)
            {
            }
        }
    }
    //}}}
    //{{{ public static function save()
    /**
     * Records updates and additions
     */
    public static function save()
    {
        self::init();
        if (self::$changed)
        {
            if (count(self::$updates))
            {
                $ids = array_keys(self::$updates);
                $sdt = Doctrine_Query::create()
                       ->from('SystemData')
                       ->whereIn('id', $ids)
                       ->execute();
                foreach (self::$updates as $id => $fp)
                {
                    foreach ($sdt as &$row)
                    {
                        if ($row->id == $id)
                        {
                            $row->data = self::query($fp['type'], $fp['name']);
                            $row->autoload = self::$autoload[$fp['type']][$fp['name']];
                            $row->save();
                            unset($row);
                            break;
                        }
                    }
                }
            }
            foreach (self::$adds as $add)
            {
                $d = new SystemData;
                $d->type = $add['type'];
                $d->name = $add['name'];
                $d->autoload = self::$autoload[$add['type']][$add['name']];
                $d->data = self::query($add['type'], $add['name']);
                $d->save();
                unset($d);
            }
            self::$updates = array();
            self::$adds = array();
            self::$changed = FALSE;
        }
    }
    //}}}
    //{{{ public static function query()
    /**
     * Looks into self::$data for information
     * You can drill down into the $data array, with each parameter getting
     * more specific. If no parameters are specified, entire array is 
     * returned. Get too specific where key doesn't exist, it returns NULL.
     *
     * @return mixed or null if doesn't exist
     */
    public static function query()
    {
        self::init();
        $args = func_get_args();
        $result = array_drill(self::$data, $args);
        if (is_null($result))
        {
            $result = call_user_func_array(array('Data', 'lookup'), $args);
            if (is_array($result))
            {
                foreach ($result as $row)
                {
                    $row['data'] = unserialize($row['data']);
                    self::register($row);
                }
            }
            return array_drill(self::$data, $args);
        }
        else
        {
            return $result;
        }
    }
    //}}}
    //{{{ public static function names($type)
    /**
     * Looks into self::$data for name columns
     *
     * @return array or null if type doesn't exist
     */
    public static function names($type)
    {
        self::init();
        return eka(self::$data, $type)
            ? array_keys(self::$data[$type])
            : NULL;
    }
    //}}}
    //{{{ public static function update($type, $name, $data, $autoload = NULL)
    /**
     * @param string $type module name or system type name
     * @param string $name name of setting or variable
     * @param mixed $data PHP native data payload
     * @param boolean $autoload auto loading flag, null to use existing value
     * @return bool true if all checks passed 
     */
    public static function update($type, $name, $data, $autoload = NULL)
    {
        self::init();
        self::$changed = TRUE;
        $signature = array('type' => $type, 'name' => $name);
        if (eka(self::$data, $type, $name))
        {
            if (!in_array($signature, self::$adds))
            {
                self::$updates[self::$id[$type][$name]] = $signature;
                if (!is_null($autoload))
                {
                    self::$autoload[$type][$name] = $autoload;
                }
            }
        }
        elseif (!in_array($signature, self::$adds))
        {
            self::$adds[] = $signature;
            self::$autoload[$type][$name] = is_null($autoload) ? FALSE : $autoload;
        }
        self::$data[$type][$name] = $data;
    }
    //}}}
    //{{{ public static function settings_form()
    /**
     */
    public static function settings_form()
    {
        $data = self::query('_Site');
        $rows = array(
            array(
                'field' => Field::layout(
                    'text',
                    array(
                        'data' => array(
                            'label' => 'Site Title'
                        )
                    )
                ),
                'name' => 'title',
                'type' => 'text',
                'value' => array(
                    'data' => $data['title']
                )
            ),
            array(
                'field' => Field::layout(
                    'text',
                    array(
                        'data' => array(
                            'label' => 'Site Description'
                        )
                    )
                ),
                'name' => 'description',
                'type' => 'text',
                'value' => array(
                    'data' => $data['description']
                )
            ),
            array(
                'field' => Field::layout(
                    'dropdown_timezones',
                    array(
                        'data' => array(
                            'label' => 'Time Zone'
                        )
                    )
                ),
                'name' => 'time_zone',
                'type' => 'dropdown_timezones',
                'value' => array(
                    'data' => $data['time_zone']
                )
            )
        );

        return $rows;
    }
    //}}}
    //{{{ private static function lookup($type, $name = NULL)
    /**
     */
    public static function lookup($type, $name = NULL)
    {
        try
        {
            $find = Doctrine_Query::create()
                    ->from('SystemData')
                    ->where('type = ?', $type);
            if (!is_null($name))
            {
                $find->andWhere('name = ?', $name);
            }
            $result = $find->execute()->toArray();
            return $result ? $result : NULL;
        }
        catch (Doctrine_Connection_Mysql_Exception $e)
        {
            // not installed yet
            return NULL;
        }
    }
    //}}}
    //{{{ private static function register($row)
    private static function register($row)
    {
        $type = &$row['type'];
        $name = &$row['name'];
        self::$data[$type][$name] = $row['data'];
        self::$id[$type][$name] = $row['id'];
        self::$autoload[$type][$name] = $row['autoload'];
    }
    //}}}
}

?>
