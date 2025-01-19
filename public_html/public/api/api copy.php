<?php
require_once __DIR__.'/boot.php';
require_once __DIR__.'/config.php';
require_once __DIR__.'/library.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action=="update_password"){
	echo("test");

}
if($action == "get_current_exp_date"){

	$reseller = auth();
	
	if(!$reseller){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'authentication failed'));
		exit();
	}

	$username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
	
	if(empty($username)){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'parameter username required'));
		exit();
	}
	
	$user = ORM::for_table('cms_lines')->where('line_user', $username)->find_one();
	if(!$user){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'i dont know this user'));
		exit();
	}

	$set['exp_date'] = $user->line_expire_date;
	
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => '', 'data' => array('user' => $set)));
	exit();

}

if($action == "userinfo"){
	
	$reseller = auth();
	
	if(!$reseller){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'authentication failed'));
		exit();
	}

	$username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
	
	if(empty($username)){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'parameter username required'));
		exit();
	}
	
	$user = ORM::for_table('cms_lines')->where('line_user', $username)->find_one();
	if(!$user){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'i dont know this user'));
		exit();
	}

	if($user->line_user_id != $reseller->user_id && !$reseller->user_is_admin){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'you can not see informations about foreign clients'));
		exit();
	}
	
	$set['exp_date'] = $user->line_expire_date;
	$set['max_connections'] = $user->line_connection;
	$set['password'] = $user->line_pass;
	
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => '', 'data' => array('user' => $set)));
	exit();
	
}

if($action == 'get_default_line_bouquets'){

	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => '', 'data' => array('bouquets' => bouquets()) ));
	exit();

}

if($action == 'get_bouquets'){

	$bouquets = array();

	foreach(ORM::for_table('cms_bouquets')->find_many() as $bouquet){
		$bouquets[$bouquet->bouquet_id] = utf8_encode($bouquet->bouquet_name);
	}

	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => '', 'data' => array('bouquets' => $bouquets) ));
	exit();

}

if($action == 'delete_user'){

	$username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
	$password = isset($_REQUEST['password']) ? $_REQUEST['password'] : '';
	
	if(empty($username)){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Username required'));
		exit();
	}
	
	if(empty($password)){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Password required'));
		exit();
	}
	
	$user = ORM::for_table('cms_lines')->where('line_user', $username)->where('line_pass', $password)->find_one();
	if(!$user){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Unknown User'));
		exit();
	}

	if($user->line_expire_date > time()){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'delete action is only available for expired users'));
		exit();
	}
		
	$user->delete();
		
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'user successfully deleted', 'data' => array()));
	exit();

}

if($action == 'verify_credentials'){
	
	$username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
	$password = isset($_REQUEST['password']) ? $_REQUEST['password'] : '';
	
	if(empty($username)){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Username required'));
		exit();
	}
	
	if(empty($password)){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Password required'));
		exit();
	}
	
	if($user = ORM::for_table('cms_lines')->where('line_user', $username)->where('line_pass', $password)->find_one()){
		
		$user_data['exp_date'] = $user->line_expire_date;
		$user_data['max_connections'] = $user->line_connection;
		
		header('Content-Type: application/json');
		echo json_encode(array('success' => true, 'message' => 'user credentials valid', 'data' => array('user' => $user_data)));
		exit();
		
	}
	else{
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Invalid User'));
		exit();
	}
	
}

if($action == 'username_exists'){
	
	$username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
	
	if(empty($username)){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Username required'));
		exit();
	}
	
	if($user = ORM::for_table('cms_lines')->where('line_user', $username)->find_one()){
		
		header('Content-Type: application/json');
		echo json_encode(array('success' => true, 'message' => 'username exists', 'data' => array()));
		exit();
		
	}
	else{
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'unkown username'));
		exit();
	}
	
}


if($action == 'create_user'){
	
	$reseller = auth();
	
	if(!$reseller){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'authentication failed'));
		exit();
	}

	if(file_exists(__DIR__."/configs/{$reseller->username}.php")){
		require_once __DIR__."/configs/{$reseller->username}.php";
	}
	
	$username = username();
	
	if(!$username){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'invalid parameter username'));
		exit();
	}
	
	$package = validate_package($reseller, $packages);
	
	if(!$package){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'invalid package'));
		exit();
	}
	
	$bouquets = bouquets();
	if(!$bouquets){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'cant load bouquet list'));
		exit();
	}
	
	$user = ORM::for_table('cms_lines')->create();
	$user->line_user = $username;
	$user->line_pass = bin2hex(openssl_random_pseudo_bytes(4));
	$user->line_expire_date = (time()+$package['apply']);
	$user->line_user_id = $reseller->id;
	$user->line_bouquet_id = $bouquets;
	$user->line_connection = 1;
	$user->line_status = 0;
	$user->line_status_reason = '';
	$user->line_note = '';
	$user->line_reseller_note = '';
	$user->line_is_restreamer = 0;
	$user->line_allowed_hls = 0;
	$user->line_allowed_ip = '';
	$user->line_allowed_ua = '';
	$user->line_allowed_isp = '';
	$user->line_fingerprint_typ = 0;
	$user->line_fingerprint_custom_text = '';
	$user->line_fingerprint_start_time = '';
	$user->line_fingerprint = '';
	$user->line_fingerprint_target = '';
	$user->line_fingerprint_stream_id = '';
	$user->save();

	$line['username'] = $user->line_user;
	$line['password'] = $user->line_pass;
	$line['exp_date'] = $user->line_expire_date;
	$line['bouquets'] = $bouquets;

	$credits_before = $reseller->user_credit;

	$reseller->credits = ($reseller->user_credit-$package['decrease']);
	$reseller->save();

	if($package['decrease']){

		$deduct = $package['decrease'];

		/*
		$log = ORM::for_table('credits_log')->create();
		$log->target_id = $reseller->id;
		$log->admin_id = 0;
		$log->amount = '-'.$deduct;
		$log->date = time();
		$log->reason = 'new line created with exp date '.date("Y/M/d H:i", $user->exp_date);
		$log->save();
		*/

	}
	
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'IPTV Line successfully created', 'data' => array('user' => $line)));
	exit();
	
}

