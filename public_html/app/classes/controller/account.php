<?php
	
    function update_xc_password($username, $current_password, $password){
    
        $api_url = 'http://iptv.watchhd.cc:5050/api/api.php?action=update_password';
        
        $post_data = array(
            'username' => $username,
            'current_password' => $current_password,
            'password' => $password
        );
    
        $opts = array( 'http' => array(
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query( $post_data ) ) );
    
        $context = stream_context_create( $opts );
       
        $body = file_get_contents( $api_url, false, $context );
        
        $api_result = json_decode($body, true);
        
        $api_result['data']['request'] = $post_data;
        
        return $api_result; // Return the full response instead of just true/false
    }
    class Controller_Account extends Controller_Base_User
    {
     
        /**
         * 
         */
        public function action_index()
        {


            if(Input::method() == 'POST')
            {
                    if(Input::post('do') && Input::post('do') == 'deleteLine'){
     
                        $line = Model_User_Line::query()->where('id', Input::post('line_id'))->where('user_id', $this->user['uid'])->where('premium_until', '<', time())->get_one();
                        if($line){
             
             
                            $ticket = new Model_Ticket;
                            do{
                                $ticket->public_id = Str::random('sha1');
                            }
                            while(Model_Ticket::query()->where('public_id', '=', $ticket->public_id)->count());
                            $ticket->user_id = $this->user['uid'];
                            $ticket->category = 'Line löschen';
                            $ticket->subject = 'Löschanfrage für Line '.$line->username.' von '.$this->user['username'];
                            $ticket->contents = '';
                            $ticket->name = $this->user['username'];
                            $ticket->email = '';
                            $ticket->closed = 0;
                            $ticket->updated_at = time();
                            $ticket->status = 'wartend auf Antwort';
                            $ticket->save();
             
                            $line->delete();
             
                            Messages::success('Line wurde gelöscht.');
                            Messages::redirect('account');
             
                        }
                        else{
             
                            Messages::error('Du kannst nur deine eigenen Lines löschen.');
                            Messages::redirect('account');
             
                        }
             
                    }
                
                else{
                $password = Input::post('password', '');
                $username = Input::post('username', '');

                if(empty($username))
                {
                    Messages::error('Bei der Datenübertragung ist ein Fehler aufgetreten. Bitte versuche es nochmal.');
                }

                if(mb_strlen($password) < 12 || preg_match("~\s~", $password))
                {
                    Messages::error('Das gewählte Passwort ist zu kurz oder enthält untültige Zeichen. Es wurden keine Änderungen vorgenommen.');
                    Messages::redirect('account');
                }

                // getting the previous password
                if(!preg_match("~_~", $username))
                {
                    $previous_password = DB::select('line_password')->from('users')->where('id', $this->user['uid'])->where('username', $username)->execute()[0]['line_password'];
                }
                else
                {
                    $previous_password = DB::select('password')->from('user_lines')->where('user_id', $this->user['uid'])->where('username', $username)->execute()[0]['password'];
                }

                // dont know why but we did not found previous password
                if(!$previous_password)
                {
                    Messages::error('Es ist ein Fehler aufgetreten. Bitte wende dich an den Support.');
                    Messages::redirect('account');
                }
                $api_response = update_xc_password($username, $previous_password, $password);
        

				
        // Update the database if API call was successful
        if(isset($api_response['success']) && $api_response['success']) {
            if(!preg_match("~_~", $username)) {
                $user = Model_User::query()->where('id', $this->user['uid'])->where('username', $username)->get_one();
                if($user){
                    $user->line_password = $password;
                    $user->save();
                }
            } else {
                $user = Model_User_Line::query()->where('user_id', $this->user['uid'])->where('username', $username)->get_one();
                if($user){
                    $user->password = $password;
                    $user->save();
                }
            }
            Messages::success('Passwort wurde erfolgreich geändert');
        } else {
            Messages::error('Es ist ein Fehler aufgetreten. Bitte wende dich an den Support.');
        }
        Response::redirect('account');
    }
        }

     
            $data['payments'] = Model_User_Payment::query()->where('user_id', '=', $this->user['uid'])->order_by('created_at', 'DESC')->get();
     
     
            $user = Model_User::query()->where('id', '=', $this->user['uid'])->get_one();
            $user->lines = Model_User_Line::query()->where('user_id', $user->id)->get();
            $data['user'] = $user;
            $data['token'] = sha1($user->username.$user->password);
			
			$data['playlists'] = array();
             // Fetch multiline status for main lines and sublines
             $multiline_status = [];
             foreach ($data['user']->lines as $line) {
                 $payment = Model_User_Payment::query()
                     ->where('user_id', '=', $this->user['uid'])
                     ->where('data', 'LIKE', '%"username":"' . $line->username . '"%')
                     ->order_by('created_at', 'DESC')
                     ->get_one();
                 if ($payment) {
                     $payment_data = json_decode($payment->data, true);
                     $multiline_status[$line->username] = isset($payment_data['buy']['multiline']) ? $payment_data['buy']['multiline'] : '0';
                 } else {
                     $multiline_status[$line->username] = '0';
                 }
             }
             $data['multiline_status'] = $multiline_status; 
			foreach(Model_Customizeduserplaylists::query()->where('user_id', $this->user['uid'])->get() as $playlist){
				
				if(empty($playlist->username) == false){
					$data['playlists'][$playlist->username][] = $playlist; 
				}
				else{
					$data['playlists'][$this->user['username']][] = $playlist; 
				}
			
			}
            
            return Response::forge(View::forge('account.html.twig', isset($data) ? $data : array(), false)); 
            
     
        }
        public function action_packages()
        {
    
            $line = Model_User_Line::query()->where('username', Input::get('line'))->where('user_id', $this->user['uid'])->get_one();
            if(!$line && Input::get('line') != $this->user['username'])
            {
                throw new HttpNotFoundException();
            }
    
            $line_id = $line ? $line->id : $this->user['uid'];
            $user_id = $line ? $line->user_id : $this->user['uid'];
        
    
            $data['packages'] = Model_User_Packet::query()->where('user_id', $user_id)->where('line_id', $line_id)->order_by('name', 'asc')->get();
    
            return Response::forge(View::forge('packages.html.twig', isset($data) ? $data : array(), false)); 
    
        }
     
    }