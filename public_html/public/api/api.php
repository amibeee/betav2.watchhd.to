<?php

/*
 *
 * - todo -
 * 1.) implement auth & allow exp update only for users own customers
 *
 */

require_once __DIR__.'/boot.php';
require_once __DIR__.'/config.php';
require_once __DIR__.'/library.php';

#$user = ORM::for_table('lines')->where('id', 1)->find_one(); 
#echo $user->allowed_outputs;
#echo "<pre>";
#print_r($user->as_array());
#echo "</pre>";
#unset($user);


$key = isset($_GET['key']) ? $_GET['key'] : '';
$action = isset($_GET['action']) ? $_GET['action'] : '';


if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
  $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
}

if(!in_array($_SERVER['REMOTE_ADDR'], array('ip-whitelist','ip-whitelist'))){
	header('Content-Type: application/json');
	echo json_encode(array('success' => false, 'message' => 'du kommst hier nicht rein'));
	exit();
}

if($action == "get_current_exp_date"){
	
	$username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
	
	if(empty($username)){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'parameter username required'));
		exit();
	}
	
	$user = ORM::for_table('lines')->where('username', $username)->find_one();
	if(!$user){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'i dont know this user'));
		exit();
	}

	$set['exp_date'] = $user->exp_date;
	
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
	
	$user = ORM::for_table('lines')->where('username', $username)->find_one();
	if(!$user){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'i dont know this user'));
		exit();
	}
	
	if($user->member_id != $reseller->id && $reseller->member_group_id != 1){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'you can not see informations about foreign clients'));
		exit();
	}
	
	$set['exp_date'] = $user->exp_date;
	$set['max_connections'] = $user->max_connections;
	$set['password'] = $user->password;
	
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => '', 'data' => array('user' => $set)));
	exit();
	
}

if($action == "isp_reset"){
	
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
	
	$user = ORM::for_table('lines')->where('username', $username)->find_one();
	if(!$user){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'i dont know this user'));
		exit();
	}
	
	$user->isp_desc = '';
	$user->save();
	
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => '', 'data' => array()));
	exit();
	
}

