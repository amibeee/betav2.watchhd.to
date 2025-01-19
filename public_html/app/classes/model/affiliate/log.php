<?php
class Model_Affiliate_Log extends Orm\Model
{

    protected static $_table_name = 'affiliate_log';
    protected static $_mysql_timestamp = true;
    
    protected static $_observers = array(
		'Orm\\Observer_CreatedAt' => array('events' => array('before_insert')),
	);
    
}