<?php
class Model_Channel_Comment extends Orm\Model
{

    protected static $_table_name = 'channel_comments';
    protected static $_mysql_timestamp = true;
    
    protected static $_observers = array(
		'Orm\\Observer_CreatedAt' => array('events' => array('before_insert')),
	);
    
}