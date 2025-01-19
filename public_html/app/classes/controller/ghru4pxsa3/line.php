<?php
class Controller_Ghru4pxsa3_Line extends Controller_Base_Admin
{


    /**
     *  
     */     
    public function action_edit($line_id)
    {   
        $type = Input::post('type', 'mainline');
       if ($type != null){
        \Log::error($type);
        $user = null;
        $line = null;
    
        if($type == 'mainline'){
            $user = Model_User::query()->where('id', $line_id)->get_one();
            $the_real_user = $user;
        } else {
            $user = Model_User_Line::query()->where('id', $line_id)->get_one();
            $the_real_user = Model_User::query()->where('id', $user->user_id)->get_one();
        }
    
        if(!$user) {
            throw new HttpNotFoundException();
        }
    
        $the_real_username = '';
        $the_real_password = '';
        
        if($type == 'mainline') {
            $the_real_username = $user->username;
            $the_real_password = $user->line_password;
        } else {
            $the_real_username = $user->username;
            $the_real_password = $user->password;
        }
    }
        else{
            $username = Input::post('username', '');
            $password = Input::post('password', '');
            $premium_until = Input::post('premium_until', '');
            $type = Input::post('type', 'mainline');
    
            $errors = array();
    
            if($type == 'mainline' && $username != $user->username) {
                $errors[] = 'Der Benutzername der Mainline kann nicht geändert werden.';
            }
            
            # username
            if(empty($username)) {
                $errors[] = 'Bitte Benutzername setzen.';
            }
    
            if(!preg_match("~{$the_real_user->username}_~", $username) && $type != 'mainline') {
                $errors[] = 'Benutzernamen müssen mit dem Benutzernamen der Hauptline gefolgt von einem _ beginnen.';
            }
    
            # password
            if(empty($password)) {
                $errors[] = 'Bitte Passwort setzen.';
            }
    
            # premium_until
            if(!empty($premium_until) && $premium_until != '1970-01-01' && !strtotime($premium_until)) {
                $errors[] = 'Bitte Premium Laufzeit setzen.';
            }
    
            if(count($errors) == 0) {
                if($type == 'mainline') {
                    $user->username = $username; 
                    $user->line_password = $password;
                    $user->premium_until = strtotime($premium_until);
                    $user->save();
                } else {
                    $user->username = $username;
                    $user->password = $password;
                    $user->premium_until = strtotime($premium_until);
                    $user->save();
                }
                
                // Updated API call with error handling
                try {
                    $panel_url = 'http://iptv.watchhd.cc:5050/';
                    $post_data = array( 
                        'username' => $the_real_username,
                        'password' => $the_real_password,
                        'user_data' => array(
                            'username' => $username,
                            'password' => $password,
                            'exp_date' => $user->premium_until
                        ) 
                    );
    
                    $ch = curl_init($panel_url . "api.php?action=user&sub=edit");
                    curl_setopt_array($ch, array(
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_POST => true,
                        CURLOPT_POSTFIELDS => http_build_query($post_data),
                        CURLOPT_TIMEOUT => 10,
                        CURLOPT_CONNECTTIMEOUT => 5,
                        CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded')
                    ));
    
                    $response = curl_exec($ch);
                    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
    
                    if ($http_code === 404) {
                        // Log the error but don't stop execution
                        \Log::error('IPTV API endpoint not found: ' . $panel_url . "api.php?action=user&sub=edit" . $post_data);
                        Messages::warning('API-Endpunkt nicht erreichbar. Lokale Änderungen wurden gespeichert 404.');
                    } else if ($response === false) {
                        \Log::error('IPTV API call failed: ' . curl_error($ch));
                        Messages::warning('API-Verbindung fehlgeschlagen. Lokale Änderungen wurden gespeichert.');
                    } else {
                        $api_result = json_decode($response, true);
                        if (!$api_result || $api_result['result'] == false) {
                            Messages::warning('Eine Änderung konnte nicht vollständig verarbeitet werden. Lokale Änderungen wurden gespeichert.');
                        } else {
                            Messages::success('Line erfolgreich aktualisiert.');
                        }
                    }
    
                } catch (Exception $e) {
                    \Log::error('Exception during API call: ' . $e->getMessage());
                    Messages::warning('Es gab ein Problem bei der API-Kommunikation. Lokale Änderungen wurden gespeichert.');
                }
    
                Messages::redirect(Uri::current().'?type='.$type);
    
            } else {
                Messages::error(implode("<br />", $errors));
                Messages::redirect(Uri::base().'ghru4pxsa3/line/'.Uri::segment(3).'/edit?type='.$type);
            }
        }
        
        $user->password = $the_real_password;
        
        $data['user'] = $user;
        $data['lines'] = Model_User_Packet::query()->where('line_id', $line_id)->where('user_id', $user->id)->get();
            
        return Response::forge(View::forge('ghru4pxsa3/line/edit.html.twig', isset($data) ? $data : array(), false)); 
    }

