<?php
return array(
    'auto_initialize'    => true,
    'driver'             => 'redis',
    'match_ip'           => false,
    'match_ua'           => true,
    'cookie_domain'      => '',
    'cookie_path'        => '/',
    'cookie_http_only'   => null,
    'encrypt_cookie'     => true,
    'expire_on_close'    => false,
    'expiration_time'    => (12*3600),  // 12 hours
    'rotation_time'      => 300,
    'flash_id'           => 'flash',
    'flash_auto_expire'  => true,
    'flash_expire_after_get' => true,
    'post_cookie_name'   => '',
    'http_header_name'   => 'Session-Id',
    'enable_cookie'      => true,
    'native_emulation'   => false,
    'redis'              => array(
        'cookie_name'    => 'fuelrid',
        'database'       => 'default',
    ),
);