if($action == 'get_bouquets'){

	$bouquets = array();

	foreach(ORM::for_table('bouquets')->find_many() as $bouquet){
		$bouquets[$bouquet->id] = utf8_encode($bouquet->bouquet_name);
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
	
	$user = ORM::for_table('lines')->where('username', $username)->where('password', $password)->find_one();
	if(!$user){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Unknown User'));
		exit();
	}

	if($user->exp_date > time()){
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
	
	if($user = ORM::for_table('lines')->where('username', $username)->where('password', $password)->find_one()){
		
		$user_data['exp_date'] = $user->exp_date;
		$user_data['max_connections'] = $user->max_connections;
		
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
	
	if($user = ORM::for_table('lines')->where('username', $username)->find_one()){
		
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
	
	$package = detect_package($packages);
	
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
	
	$user = ORM::for_table('lines')->create();
	$user->member_id = $reseller->id;
	$user->username = $username;
	$user->password = random_str(12);
	$user->exp_date = (time()+$package['apply']);
	$user->admin_enabled = 1;
	$user->enabled = 1;
	$user->admin_notes = 'user was created by spongebobs api';
	$user->reseller_notes = 'user was created by spongebobs api';
	$user->bouquet = json_encode($bouquets);
	$user->max_connections = 1;
	$user->is_restreamer = 0;
	$user->allowed_ips = '[]';
	$user->allowed_ua = '[]';
	$user->is_trial = $package['is_trial'];
	$user->created_at = time();
	#$user->created_by = $reseller->id;
	$user->pair_id = null;
	$user->is_mag = 0;
	$user->is_e2 = 0;
	$user->force_server_id = 0;
	$user->is_isplock = 0;
	$user->isp_desc = '';
	$user->forced_country = '';
	$user->is_stalker = 0;
	$user->bypass_ua = 0;
	$user->as_number = null;
	$user->play_token = '';
	$user->allowed_outputs = '[1,2,3]';
	$user->save();

	$allowed_output_ids = array(1, 2);
	foreach($allowed_output_ids as $output_id){
		#$user_output = ORM::for_table('user_output')->create();
		#$user_output->user_id = $user->id;
		#$user_output->access_output_id = $output_id;
		#$user_output->save();
		#unset($user_output);
	}
	
	$line['username'] = $user->username;
	$line['password'] = $user->password;
	$line['exp_date'] = $user->exp_date;
	$line['bouquets'] = $bouquets;

	#$credits_before = $reseller->credits;

	#$reseller->credits = ($reseller->credits-$package['decrease']);
	#$reseller->save();

	/*
	if($package['decrease']){

		$deduct = $package['decrease'];

		$log = ORM::for_table('users_credits_logs')->create();
		$log->target_id = $reseller->id;
		$log->admin_id = 0;
		$log->amount = '-'.$deduct;
		$log->date = time();
		$log->reason = 'new line created with exp date '.date("Y/M/d H:i", $user->exp_date);
		$log->save();

	}
	*/
	
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
	
	$user = ORM::for_table('lines')->where('username', $username)->find_one();
	if(!$user){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'invalid parameter username'));
		exit();
	}
	
	$user->password = bin2hex(openssl_random_pseudo_bytes(4));
	$user->save();
	
	$line['username'] = $user->username;
	$line['password'] = $user->password;
	
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
	$is_multiconnect = isset($_REQUEST['is_multiconnect']) && $_REQUEST['is_multiconnect'] ? true : false; 
	
	if(!$username){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'invalid parameter username'));
		exit();
	}
	
	$user = ORM::for_table('lines')->where('username', $username)->find_one();
	if(!$user){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'invalid parameter username'));
		exit();
	}

	$package = detect_package($packages);
	
	if(!$package){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'could not guess package'));
		exit();
	}
	
	if(time() > $user->exp_date){
		$user->exp_date = time();
	}
	$user->exp_date = ($user->exp_date+$package['apply']);
	$user->is_isplock = ($is_multiconnect ? 1 : 0);
	$user->max_connections = ($is_multiconnect ? 2 : 1);
	$user->save();
	
	$credits_before = $reseller->credits;

	$reseller->credits = ($reseller->credits-$package['decrease']);
	$reseller->save();

	if($package['decrease']){

		$deduct = $package['decrease'];

		$log = ORM::for_table('users_credits_logs')->create();
		$log->target_id = $reseller->id;
		$log->admin_id = 0;
		$log->amount = '-'.$deduct;
		$log->date = time();
		$log->reason = 'line extended with exp date '.date("Y/M/d H:i", $user->exp_date);
		$log->save();

	}

	$data['user']['exp_date'] = $user->exp_date;
	
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Expire Date updated', 'data' => $data));
	exit();
	
}

if($action == 'set_exp'){
	
	$username = username(true);
	$expire_date = isset($_REQUEST['expire_date']) ? $_REQUEST['expire_date'] : 0;
	$is_multiconnect = isset($_REQUEST['is_multiconnect']) && $_REQUEST['is_multiconnect'] ? true : false;
	
	if(!$username){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'invalid parameter username'));
		exit();
	}
	
	$user = ORM::for_table('lines')->where('username', $username)->find_one();
	if(!$user){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'invalid parameter username'));
		exit();
	}
	
	if(!ctype_digit($expire_date)){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'invalid parameter expire_date'));
		exit();
	}

	$user->is_isplock = ($is_multiconnect ? 1 : 0);
	$user->max_connections = ($is_multiconnect ? 2 : 1);
	$user->exp_date = $expire_date;
	$user->save();

	$data['user']['exp_date'] = $user->exp_date;
	
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Expire Date updated', 'data' => $data));
	exit();
	
}

if($action == 'set_exp_and_bouquet'){
	
	$username = username(true);
	$expire_date = isset($_REQUEST['exp_date']) ? $_REQUEST['exp_date'] : 0;
	$bouquet = isset($_REQUEST['bouquet']) ? $_REQUEST['bouquet'] : '';
	$is_multiconnect = isset($_REQUEST['is_multiconnect']) && $_REQUEST['is_multiconnect'] ? true : false;
	
	if(!$username){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'invalid parameter username'));
		exit();
	}
	
	$user = ORM::for_table('lines')->where('username', $username)->find_one();
	if(!$user){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'invalid parameter username'));
		exit();
	}
	
	if(!ctype_digit($expire_date)){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'invalid parameter expire_date'));
		exit();
	}

	$user->is_isplock = ($is_multiconnect ? 1 : 0);
	$user->max_connections = ($is_multiconnect ? 2 : 1);
	$user->exp_date = $expire_date;
	$user->bouquet = $bouquet;
	$user->save();

	$data['user']['exp_date'] = $user->exp_date;
	$data['user']['max_connections'] = $user->max_connections;
	$data['user']['is_isplock'] = $user->is_isplock;
	$data['user']['bouquet'] = $user->bouquet;
	
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Line successfully updated', 'data' => $data));
	exit();
	
}

