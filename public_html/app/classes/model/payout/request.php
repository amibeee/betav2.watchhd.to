<?php
class Model_Payout_Request extends Orm\Model
{

    protected static $_table_name = 'payout_requests';
    protected static $_mysql_timestamp = true;
    
    protected static $_observers = array(
		'Orm\\Observer_CreatedAt' => array('events' => array('before_insert')),
	);
    
}