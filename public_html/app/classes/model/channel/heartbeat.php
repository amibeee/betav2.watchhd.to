<?php
class Model_Channel_Heartbeat extends Orm\Model
{

    protected static $_table_name = 'channel_heartbeats';
    protected static $_mysql_timestamp = true;
    
    protected static $_observers = array(
		'Orm\\Observer_CreatedAt' => array('events' => array('before_insert')),
	);
    
}