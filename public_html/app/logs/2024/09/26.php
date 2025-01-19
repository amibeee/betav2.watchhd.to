<?php defined('COREPATH') or exit('No direct script access allowed'); ?>

ERROR - 2024-09-26 01:20:44 --> Warning - file_get_contents(https://iptv.watchhd.cc:25463/get.php?username=amibeee&amp;password=g1zmpaJXdkJp&amp;type=m3u&amp;output=m3u8): failed to open stream: Connection timed out in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/channel.php on line 96
ERROR - 2024-09-26 01:41:16 --> Error - Unknown "is_logged_in" function. in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/views/layout.html.twig on line 122
ERROR - 2024-09-26 01:45:03 --> Error - Unexpected character "&". in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/views/layout.html.twig on line 122
ERROR - 2024-09-26 01:46:11 --> Error - Unexpected token "punctuation" of value "|" ("name" expected). in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/views/layout.html.twig on line 122
ERROR - 2024-09-26 01:46:12 --> Error - Unexpected token "punctuation" of value "|" ("name" expected). in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/views/layout.html.twig on line 122
ERROR - 2024-09-26 17:05:56 --> Error - The requested view could not be found: buy/step1_payment.html.twig in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/view.php on line 477
ERROR - 2024-09-26 19:42:18 --> Authentication failed: No reseller ID in session.
ERROR - 2024-09-26 19:42:19 --> Failed to create user line. Rolling back user creation.
ERROR - 2024-09-26 19:45:29 --> Notice - Trying to access array offset on value of type bool in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/ajax/line/create.php on line 69
ERROR - 2024-09-26 19:50:58 --> Error - The requested view could not be found: buy/step1_payment.html.twig in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/view.php on line 477
ERROR - 2024-09-26 19:51:43 --> Error - The requested view could not be found: buy/step1_payment.html.twig in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/view.php on line 477
ERROR - 2024-09-26 19:51:45 --> Error - The requested view could not be found: buy/step1_payment.html.twig in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/view.php on line 477
ERROR - 2024-09-26 19:54:38 --> Error - The requested view could not be found: buy/step1_payment.html.twig in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/view.php on line 477
ERROR - 2024-09-26 20:06:57 --> Exception occurred while saving payment: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('12 Monate Premium', 22367, 59.000000, 'afcbed1fdb3ee85ac5baefa8c3e7031d', 1727374017, 'pending', 'premium', '{\"premium_days\":365,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 59.000000, '')"
ERROR - 2024-09-26 20:06:57 --> Stack trace: #0 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/database/query.php(314): Fuel\Core\Database_PDO_Connection->query()
#1 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/query.php(1765): Fuel\Core\Database_Query->execute()
#2 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1478): Orm\Query->insert()
#3 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1421): Orm\Model->create()
#4 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(200): Orm\Model->save()
#5 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(82): Controller_Buy->save_payment()
#6 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(15): Controller_Buy->handle_step2()
#7 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/base.php(111): Controller_Buy->action_index()
#8 [internal function]: Controller_Base->router()
#9 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/request.php(454): ReflectionMethod->invokeArgs()
#10 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(71): Fuel\Core\Request->execute()
#11 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(92): {closure}()
#12 {main}
ERROR - 2024-09-26 20:06:57 --> Payment processing failed: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('12 Monate Premium', 22367, 59.000000, 'afcbed1fdb3ee85ac5baefa8c3e7031d', 1727374017, 'pending', 'premium', '{\"premium_days\":365,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 59.000000, '')"
ERROR - 2024-09-26 20:24:36 --> Compile Error - Cannot redeclare Controller_Buy::handle_payment_method_request() in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php on line 190
ERROR - 2024-09-26 21:28:43 --> Exception occurred while saving payment: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, 'c50502993675f51c63c63c489a8735ba', 1727378923, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 21:28:43 --> Stack trace: #0 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/database/query.php(314): Fuel\Core\Database_PDO_Connection->query()
#1 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/query.php(1765): Fuel\Core\Database_Query->execute()
#2 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1478): Orm\Query->insert()
#3 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1421): Orm\Model->create()
#4 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(200): Orm\Model->save()
#5 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(82): Controller_Buy->save_payment()
#6 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(15): Controller_Buy->handle_step2()
#7 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/base.php(111): Controller_Buy->action_index()
#8 [internal function]: Controller_Base->router()
#9 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/request.php(454): ReflectionMethod->invokeArgs()
#10 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(71): Fuel\Core\Request->execute()
#11 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(92): {closure}()
#12 {main}
ERROR - 2024-09-26 21:28:43 --> Payment processing failed: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, 'c50502993675f51c63c63c489a8735ba', 1727378923, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 21:29:30 --> Exception occurred while saving payment: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, 'd2b9520c5e6ff0ac4dde5e1818b909a7', 1727378970, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 21:29:30 --> Stack trace: #0 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/database/query.php(314): Fuel\Core\Database_PDO_Connection->query()
#1 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/query.php(1765): Fuel\Core\Database_Query->execute()
#2 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1478): Orm\Query->insert()
#3 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1421): Orm\Model->create()
#4 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(200): Orm\Model->save()
#5 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(82): Controller_Buy->save_payment()
#6 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(15): Controller_Buy->handle_step2()
#7 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/base.php(111): Controller_Buy->action_index()
#8 [internal function]: Controller_Base->router()
#9 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/request.php(454): ReflectionMethod->invokeArgs()
#10 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(71): Fuel\Core\Request->execute()
#11 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(92): {closure}()
#12 {main}
ERROR - 2024-09-26 21:29:30 --> Payment processing failed: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, 'd2b9520c5e6ff0ac4dde5e1818b909a7', 1727378970, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 21:29:44 --> Exception occurred while saving payment: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, '9e2d9974eb1b6bbff51dadbbe79abfe3', 1727378984, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 21:29:44 --> Stack trace: #0 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/database/query.php(314): Fuel\Core\Database_PDO_Connection->query()
#1 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/query.php(1765): Fuel\Core\Database_Query->execute()
#2 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1478): Orm\Query->insert()
#3 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1421): Orm\Model->create()
#4 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(200): Orm\Model->save()
#5 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(82): Controller_Buy->save_payment()
#6 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(15): Controller_Buy->handle_step2()
#7 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/base.php(111): Controller_Buy->action_index()
#8 [internal function]: Controller_Base->router()
#9 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/request.php(454): ReflectionMethod->invokeArgs()
#10 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(71): Fuel\Core\Request->execute()
#11 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(92): {closure}()
#12 {main}
ERROR - 2024-09-26 21:29:44 --> Payment processing failed: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, '9e2d9974eb1b6bbff51dadbbe79abfe3', 1727378984, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 21:29:45 --> Exception occurred while saving payment: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, '019bf62486b91f7bd4dada26825c7c56', 1727378985, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 21:29:45 --> Stack trace: #0 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/database/query.php(314): Fuel\Core\Database_PDO_Connection->query()
#1 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/query.php(1765): Fuel\Core\Database_Query->execute()
#2 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1478): Orm\Query->insert()
#3 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1421): Orm\Model->create()
#4 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(200): Orm\Model->save()
#5 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(82): Controller_Buy->save_payment()
#6 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(15): Controller_Buy->handle_step2()
#7 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/base.php(111): Controller_Buy->action_index()
#8 [internal function]: Controller_Base->router()
#9 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/request.php(454): ReflectionMethod->invokeArgs()
#10 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(71): Fuel\Core\Request->execute()
#11 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(92): {closure}()
#12 {main}
ERROR - 2024-09-26 21:29:45 --> Payment processing failed: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, '019bf62486b91f7bd4dada26825c7c56', 1727378985, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 21:30:16 --> Exception occurred while saving payment: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, '460ea83ea2f9d8d07e7e85615f3abdcc', 1727379016, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 21:30:16 --> Stack trace: #0 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/database/query.php(314): Fuel\Core\Database_PDO_Connection->query()
#1 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/query.php(1765): Fuel\Core\Database_Query->execute()
#2 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1478): Orm\Query->insert()
#3 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1421): Orm\Model->create()
#4 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(200): Orm\Model->save()
#5 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(82): Controller_Buy->save_payment()
#6 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(15): Controller_Buy->handle_step2()
#7 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/base.php(111): Controller_Buy->action_index()
#8 [internal function]: Controller_Base->router()
#9 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/request.php(454): ReflectionMethod->invokeArgs()
#10 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(71): Fuel\Core\Request->execute()
#11 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(92): {closure}()
#12 {main}
ERROR - 2024-09-26 21:30:16 --> Payment processing failed: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, '460ea83ea2f9d8d07e7e85615f3abdcc', 1727379016, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 21:30:38 --> Exception occurred while saving payment: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, 'f45347a158cf6efc8c6f6830800bf1de', 1727379038, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 21:30:38 --> Stack trace: #0 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/database/query.php(314): Fuel\Core\Database_PDO_Connection->query()
#1 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/query.php(1765): Fuel\Core\Database_Query->execute()
#2 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1478): Orm\Query->insert()
#3 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1421): Orm\Model->create()
#4 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(200): Orm\Model->save()
#5 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(82): Controller_Buy->save_payment()
#6 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(15): Controller_Buy->handle_step2()
#7 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/base.php(111): Controller_Buy->action_index()
#8 [internal function]: Controller_Base->router()
#9 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/request.php(454): ReflectionMethod->invokeArgs()
#10 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(71): Fuel\Core\Request->execute()
#11 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(92): {closure}()
#12 {main}
ERROR - 2024-09-26 21:30:38 --> Payment processing failed: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, 'f45347a158cf6efc8c6f6830800bf1de', 1727379038, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 21:30:47 --> Exception occurred while saving payment: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, '5cf322235e08b8afd537da1418d4ebca', 1727379047, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 21:30:47 --> Stack trace: #0 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/database/query.php(314): Fuel\Core\Database_PDO_Connection->query()
#1 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/query.php(1765): Fuel\Core\Database_Query->execute()
#2 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1478): Orm\Query->insert()
#3 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1421): Orm\Model->create()
#4 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(200): Orm\Model->save()
#5 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(82): Controller_Buy->save_payment()
#6 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(15): Controller_Buy->handle_step2()
#7 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/base.php(111): Controller_Buy->action_index()
#8 [internal function]: Controller_Base->router()
#9 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/request.php(454): ReflectionMethod->invokeArgs()
#10 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(71): Fuel\Core\Request->execute()
#11 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(92): {closure}()
#12 {main}
ERROR - 2024-09-26 21:30:47 --> Payment processing failed: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, '5cf322235e08b8afd537da1418d4ebca', 1727379047, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 21:30:54 --> Exception occurred while saving payment: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, '68e09c4746a6dafb6fce7b970e8ec027', 1727379054, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 21:30:54 --> Stack trace: #0 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/database/query.php(314): Fuel\Core\Database_PDO_Connection->query()
#1 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/query.php(1765): Fuel\Core\Database_Query->execute()
#2 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1478): Orm\Query->insert()
#3 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1421): Orm\Model->create()
#4 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(200): Orm\Model->save()
#5 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(82): Controller_Buy->save_payment()
#6 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(15): Controller_Buy->handle_step2()
#7 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/base.php(111): Controller_Buy->action_index()
#8 [internal function]: Controller_Base->router()
#9 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/request.php(454): ReflectionMethod->invokeArgs()
#10 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(71): Fuel\Core\Request->execute()
#11 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(92): {closure}()
#12 {main}
ERROR - 2024-09-26 21:30:54 --> Payment processing failed: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, '68e09c4746a6dafb6fce7b970e8ec027', 1727379054, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 21:31:05 --> Exception occurred while saving payment: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, 'f6548fead7080252864f44214e106a84', 1727379065, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 21:31:05 --> Stack trace: #0 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/database/query.php(314): Fuel\Core\Database_PDO_Connection->query()
#1 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/query.php(1765): Fuel\Core\Database_Query->execute()
#2 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1478): Orm\Query->insert()
#3 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1421): Orm\Model->create()
#4 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(200): Orm\Model->save()
#5 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(82): Controller_Buy->save_payment()
#6 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(15): Controller_Buy->handle_step2()
#7 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/base.php(111): Controller_Buy->action_index()
#8 [internal function]: Controller_Base->router()
#9 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/request.php(454): ReflectionMethod->invokeArgs()
#10 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(71): Fuel\Core\Request->execute()
#11 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(92): {closure}()
#12 {main}
ERROR - 2024-09-26 21:31:05 --> Payment processing failed: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, 'f6548fead7080252864f44214e106a84', 1727379065, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 21:31:18 --> Exception occurred while saving payment: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, 'a9b8b1af6e892c6bcb09264215149e09', 1727379078, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 21:31:18 --> Stack trace: #0 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/database/query.php(314): Fuel\Core\Database_PDO_Connection->query()
#1 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/query.php(1765): Fuel\Core\Database_Query->execute()
#2 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1478): Orm\Query->insert()
#3 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1421): Orm\Model->create()
#4 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(200): Orm\Model->save()
#5 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(82): Controller_Buy->save_payment()
#6 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(15): Controller_Buy->handle_step2()
#7 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/base.php(111): Controller_Buy->action_index()
#8 [internal function]: Controller_Base->router()
#9 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/request.php(454): ReflectionMethod->invokeArgs()
#10 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(71): Fuel\Core\Request->execute()
#11 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(92): {closure}()
#12 {main}
ERROR - 2024-09-26 21:31:18 --> Payment processing failed: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, 'a9b8b1af6e892c6bcb09264215149e09', 1727379078, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 21:31:19 --> Exception occurred while saving payment: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, 'bd510cef7fb6afeddf602409d56e06f8', 1727379079, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 21:31:19 --> Stack trace: #0 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/database/query.php(314): Fuel\Core\Database_PDO_Connection->query()
#1 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/query.php(1765): Fuel\Core\Database_Query->execute()
#2 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1478): Orm\Query->insert()
#3 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1421): Orm\Model->create()
#4 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(200): Orm\Model->save()
#5 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(82): Controller_Buy->save_payment()
#6 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(15): Controller_Buy->handle_step2()
#7 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/base.php(111): Controller_Buy->action_index()
#8 [internal function]: Controller_Base->router()
#9 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/request.php(454): ReflectionMethod->invokeArgs()
#10 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(71): Fuel\Core\Request->execute()
#11 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(92): {closure}()
#12 {main}
ERROR - 2024-09-26 21:31:19 --> Payment processing failed: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, 'bd510cef7fb6afeddf602409d56e06f8', 1727379079, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 21:31:21 --> Exception occurred while saving payment: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, '3e204596987864633e5aa99a9838e8cb', 1727379081, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 21:31:21 --> Stack trace: #0 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/database/query.php(314): Fuel\Core\Database_PDO_Connection->query()
#1 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/query.php(1765): Fuel\Core\Database_Query->execute()
#2 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1478): Orm\Query->insert()
#3 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1421): Orm\Model->create()
#4 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(200): Orm\Model->save()
#5 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(82): Controller_Buy->save_payment()
#6 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(15): Controller_Buy->handle_step2()
#7 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/base.php(111): Controller_Buy->action_index()
#8 [internal function]: Controller_Base->router()
#9 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/request.php(454): ReflectionMethod->invokeArgs()
#10 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(71): Fuel\Core\Request->execute()
#11 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(92): {closure}()
#12 {main}
ERROR - 2024-09-26 21:31:21 --> Payment processing failed: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, '3e204596987864633e5aa99a9838e8cb', 1727379081, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 21:32:00 --> Exception occurred while saving payment: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, 'c269c7e1c934dc35b1c1ffc8edcd2a6f', 1727379120, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 21:32:00 --> Stack trace: #0 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/database/query.php(314): Fuel\Core\Database_PDO_Connection->query()
#1 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/query.php(1765): Fuel\Core\Database_Query->execute()
#2 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1478): Orm\Query->insert()
#3 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/packages/orm/classes/model.php(1421): Orm\Model->create()
#4 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(200): Orm\Model->save()
#5 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(82): Controller_Buy->save_payment()
#6 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/buy.php(15): Controller_Buy->handle_step2()
#7 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/base.php(111): Controller_Buy->action_index()
#8 [internal function]: Controller_Base->router()
#9 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/request.php(454): ReflectionMethod->invokeArgs()
#10 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(71): Fuel\Core\Request->execute()
#11 /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/public/index.php(92): {closure}()
#12 {main}
ERROR - 2024-09-26 21:32:00 --> Payment processing failed: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'method' cannot be null with query: "INSERT INTO `user_payments` (`product`, `user_id`, `amount`, `token`, `created_at`, `status`, `type`, `data`, `method`, `notice`, `worth`, `decline_reason`) VALUES ('1 Monat Premium', 22367, 24.000000, 'c269c7e1c934dc35b1c1ffc8edcd2a6f', 1727379120, 'pending', 'premium', '{\"premium_days\":30,\"buy\":{\"type\":\"mainline\",\"line_id\":22367,\"username\":\"amibeee\"},\"packages\":[\"1\"]}', null, '', 24.000000, '')"
ERROR - 2024-09-26 22:00:54 --> Error - The requested view could not be found: buy/buy_process.html.twig in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/view.php on line 477
ERROR - 2024-09-26 22:03:01 --> Error - The requested view could not be found: buy/buy_process.html.twig in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/view.php on line 477
ERROR - 2024-09-26 22:03:03 --> Error - The requested view could not be found: buy/buy_process.html.twig in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/view.php on line 477
ERROR - 2024-09-26 22:03:47 --> Error - The requested view could not be found: buy/buy_process.html.twig in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/view.php on line 477
ERROR - 2024-09-26 22:34:43 --> Error - The requested view could not be found: buy/step1.html.twig in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/view.php on line 477
ERROR - 2024-09-26 22:34:49 --> Error - The requested view could not be found: buy/step1.html.twig in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/view.php on line 477
ERROR - 2024-09-26 22:35:09 --> Error - The requested view could not be found: buy/step1.html.twig in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/view.php on line 477
ERROR - 2024-09-26 22:37:05 --> Error - The requested view could not be found: buy/step1.html.twig in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/fuel/core/classes/view.php on line 477
ERROR - 2024-09-26 23:14:34 --> Error - syntax error, unexpected '=>' (T_DOUBLE_ARROW) in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/pay.php on line 33
ERROR - 2024-09-26 23:15:22 --> Error - syntax error, unexpected 'private' (T_PRIVATE) in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/pay.php on line 35
ERROR - 2024-09-26 23:15:22 --> Error - syntax error, unexpected 'private' (T_PRIVATE) in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/pay.php on line 35
ERROR - 2024-09-26 23:25:05 --> Error - Unexpected character "!". in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/views/buy/step2.html.twig on line 38
ERROR - 2024-09-26 23:29:15 --> Notice - Trying to access array offset on value of type null in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/ghru4pxsa3/channels.php on line 536
ERROR - 2024-09-26 23:30:06 --> Notice - Trying to access array offset on value of type null in /home/amibee/Pictures/sucuk/web/watchhd.to/public_html/app/classes/controller/ghru4pxsa3/channels.php on line 536
