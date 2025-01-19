<?php
class Model_Blog_Post extends Orm\Model
{

    protected static $_table_name = 'blog_posts';
    protected static $_mysql_timestamp = true;
    
    protected static $_observers = array(
		'Orm\\Observer_CreatedAt' => array('events' => array('before_insert')),
        'Orm\\Observer_Slug' => array(
        'events' => array('before_insert'),
        'source' => 'title',  
        'property' => 'slug', 
        'separator' => '-',   
        'unique' => true,     
    ),
	);
    
}