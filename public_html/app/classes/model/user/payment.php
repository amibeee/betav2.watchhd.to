<?php
class Model_User_Payment extends Orm\Model
{

    protected static $_table_name = 'user_payments';
    protected static $_mysql_timestamp = true;
    
    protected static $_observers = array(
		'Orm\\Observer_CreatedAt' => array('events' => array('before_insert')),
	);
    
}