    /**
     *  
     */     
    public function action_packages($line_id)
    {   

        $user = null;
        $line = null;
        \Log::error($line_id);
		$exp_date = null;
        $type = Input::post('type', 'mainline');
        \Log::error($type);

        if($type === 'mainline'){
            $user = Model_User::query()->where('id', $line_id)->get_one();
			$exp_date = $user->premium_until;
        }
        else{
            $line = Model_User_Line::query()->where('id', $line_id)->get_one();
            $user = Model_User::query()->where('id', $line->user_id)->get_one();
			$exp_date = $line->premium_until;
        }
		
		/* [added] 08092020 */
		if(Input::post('cmd', '') != ''){
			
			switch(Input::post('cmd')){
				
				case 'repair-packages':
				
					if($type== 'mainline'){
						shell_exec('php /home/betav2user/web/betav2.watchhd.to/public_html/oil refine addpackages '.$user->id);
					}
					else{
						shell_exec('php /home/betav2user/web/betav2.watchhd.to/public_html/oil refine addpackagestolines '.$line->id);
					}	
				
					break;
				
			}
		
		}
		/* ende */
		

        if(!$user)
        {
            throw new HttpNotFoundException();
        }

        if(Input::method() == 'POST')
        {

            if(Input::post('setup', '') != '')
            {

                $packets[24]['name'] = 'IPTV-DACH';
                $packets[24]['description'] = '';
                $packets[24]['price'] = 9.00;
                $packets[24]['turnable'] = false;
                $packets[24]['channels'] = 'Sky DE, HD+, DAZN, Teleclub HD, MySports HD uvm.';
                
                $packets[35]['name'] = 'IPTV-Europa & Amerika';
                $packets[35]['description'] = '';
                $packets[35]['price'] = 0.00;
                $packets[35]['turnable'] = true;
                $packets[35]['channels'] = 'Sky UK, Bein Sports France, LaLiga TV, Lig TV, NFL Gamepass, ESPN uvm.';
                
                $packets[23]['name'] = 'IPTV-XXX';
                $packets[23]['description'] = '';
                $packets[23]['price'] = 0.00;
                $packets[23]['turnable'] = true;
                $packets[23]['channels'] = 'Redlight HD, Hustler TV, Penthouse HD, RealityKings uvm.';
                
                $packets[45]['name'] = 'IPTV-VoD';
                $packets[45]['description'] = '';
                $packets[45]['price'] = 0.00;
                $packets[45]['turnable'] = true;
                $packets[45]['channels'] = 'ca. 30-40 Kinohits in 720p';
                
                $packets[31]['name'] = 'IPTV-VoD Premium';
                $packets[31]['description'] = '';
                $packets[31]['price'] = 3.00;
                $packets[31]['turnable'] = true;
                $packets[31]['channels'] = 'über 800+ Filme  in 4K, 3D, 1080p und 720p';
				
				$defaults = array();
                $defaults[] = array(24, 'active', 'IPTV-DACH');
                $defaults[] = array(35, 'active', 'IPTV-Europa & Amerika');
                $defaults[] = array(23, 'inactive', 'IPTV-XXX');
                $defaults[] = array(45, 'active', 'IPTV-VoD');
                $defaults[] = array(31, 'inactive', 'IPTV-VoD Premium');

				foreach($defaults as $key => $default){
					
                    $packet = Model_User_Packet::query()->where('line_id', $line_id)->where('bouquet_id', $default[0])->where('line_type', $type)->get_one();
				
					if($packet){
									
									
						$status = in_array($default[0], array(24, 35)) ? $default[1] : 'inactive';
						$packet->booked_until = ($status == 'active' || in_array($default[0], array(45,23)) ? $exp_date : 0);
						$packet->save();
									
					}
                    else{

                                    $status = in_array($default[0], array(24, 35)) ? $default[1] : 'inactive';
                                    
                                    $packet = new Model_User_Packet;
									$packet->name = $default[2];
                                    $packet->user_id = $user->id;
                                    $packet->status = $status;
                                    $packet->line_type = $type;
                                    $packet->line_id = $line_id;
                                    $packet->bouquet_id = $default[0];
                                    $packet->booked_until = ($status == 'active' || in_array($default[0], array(45,23)) ? $exp_date : 0);
                                    $packet->save();
									
									
                    }

                }

                Messages::success('Setup wurde ausgeführt.');
                Messages::redirect('ghru4pxsa3/line/'.Uri::segment(3).'/packages?type='.$type);

		
            }
        
        }

		$items = Input::post('date', array());
		if(count($items) && Input::post('saveBtn', '') != ''){
			
			foreach(Input::post('date', array()) as $key => $value)
            {
                
                $timestamp = strtotime("{$value} ".Input::post('time.'.$key)."");
                if($timestamp)
                {
                    DB::update('user_packets')->set(array('booked_until' => $timestamp))->where('id', $key)->execute();
                }
                

            }

            Messages::success('Änderungen wurden gespeichert');
            Messages::redirect('ghru4pxsa3/line/'.Uri::segment(3).'/packages?type='.$type);
			
		}
		
        $data['user'] = $user;
        $data['packages'] = Model_User_Packet::query()->where('line_id', $line_id)->where('line_type', $type)->get();
            
        return Response::forge(View::forge('ghru4pxsa3/line/packages.html.twig', isset($data) ? $data : array(), false)); 
    
    }

}