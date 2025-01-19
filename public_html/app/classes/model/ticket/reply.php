<?php
class Model_Ticket_Reply extends Orm\Model
{

    protected static $_table_name = 'ticket_replies';
    protected static $_mysql_timestamp = true;
    
    protected static $_observers = array(
		'Orm\\Observer_CreatedAt' => array('events' => array('before_insert')),
	);
    
}