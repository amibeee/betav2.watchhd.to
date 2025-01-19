<?php
class Model_Coupon extends Orm\Model
{

    protected static $_table_name = 'coupons';
    protected static $_mysql_timestamp = true;
    
    protected static $_observers = array(
		'Orm\\Observer_CreatedAt' => array('events' => array('before_insert')),
	);
    
}