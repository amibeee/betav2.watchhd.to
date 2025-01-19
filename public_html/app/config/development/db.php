<?php
/**
 * The development database settings. These get merged with the global settings.
 */

return array(
	'default' => array(
		'connection'  => array(
			'dsn'        => 'mysql:host=localhost;dbname=sucuk_site;mysql:unix_socket=/var/lib/mysql/mysql.sock',
			'username'   => 'sucuk_site',
			'password'   => '6LVHAtKbaM6v1HGV',
		),
                'charset'        => 'utf8',
                'enable_cache'   => false,
                'profiling'      => true,
	),
);
