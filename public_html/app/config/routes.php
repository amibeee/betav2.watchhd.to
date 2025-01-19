<?php
return array(
    // Default route, redirects / to home
    '_root_' => 'home',
    // 404 error route
    '_404_' => 'welcome/404',
    // 2FA verification routes
    'verify/(:any)' => 'verify/index/$1',
    'verify2fa' => 'verify2fa/index',
    'verify_2fa' => 'verify2fa/index',
    'cancel_2fa' => 'verify2fa/cancel',
    // General pages and channel routes
    'page/(:any)' => 'page/index/$1',
    'channel/(:any)' => 'channel/index/$1',
    'buy/(:any)' => 'buy/index/$1',
    'pay/(:num)' => 'pay/index/$1',
    'callback/(:any)' => 'callback/index/$1',
    'callback/(:any)' => 'pay/callback/$1',

    // Special redirects
    'ghru4pxsa3' => function(Request $request) {
        Response::redirect('ghru4pxsa3/dashboard');
    },
    'cash' => function(Request $request) {
        Response::redirect('cash/dashboard');
    },
    'mod' => function(Request $request) {
        Response::redirect('mod/dashboard');
    },
    // Routes for ghru4pxsa3 module
    'ghru4pxsa3/users/(:num)' => 'ghru4pxsa3/users/index/$1',
    'ghru4pxsa3/user/(:num)/(:any)' => 'ghru4pxsa3/users/$2/$1',
    'ghru4pxsa3/tickets/(:num)' => 'ghru4pxsa3/tickets/index/$1',
    'ghru4pxsa3/ticket/(:num)/(:any)' => 'ghru4pxsa3/tickets/$2/$1',
    'ghru4pxsa3/channels/(:num)' => 'ghru4pxsa3/channels/index/$1',
    'ghru4pxsa3/channel/(:num)/(:any)' => 'ghru4pxsa3/channels/$2/$1',
    'ghru4pxsa3/coupons/(:num)' => 'ghru4pxsa3/coupons/index/$1',
    'ghru4pxsa3/coupon/(:num)/(:any)' => 'ghru4pxsa3/coupons/$2/$1',
    'ghru4pxsa3/payments/(:num)' => 'ghru4pxsa3/payments/index/$1',
    'ghru4pxsa3/payment/(:num)/(:any)' => 'ghru4pxsa3/payments/$2/$1',
    'ghru4pxsa3/blog/(:num)/(:any)' => 'ghru4pxsa3/blog/$2/$1',
    'ghru4pxsa3/helper/(:num)/(:any)' => 'ghru4pxsa3/helpers/$2/$1',
    // AJAX routes
    'ajax/channel/comments/(:num)' => 'ajax/channel/comments/index/$1',
    'ajax/channel/uc/(:num)' => 'ajax/channel/uc/index/$1',
    'ajax/package/(:num)/action' => 'ajax/package/action/$1',
    'ajax/packets' => 'ajax/packets/index', // New route for fetching packets
    
    // Password recovery and change email
    'passwordrecovery/(:any)' => 'passwordrecovery/setpassword/$1',
    'changeemail/(:any)' => 'changeemail/changeemail/$1',
    'migrate/(:any)' => 'migrate/setpassword/$1',
    'ticket/(:any)' => 'ticket/index/$1',
    // Checkout and feedback
    'checkout/(:any)' => 'checkout/index/$1',
    'feedback/(:any)' => 'feedback/index/$1',
    // Language and playlist
    'language/(:any)' => 'language/index/$1',
    'playlist/(:any)' => 'playlist/index/$1',
    // M3U routes
    'm3u' => 'm3u/info',
    'm3u/(:any)' => 'm3u/index/$1',
    // Mod tickets
    'mod/tickets/(:num)' => 'mod/tickets/index/$1',
    'mod/ticket/(:num)/(:any)' => 'mod/tickets/$2/$1',
    // Ghru4pxsa3 templates
    'ghru4pxsa3/template/(:num)/(:any)' => 'ghru4pxsa3/templates/$2/$1',
    // Ghru4pxsa3 line routes
    'ghru4pxsa3/line/(:num)/(:any)' => 'ghru4pxsa3/line/$2/$1',
);