if($action == 'refresh_password'){
	
	$reseller = auth();
	
	if(!$reseller){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'authentication failed'));
		exit();
	}
	
	$username = username(true);
	
	if(!$username){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'invalid parameter username'));
		exit();
	}
	
	$user = ORM::for_table('cms_user')->where('user_name', $username)->find_one();
	if(!$user){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'invalid parameter username'));
		exit();
	}
	
	$user->user_pass = bin2hex(openssl_random_pseudo_bytes(6));
	$user->save();
	
	$line['username'] = $user->user_name;
	$line['password'] = $user->user_pass;
	
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'IPTV Line successfully updated', 'data' => array('user' => $line)));
	exit();
	
	
	
}

if($action == 'update_exp'){
	
	$reseller = auth();
	
	if(!$reseller){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'authentication failed'));
		exit();
	}

	if(file_exists(__DIR__."/configs/{$reseller->username}.php")){
		require_once __DIR__."/configs/{$reseller->username}.php";
	}
	
	$username = username(true);
	
	if(!$username){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'invalid parameter username'));
		exit();
	}
	
	$user = ORM::for_table('cms_user')->where('user_name', $username)->find_one();
	if(!$user){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'invalid parameter username'));
		exit();
	}

	$package = detect_package($reseller, $packages);
	
	if(!$package){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'could not guess package'));
		exit();
	}
	
	if(time() > $user->line_expire_date){
		$user->line_expire_date = time();
	}
	$user->line_expire_date = ($user->line_expire_date+$package['apply']);
	$user->save();
	
	$credits_before = $reseller->user_credit;

	$reseller->user_credit = ($reseller->user_credit-$package['decrease']);
	$reseller->save();

	if($package['decrease']){

		$deduct = $package['decrease'];

		/*
		$log = ORM::for_table('credits_log')->create();
		$log->target_id = $reseller->id;
		$log->admin_id = 0;
		$log->amount = '-'.$deduct;
		$log->date = time();
		$log->reason = 'line extended with exp date '.date("Y/M/d H:i", $user->exp_date);
		$log->save();
		*/

	}

	$data['user']['exp_date'] = $user->line_expire_date;
	
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Expire Date updated', 'data' => $data));
	exit();
	
}

if($action == 'get_credits'){

	$reseller = auth();
	if(!$reseller){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'authorization failed'));
		exit();

	}

	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'username exists', 'data' => array('credits' => $reseller->user_credit)));
	exit();
		
		
}

if($action == 'update_credits'){

	$reseller = auth();
	if(!$reseller){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'authorization failed'));
		exit();

	}

	$username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
	$credits = isset($_REQUEST['credits']) ? $_REQUEST['credits'] : '';
	$subtract = isset($_REQUEST['subtract']) ? true : false;
	
	if(empty($username)){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Username required'));
		exit();
	}

	if($username == $reseller->user_name){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'you can not charge your own account'));
		exit();	
	}
		
	if(!ctype_digit($credits)){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Credits required'));
		exit();
	}

	if($credits > $reseller->user_credit && $subtract == false){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'you cant charge that amount of credits because you only have '.$reseller->user_credit.' credits left'));
		exit();

	}

	if($user = ORM::for_table('cms_user')->where('user_name', $username)->find_one()){
		
		if($subtract){
			$user->user_credit = ($user->user_credit-$credits);
		}
		else{
			$user->user_credit = ($user->user_credit+$credits);
		}
		$user->save();
		
		if(!$subtract){
			$reseller->user_credit = ($reseller->user_credit-$credits);
			$reseller->save();
		}
		
		header('Content-Type: application/json');
		echo json_encode(array('success' => true, 'message' => 'Credits updated', 'data' => array('credits' => ['user' => $user->user_credit, 'reseller' => $reseller->user_credit]) ));
		exit();
		
	}
	else{
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Invalid User'));
		exit();
	}
	
}