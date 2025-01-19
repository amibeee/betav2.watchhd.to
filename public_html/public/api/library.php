<?php
function validate_package($reseller, $packages){
	
	$package_id = isset($_POST['package_id']) ? (int) $_POST['package_id'] : 0;
	
	if(false == isset($packages[$package_id]['decrease'])){
		return false;
	}
	
	if($reseller->credits < $packages[$package_id]['decrease']){
		return false;
	}
	
	$packages[$package_id]['apply'] = ($packages[$package_id]['duration']*86400);
	
	return $packages[$package_id];
	
}

function detect_package($reseller, $packages){
	
	$add_time = isset($_POST['add_time']) ? (int) $_POST['add_time'] : 0;

	$the_package = null;

	foreach($packages as $package){
		if(($add_time/86400) == $package['duration']){
			$the_package = $package;
		}
	}
	
	if(!$the_package){
		return false;
	}
	
	if($reseller->credits < $the_package['decrease']){
		return false;
	}
	
	$the_package['apply'] = ($the_package['duration']*86400);
	
	return $the_package;
	
}

function username($bypassDuplicateValidation = false){
	
	$username = isset($_POST['username']) ? $_POST['username'] : '';
	
	if(empty($username)){
		return false;
	}
	
	if(preg_match("/\s/", $username)){
		return false;
	}
	
	// todeck check chars
	
	if(ORM::for_table('cms_user')->where('user_name', $username)->count() && $bypassDuplicateValidation == false){
		return false;
	}
	
	return $username;
	
}

function bouquets(){

	$user = ORM::for_table('cms_lines')->where('line_user', 'homepagecQvDxDw8zqZrnN68')->find_one();
	if(!$user) return false;
			
	return $user->line_bouquet_id;

}

function auth(){

	$auth['username'] = isset($_REQUEST['auth']['username']) ? $_REQUEST['auth']['username'] : '';

	if(!($user = ORM::for_table('cms_user')->where('user_name', $auth['username'])->find_one())){
		return false;
	}

	return $user;

}
