    <?php
	
	function create_xc_user($username){

    $api_url = 'http://iptv.watchhd.cc:5050/api/api.php?action=create_user';
    
    $post_data = array(
		
        'username' => $username 
    );


       $opts = array( 'http' => array(
        'method' => 'POST',
        'header' => 'Content-type: application/x-www-form-urlencoded',
        'content' => http_build_query( $post_data ) ) );

    $context = stream_context_create( $opts );
   
    $body = file_get_contents( $api_url, false, $context );
	
    $api_result = json_decode( $body, true );

    if ( $api_result['success'] )
    {
        return $api_result['data']['user'];
    }
    else{
        return false;
    }

}
	
    class Controller_Ajax_Line_Create extends Controller_Base_Ajax
    {
     
        /**
         * 
         */
        public function post_index()
        {
     
            if(!$this->user['is_user'])
            {
                return $this->response(array('success' => false, 'message' => 'Bitte einloggen'));
            }
     
            $user_id = $this->user['uid'];
     
    		if(Model_User_Line::query()->where('user_id', $user_id)->count() == 5)
    		{
    			    return $this->response(array('success' => false, 'message' => 'Du kannst keine weiteren sublines erstellen'));
    		}
     
            /* Line erstellen */
            $username = $this->user['username'].'_'.Str::random('alnum', 10);
			
			$line = create_xc_user($username);
			if(!$line){
				 $this->response(array('success' => false, 'message' => 'Aktion konnte nicht erfolgreich ausgeführt werden.'));
			}
           
                
     
    				$user = new Model_User_Line;
                    $user->user_id = $user_id;
                    $user->display_name = (Input::post('display_name', null) ? Input::post('display_name') : $username);
    				$user->username = $username;
                    $user->password = $line['password'];
    				$user->premium_until = $line['exp_date'];
					$user->reminder_send_at = 0;
                    $user->save();
     
                    /* Line erstellen */
     
            return $this->response(array('success' => true));
     
        }
     
    }