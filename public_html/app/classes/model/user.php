<?php
class Model_User extends Orm\Model
{

    protected static $_table_name = 'users';
    protected static $_mysql_timestamp = true;
    
    protected static $_observers = array(
		'Orm\\Observer_CreatedAt' => array('events' => array('before_insert')),
		'Orm\\Observer_UpdatedAt' => array('events' => array('before_save')),
	);
    
}