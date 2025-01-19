<?php
class Model_Commission extends Orm\Model
{

    protected static $_table_name = 'sales_agent_commissions';
    protected static $_mysql_timestamp = true;
    
    protected static $_observers = array(
		'Orm\\Observer_CreatedAt' => array('events' => array('before_insert')),
	);
    
}