<?php
class Model_Page extends Orm\Model
{

    protected static $_table_name = 'pages';
    protected static $_mysql_timestamp = true;
    
    protected static $_observers = array(
		'Orm\\Observer_UpdatedAt' => array('events' => array('before_save')),
	);
    
}