if($action == 'update_user'){
    
    $username = username(true);
    $expire_date = isset($_REQUEST['exp_date']) ? $_REQUEST['exp_date'] : 0;
    $password = isset($_REQUEST['password']) ? $_REQUEST['password'] : '';
    $bouquet = isset($_REQUEST['bouquet']) ? $_REQUEST['bouquet'] : '';
    $max_connections = isset($_REQUEST['max_connections']) ? (int)$_REQUEST['max_connections'] : 1;
    $is_multiconnect = isset($_REQUEST['is_multiconnect']) && $_REQUEST['is_multiconnect'] ? true : false;
    
    // Input validation
    if(!$username){
        header('Content-Type: application/json');
        echo json_encode(array('success' => false, 'message' => 'invalid parameter username'));
        exit();
    }
    
    $user = ORM::for_table('lines')->where('username', $username)->find_one();
    if(!$user){
        header('Content-Type: application/json');
        echo json_encode(array('success' => false, 'message' => 'invalid parameter username'));
        exit();
    }
    
    if(!ctype_digit($expire_date)){
        header('Content-Type: application/json');
        echo json_encode(array('success' => false, 'message' => 'invalid parameter expire_date'));
        exit();
    }
    
    if(empty($password)){
        header('Content-Type: application/json');
        echo json_encode(array('success' => false, 'message' => 'password required'));
        exit();
    }

    // Update user data
    $user->is_isplock = ($is_multiconnect ? 1 : 0);
    $user->password = $password;
    $user->exp_date = $expire_date;
    $user->max_connections = $max_connections;  // Use passed max_connections value
    $user->bouquet = $bouquet;
    $user->save();

    // Prepare response data
    $data = array(
        'user' => array(
            'exp_date' => $user->exp_date,
            'max_connections' => $user->max_connections,
            'is_isplock' => $user->is_isplock,
            'bouquet' => $user->bouquet,
            'username' => $user->username
        )
    );
    
    // Send response
    header('Content-Type: application/json');
    echo json_encode(array(
        'success' => true, 
        'message' => 'User updated successfully', 
        'data' => $data
    ));
    exit();
}

if($action == 'update_password'){
	
	$username = username(true);
	$current_password = isset($_REQUEST['current_password']) ? $_REQUEST['current_password'] : '';
	$password = isset($_REQUEST['password']) ? $_REQUEST['password'] : '';
	
	if(!$username){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'invalid parameter username'));
		exit();
	}
	
	$user = ORM::for_table('lines')->where('username', $username)->where('password', $current_password)->find_one();
	if(!$user){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'invalid user data'));
		exit();
	}
	
	if(empty($password)){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'password required'));
		exit();
	}

	$user->password = $password;
	$user->save();

	$data = array();
	
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'User updated', 'data' => $data));
	exit();
	
}

if($action == 'update_stream_display_name'){
	
	$channel_id = isset($_REQUEST['channel_id']) ? $_REQUEST['channel_id'] : '';
	$title = isset($_REQUEST['title']) ? trim(urldecode($_REQUEST['title'])) : '';
	
	$stream = ORM::for_table('streams')->where('id', $channel_id)->find_one();
	if(!$stream){
		
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Channel unknown'));
		exit();
		
	}
	
	$whitelist = array(
'130675', '130161', '130657', '130658', '130659',
'130660', '130661', '130662', '130663'
,'130664' ,'130665', '130666', '130667',
'130668','130669','130670','130671','130672','130673','130674');
	
	if(!in_array($channel_id, $whitelist)){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'action not permitted for this channel id'));
		exit();
	}
	
	if(empty($title)){
	
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'title required'));
		exit();
	
	}
	
	$stream->stream_display_name = $title;
	$stream->save();
	
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'update successfully', 'data' => array()));
	exit();
	
}

