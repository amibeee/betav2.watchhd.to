        <?php
        class Controller_Ghru4pxsa3_Users extends Controller_Base_Admin
        {
        
            /**
             *  
             */     
            public function action_credit()
            {   
        
                if(Input::method() == 'POST')
                {
        
                    $add = (int) Input::post('bonustime');
        
                    if($add < 86400)
                    {
        
                        \Messages::error('Zusatzzeit darf nicht kleiner als 1 Tag sein.');
                        \Messages::redirect('ghru4pxsa3/users/credit');
        
                    }
                    

        
                    foreach(Model_User::query()->where('premium_until', '>', time())->get() as $user)
                    {
                        
                        
                        
                        if($user->premium_until < time()) $user->premium_until = time();
                        $user->premium_until = ($user->premium_until+$add);
                        $user->save();
                    }
        
                    \Messages::success('Premium Laufzeit wurde erweitert.');
                    \Messages::redirect('ghru4pxsa3/users/credit');
        
                }
        
                return Response::forge(View::forge('ghru4pxsa3/users/credit.html.twig', isset($data) ? $data : array(), false)); 
        
            }

    /**
             *  
             */     
            public function action_lines($user_id)
            {   
        
                $user = Model_User::query()->where('id', $user_id)->get_one();
                if(!$user)
                {
                    throw new HttpNotFoundException();
                }

                $user->lines = Model_User_Line::query()->where('user_id', $user_id)->get();

                if(Input::method() == 'POST')
                {
            
                    foreach(Input::post('line', array()) as $username => $password)
                    {
        
                        // check if password did have a given length, else ignore the changes
                        if(mb_strlen($password) < 8)
                        {
                            Messages::warning('Eine Änderung wurde ignoriert da ein zu kurzes Passwort angegeben wurde.');
                            continue;
                        }

                        // getting the previous password
                        if(!preg_match("~_~", $username))
                        {
                            $previous_password = DB::select('line_password')->from('users')->where('username', $username)->execute()[0]['line_password'];
                        }
                        else
                        {
                            $previous_password = DB::select('password')->from('user_lines')->where('username', $username)->execute()[0]['password'];
                        }

                        // dont know why but we did not found previous password
                        if(!$previous_password) continue;

                        // update the user line via iptv panel api
                        $panel_url = 'http://iptv.watchhd.cc:8000/';
                        /*
                        $username = $username;
                        $password = $password;
                        $max_connections = 1;
                        $reseller = 0;
                        $bouquet_ids = array(
                            1,
                        );
                        $expire_date = time();
                        */
                        ###############################################################################
                        //$line['username'] = $username;
                        //$line['password'] = $previous_password;

                        $post_data = array( 
                            'username' => $username,
                            'password' => $previous_password,
                            'user_data' => array(
                                'password' => $password,
                            ) 
                        );

                        $opts = array( 
                                'http' => array(
                                    'method' => 'POST',
                                    'header' => 'Content-type: application/x-www-form-urlencoded',
                                    'content' => http_build_query( $post_data ) 
                                ) 
                        );

                        $context = stream_context_create( $opts );
                        $api_result = json_decode( file_get_contents( $panel_url . "api.php?action=user&sub=edit", false, $context ), true );
                    
                        if($api_result['result'] == false)
                        {
                            Messages::warning('Eine Änderung konnte nicht verarbeitet werden da die API nicht wie erwartet geantwortet hat.');
                            continue;
                        }

                        // updating the user db entry
                        if(!preg_match("~_~", $username))
                        {

                            $user = Model_User::query()->where('username', $username)->get_one();
                            $user->line_password = $password;
                            $user->save();
                        
                        }
                        else
                        {
                            
                            $user = Model_User_Line::query()->where('username', $username)->get_one();
                            $user->password = $password;
                            $user->save();

                        }

                        Messages::success('lines successfully updated');
                        Messages::redirect('ghru4pxsa3/user/'.$user_id.'/lines');

                    }

                }

                
                $data['user'] = $user;
        
                return Response::forge(View::forge('ghru4pxsa3/users/lines.html.twig', isset($data) ? $data : array(), false)); 
        
            }
        
            /**
             *  
             */     
            public function action_expiring()
            {   
        
                $total = 0;
                $values = array();
                for($i = 0; $i < 30; $i++)
                {
                    $timestamp = strtotime('+ '.$i.' days');
                    $values[date('d.m.Y', $timestamp)] = Model_User::query()->where(DB::expr('DAY(FROM_UNIXTIME(premium_until))'), '=', date('d', $timestamp))->where(DB::expr('MONTH(FROM_UNIXTIME(premium_until))'), '=', date('m', $timestamp))->where(DB::expr('YEAR(FROM_UNIXTIME(premium_until))'), '=', date('Y', $timestamp))->count();     
                    $total = ($total+$values[date('d.m.Y', $timestamp)]);
                }
        
                $data['values'] = $values;
                $data['total'] = $total;
        
                return Response::forge(View::forge('ghru4pxsa3/users/expiring.html.twig', isset($data) ? $data : array(), false));
        
            }
        
            /**
             *  
             */     
            public function action_index()
            {
                // Default package settings
                $defaults = [
                    [1, 'active', 'IPTV-DACH'],
                    [2, 'inactive', 'IPTV-DE HEVC'],
                    [3, 'active', 'IPTV-UK'],
                    [4, 'active', 'IPTV-Frankreich'],
                    [5, 'active', 'IPTV-Polen'],
                    [8, 'active', 'IPTV-Türkei'],
                    [9, 'active', 'IPTV-Rest von Europa'],
                    [10, 'active', 'IPTV-USA/Canada'],
                    [11, 'inactive', 'IPTV-Rest der Welt'],
                    [12, 'inactive', 'IPTV-World Sport'],
                    [24, 'active', 'IPTV-Music'],
                    [27, 'inactive', 'IPTV-XXX'],
                    [28, 'inactive', 'IPTV-VoD'],
                ];
            
                // Initialize user query
                $query = Model_User::query();
            
                // Filter by search query (username or email)
                $searchQuery = Input::post('query', '');
                if (!empty($searchQuery)) {
                    $query->where_open()
                          ->where('username', 'LIKE', '%' . addslashes($searchQuery) . '%')
                          ->or_where('email', 'LIKE', '%' . addslashes($searchQuery) . '%')
                          ->where_close();
                }
            
                // Handle credit time action
                if (Input::post('action', '') == 'credit_time') {
                    $user_id = Input::post('user_id', 0);
 
                        if (!$user_id) {
                            Messages::error('Invalid user ID');
                            Messages::redirect('ghru4pxsa3/users');
                        }
                    
                        $user = Model_User::find($user_id);
                        if (!$user) {
                            Messages::error('User not found');
                            Messages::redirect('ghru4pxsa3/users');
                        }

            
                    if ($user) {
                        $current_time = time();
                        $user->premium_until = max($user->premium_until, $current_time) + 86400;
                        $user->save();
            
                        // Send email notification
                        $email = Email::forge();
                        $email->to($user->email)
                              ->subject('[watchhd.to] Testline Aktivierung')
                              ->html_body(\View::forge('email/testline.html.twig', [
                                  'name' => $user->username,
                                  'valid_until' => date("d.m.Y H:i", $user->premium_until),
                              ]));
            
                        try {
                            $email->send();
                        } catch (\EmailSendingFailedException $e) {
                            // Handle email sending failure
                        }
            
                        // Update tickets
                        $tickets = Model_Ticket::query()
                            ->where('user_id', $user_id)
                            ->where('category', 'Testline')
                            ->get();
            
                        foreach ($tickets as $ticket) {
                            $reply = new Model_Ticket_Reply();
                            $reply->ticket_id = $ticket->id;
                            $reply->user_id = $this->user['uid'];
                            $reply->contents = "Dir wurden 24 Stunden Premium Laufzeit gutgeschrieben. Daten wie m3u EPG etc. findest du unter deinem Account. Pakete kannst du dort ebenfalls verwalten. Grüße";
                            $reply->name = $this->user['username'];
                            $reply->save();
            
                            $ticket->closed = 1;
                            $ticket->status = 'geschlossen';
                            $ticket->save();
                        }
            
                        // Update user packages
                        foreach ($defaults as $default) {
                            $packet = Model_User_Packet::query()
                                ->where('user_id', $user->id)
                                ->where('bouquet_id', $default[0])
                                ->get_one();
            
                            $status = in_array($default[0], [1, 2]) ? $default[1] : 'inactive';
                            $booked_until = ($status === 'active' || in_array($default[0], [8, 3]))
                                ? $user->premium_until
                                : 0;
            
                            if ($packet) {
                                $packet->booked_until = $booked_until;
                                $packet->save();
                            } else {
                                $new_packet = new Model_User_Packet();
                                $new_packet->name = $default[2];
                                $new_packet->user_id = $user->id;
                                $new_packet->status = $status;
                                $new_packet->line_type = 'mainline';
                                $new_packet->line_id = $user->id;
                                $new_packet->bouquet_id = $default[0];
                                $new_packet->booked_until = $booked_until;
                                $new_packet->save();
                            }
                        }
            
                        Messages::success('Test Time successfully added');
                        Messages::redirect('ghru4pxsa3/users');
                    }
                }
            
                // Filter by group
                $group = Input::post('group', '');
                if (!empty($group)) {
                    if ($group === 'premium') {
                        $query->where('premium_until', '>', time());
                    } else {
                        $query->where('group', $group);
                    }
                }
            
                // Configure pagination
                $pagination_config = [
                    'name' => 'bootstrap3',
                    'pagination_url' => \Uri::base() . 'ghru4pxsa3/users',
                    'per_page' => 100,
                    'total_items' => $query->count(),
                    'num_links' => 4,
                    'uri_segment' => 3,
                    'show_first' => true,
                    'show_last' => true,
                    'query_string' => [
                        'group' => $group,
                        'query' => $searchQuery,
                    ],
                ];
            
                $pagination = Pagination::forge('', $pagination_config);
                $query->order_by('created_at', 'DESC')
                      ->offset($pagination->offset)
                      ->limit($pagination->per_page);
            
                // Render view
                $data = [
                    'pagination' => $pagination->render(),
                    'users' => $query->get(),
                ];
            
                return Response::forge(View::forge('ghru4pxsa3/users/list.html.twig', $data));
            }
                          
            /**
             * 
             */
            public function action_payments($user_id)
            {
        
                $data['payments'] = Model_User_Payment::query()->where('user_id', '=', $user_id)->order_by('created_at', 'DESC')->get();
                return Response::forge(View::forge('ghru4pxsa3/users/payments.html.twig', isset($data) ? $data : array(), false)); 
        
            }
        
            /**
             * 
             */
            public function action_cash($user_id)
            {
        
                $data['stats']['leads']['today'] = Model_Affiliate_Log::query()->where('affiliate_id', '=', $user_id)->where('type', '=', 'lead')->where(DB::expr('DAY(FROM_UNIXTIME(created_at))'), '=', date('d'))->where(DB::expr('MONTH(FROM_UNIXTIME(created_at))'), '=', date('m'))->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y'))->count();
                $data['stats']['leads']['week'] = Model_Affiliate_Log::query()->where('affiliate_id', '=', $user_id)->where('type', '=', 'lead')->where(DB::expr('WEEK(FROM_UNIXTIME(created_at))'), '=', date('W'))->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y'))->count();
                $data['stats']['leads']['month'] = Model_Affiliate_Log::query()->where('affiliate_id', '=', $user_id)->where('type', '=', 'lead')->where(DB::expr('MONTH(FROM_UNIXTIME(created_at))'), '=', date('m'))->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y'))->count();
                $data['stats']['leads']['total'] = Model_Affiliate_Log::query()->where('affiliate_id', '=', $user_id)->where('type', '=', 'lead')->count();
        
                $data['stats']['earnings']['today'] = DB::select(array(DB::expr('SUM(affiliate_log.worth)'), 'sales'))->from('affiliate_log')->where('affiliate_id', '=', $user_id)->where(DB::expr('DAY(FROM_UNIXTIME(created_at))'), '=', date('d'))->where(DB::expr('MONTH(FROM_UNIXTIME(created_at))'), '=', date('m'))->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y'))->execute()[0]['sales'];
                $data['stats']['earnings']['week'] = DB::select(array(DB::expr('SUM(affiliate_log.worth)'), 'sales'))->from('affiliate_log')->where('affiliate_id', '=', $user_id)->where(DB::expr('WEEK(FROM_UNIXTIME(created_at))'), '=', date('W'))->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y'))->execute()[0]['sales'];
                $data['stats']['earnings']['month'] = DB::select(array(DB::expr('SUM(affiliate_log.worth)'), 'sales'))->from('affiliate_log')->where('affiliate_id', '=', $user_id)->where(DB::expr('MONTH(FROM_UNIXTIME(created_at))'), '=', date('m'))->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y'))->execute()[0]['sales'];
                $data['stats']['earnings']['total'] = DB::select(array(DB::expr('SUM(affiliate_log.worth)'), 'sales'))->from('affiliate_log')->where('affiliate_id', '=', $user_id)->execute()[0]['sales'];
        
        
                #$data['stats']['sales']['today'] = DB::select(array(DB::expr('SUM(affiliate_log.worth)'), 'sales'))->from('affiliate_log')->where(DB::expr('DAY(FROM_UNIXTIME(created_at))'), '=', date('d'))->where(DB::expr('MONTH(FROM_UNIXTIME(created_at))'), '=', date('m'))->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y'))->execute()[0]['sales'];
                #$data['stats']['sales']['week'] = DB::select(array(DB::expr('SUM(affiliate_log.amount)'), 'sales'))->from('affiliate_log')->where(DB::expr('WEEK(FROM_UNIXTIME(created_at))'), '=', date('W'))->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y'))->execute()[0]['sales'];
                #$data['stats']['sales']['month'] = DB::select(array(DB::expr('SUM(affiliate_log.amount)'), 'sales'))->from('affiliate_log')->where(DB::expr('MONTH(FROM_UNIXTIME(created_at))'), '=', date('m'))->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y'))->execute()[0]['sales'];
        
                #$data['stats']['users']['total'] = Model_User::query()->count();
                #$data['stats']['users']['active'] = Model_User::query()->where('last_login', '>', (time()-604800))->count();
        
        
                return Response::forge(View::forge('ghru4pxsa3/users/cash.html.twig', isset($data) ? $data : array(), false)); 
        
            }
        
            /**
             * 
             */
            public function action_create()
            {
        
                if(Input::method() == 'POST')
                {
        
                    $username = trim(Input::post('username', ''));
                    $email = trim(Input::post('email', ''));
                    $password = trim(Input::post('password', ''));
                    $group = trim(Input::post('group', ''));
                    $premium_until = trim(Input::post('premium_until', date('Y-m-d')));
                    $tokens = trim(Input::post('tokens', ''));
                    $referred_user_id = trim(Input::post('referred_user_id', 0));
                    $affiliate_tracking_id = trim(Input::post('affiliate_tracking_id', ''));
        
                    $errors = array();
        
                    # username
                    if(empty($username))
                    {
                        $errors[] = 'Bitte Benutzernamen angeben.';    
                    }
        
                    if(!empty($username) && !preg_match('/^[A-Za-z][A-Za-z0-9]{3,20}$/', $username))
                    {
                        $errors[] = 'Der Benutzername enthält ungültige Zeichen.';  
                    }
        
                    if(!empty($username) && Model_User::query()->where('username', '=', $username)->count())
                    {
                        $errors[] = 'Dieser Benutzername wird bereits genutzt.';
                    }
        
                    # email
                    if(empty($email))
                    {
                        $errors[] = 'Bitte Email Adresse angeben.';
                    }
        
                    if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL))
                    {
                        $errors[] = 'Bitte gültige Email Adresse angeben.';
                    }
        
                    if(!empty($email) && Model_User::query()->where('email', '=', $email)->count())
                    {
                        $errors[] = 'Diese Email Adresse wird bereits genutzt.';    
                    }
        
                    # password
                    if(empty($password))
                    {
                        $errors[] = 'Bitte Passwort angeben.';
                    }
        
                    # group
                    if(!in_array($group, array('1', '25', '50', '75', '100')))
                    {
                        $errors[] = 'Bitte Benutzergruppe angeben.';  
                    }
        
                    # premium_until
                    if(!empty($premium_until) && $premium_until != '1970-01-01' && !strtotime($premium_until))
                    {
                        $errors[] = 'Bitte Premium Laufzeit setzen.';
                    }
        
                    # tokens
                    if(!ctype_digit($tokens))
                    {
                        $errors[] = 'Bitte Rekorder Token Anzahl angeben.';  
                    }
        
                    # referred_user_id
                    if($referred_user_id != 0 && !Model_User::query()->where('id', '=', $referred_user_id)->count())
                    {
                        $errors[] = 'Dieser Benutzer existiert nicht. (Geworben durch)';
                    }
        
                    # affiliate_tracking_id
                    if(!empty($affiliate_tracking_id) && !ctype_alpha($affiliate_tracking_id))
                    {
                        $errors[] = 'Tracking ID hat ein ungültiges Format.';
                    }
        
                    if(count($errors) == 0)
                    {
        
                        $activation_token = Str::random('sha1');
        
                        $user = new Model_User;
                        $user->username = $username;
                        $user->group = $group;
                        $user->email = $email;
                        $user->password = Auth::instance()->hash_password($password);
                        $user->last_login = time();
                        $user->login_hash = Str::random('sha1');
                        $user->profile_fields = '';
                        $user->password_recovery_token = '';
                        $user->password_recovery_token_requested_at = '';
                        $user->activated = 1;
                        $user->tokens = $tokens;
                        $user->premium_until = strtotime($premium_until);
                        $user->activated_at = time();
                        $user->activation_token = $activation_token;
                        $user->email_notification_premium_ends = 1;
                        $user->email_notification_record_download_available = 1;
                        $user->email_notification_friend_sale = 1;
                        $user->referred_user_id = $referred_user_id;
                        $user->affiliate_id = 0;
                        $user->affiliate_tracking_id = $affiliate_tracking_id;
                        $user->reminder_send_at = 0;
                        $user->save();
        
                        \Messages::success('Benutzerkonto wurde erstellt.');
                        \Messages::redirect('ghru4pxsa3/users');
        
                    }
                    else
                    {
        
                        Session::set_flash('input.old', Input::post());
        
                        \Messages::error(implode('<br />', $errors));
                        \Messages::redirect(Uri::current());
        
                    }
        
                }
        
                $data['password'] = Str::random('alnum', 12);
        
                return Response::forge(View::forge('ghru4pxsa3/users/create.html.twig', isset($data) ? $data : array(), false)); 
        
            }
        
            /**
             * 
             */
            public function action_edit($user_id)
            {
                
                $defaults = array();
                $defaults[] = array(1, 'active', 'IPTV-DACH');
                $defaults[] = array(2, 'inactive', 'IPTV-DE HEVC');
                $defaults[] = array(3, 'active', 'IPTV-UK');
                $defaults[] = array(4, 'active', 'IPTV-Frankreich');
                $defaults[] = array(5, 'active', 'IPTV-Polen');
                $defaults[] = array(8, 'active', 'IPTV-Türkei');
                $defaults[] = array(9, 'active', 'IPTV-Rest von Europa');
                $defaults[] = array(10, 'active', 'IPTV-USA/Canada');
                $defaults[] = array(11, 'inactive', 'IPTV-Rest der Welt');
                $defaults[] = array(12, 'inactive', 'IPTV-World Sport');
                $defaults[] = array(24, 'active', 'IPTV-Music');
                $defaults[] = array(27, 'inactive', 'IPTV-XXX');
                $defaults[] = array(28, 'inactive', 'IPTV-VoD');
                // hier müssen die defaults wieder geadded werden
        
                $user = Model_User::query()->where('id', '=', $user_id)->get_one();
                if(!$user) throw new HttpNotFoundException;
        
                if(Input::method() == 'POST')
                {
        
                    $username = trim(Input::post('username', ''));
                    $email = trim(Input::post('email', ''));
                    $group = trim(Input::post('group', ''));
                    $exp_date = Input::post('exp_date', '');
                    $tokens = trim(Input::post('tokens', ''));
                    $affiliate_tracking_id = trim(Input::post('affiliate_tracking_id', ''));
                    $password1 = trim(Input::post('password1', ''));
                    $password2 = trim(Input::post('password2', ''));
                    $line_password = trim(Input::post('line_password', ''));
    
                    $exp_date = "{$exp_date['date']} {$exp_date['time']}";
                    
                    $errors = array();
        
                    # username
                    if(empty($username))
                    {
                        $errors[] = 'Bitte Benutzernamen angeben.';    
                    }
        
                    if(!empty($username) && !preg_match('/^[A-Za-z][A-Za-z0-9]{3,20}$/', $username))
                    {
                        $errors[] = 'Der Benutzername enthält ungültige Zeichen.';  
                    }
        
                    if(!empty($username) && Model_User::query()->where('username', '=', $username)->where('id', '!=', $user_id)->count())
                    {
                        $errors[] = 'Dieser Benutzername wird bereits genutzt.';
                    }
        
                    # email
                    if(empty($email))
                    {
                        $errors[] = 'Bitte Email Adresse angeben.';
                    }
        
                    if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL))
                    {
                        $errors[] = 'Bitte gültige Email Adresse angeben.';
                    }
        
                    if(!empty($email) && Model_User::query()->where('email', '=', $email)->where('id', '!=', $user_id)->count())
                    {
                        $errors[] = 'Diese Email Adresse wird bereits genutzt.';    
                    }
        
                    # group
                    if(!in_array($group, array('1', '25', '50', '75', '100')))
                    {
                        $errors[] = 'Bitte Benutzergruppe angeben.';  
                    }
        
                    # premium_until
                    if(!empty($exp_date) && date("Y", strtotime($exp_date)) != 1970 && !strtotime($exp_date))
                    {
                        $errors[] = 'Bitte Premium Laufzeit setzen.';
                    }
        
                    /*
                    if(!empty($premium_until) && strtotime($premium_until) && strtotime($premium_until) < time())
                    {
                        $errors[] = 'Premium Laufzeit muss irgendwann in der Zukunft enden.';
                    }
                    */
        
                    # affiliate_tracking_id
                    if(!empty($affiliate_tracking_id) && !ctype_alpha($affiliate_tracking_id))
                    {
                        $errors[] = 'Tracking ID hat ein ungültiges Format.';
                    }
        
                    if(!empty($password1) && mb_strlen($password1) < 8)
                    {
                        $errors[] = 'Password zu kurz.';
                    }
        
                    if($password2 != $password1)
                    {
                        $errors[] = 'Passwort wiederholen muss mit Passwort übereinstimmen.';
                    }
                    
                    if(empty($line_password))
                    {
                        $errors[] = 'Line Passwort fehlt';
                    }
        
                    if(count($errors) == 0)
                    {
                        
                        
        
                        $user->username = $username;
                        $user->email = $email;
                        $user->group = $group;
                        $user->tokens = 0;
                        $user->affiliate_tracking_id = $affiliate_tracking_id;
                        if(!empty($password1))
                        {
                            $user->password = Auth::instance()->hash_password($password1);
                        }
                        if(!empty($exp_date))
                        {
                            $user->premium_until = strtotime($exp_date);   
                        } 
                        $user->line_password = $line_password;
                        $user->save();
                        
                        foreach($defaults as $key => $default){
                            

                                    $packet = Model_User_Packet::query()->where('user_id', $user->id)->where('bouquet_id', $default[0])->get_one();
                                    if($packet){
                                        
                                        /*
                                        $booked_packages = isset($data['packages']) ? $data['packages'] : array(24, 35);
                                        $status = in_array($default[0], $booked_packages) ? $default[1] : 0;

                                        $packet->booked_until = ($packet->booked_until == 0 ? (time()+$add) : ($packet->booked_until+$add));
                                        $packet->status = $status;
                                        $packet->save();
                                        */
                                        $status = in_array($default[0], array(1, 2)) ? $default[1] : 'inactive';
                                        $packet->booked_until = ($status == 'active' || in_array($default[0], array(8,3)) ? $user->premium_until : 0);
                                        $packet->save();
                                        
                                        
                                    }
                                    else{

                                        $status = in_array($default[0], array(1, 2)) ? $default[1] : 'inactive';
                                        
                                        $packet = new Model_User_Packet;
                                        $packet->name = $default[2];
                                        $packet->user_id = $user->id;
                                        $packet->status = $status;
                                        $packet->line_type = 'mainline';
                                        $packet->line_id = $user->id;
                                        $packet->bouquet_id = $default[0];
                                        $packet->booked_until = ($status == 'active' || in_array($default[0], array(8,3)) ? $user->premium_until : 0);
                                        $packet->save();
                                        
                                        
                                    }

                                }
                                
                                
        
                        \Messages::success('Änderungen wurden übernommen.');
                        \Messages::redirect('ghru4pxsa3/users');
        
                    }
                    else
                    {
        
                        Session::set_flash('input.old', Input::post());
        
                        \Messages::error(implode('<br />', $errors));
                        \Messages::redirect(Uri::current());
        
                    }
        
                }
                $last_payment = Model_User_Payment::query()
                    ->where('user_id', $user_id)
                    ->where('status', 'paid')
                    ->where('data', 'LIKE', '%multiline%')
                    ->order_by('id', 'desc')
                    ->get_one();

                $last_payment_data = isset($last_payment) ? json_decode($last_payment->data, true) : [];
                $data['mSTAT'] = isset($last_payment_data['buy']['multiline']) ? $last_payment_data['buy']['multiline'] : '0';
                $data['user'] = $user;
        
                return Response::forge(View::forge('ghru4pxsa3/users/edit.html.twig', isset($data) ? $data : array(), false)); 
        
            }
        
            /**
             * 
             */
            public function post_ban($user_id)
            {
        
        

        
                $user = Model_User::query()->where('id', '=', $user_id)->get_one();
                if(!$user) throw new HttpNotFoundException;
        
                $user->suspended = 1;
                $user->save();
        
                \Messages::success('Benutzer wurde gebannt!');
                \Messages::redirect('ghru4pxsa3/users');
        
            }
        
            /**
             * 
             */
            public function post_unban($user_id)
            {
        
        

                $user = Model_User::query()->where('id', '=', $user_id)->get_one();
                if(!$user) throw new HttpNotFoundException;
        
                $user->suspended = 0;
                $user->save();
        
                \Messages::success('Benutzer wurde entbannt!');
                \Messages::redirect('ghru4pxsa3/users');
        
            }
        
            /**
             * 
             */
            public function post_delete($user_id)
            {
        
        

                $user = Model_User::query()->where('id', '=', $user_id)->get_one();
                if(!$user) throw new HttpNotFoundException;
        
                DB::delete('user_recordings')->where('user_id', '=', $user_id)->execute();
                DB::delete('user_payments')->where('user_id', '=', $user_id)->execute();
                DB::delete('tokens')->where('user_id', '=', $user_id)->execute();
                DB::delete('channel_heartbeats')->where('user_id', '=', $user_id)->execute();
        
                $user->delete();
        
                \Messages::success('Benutzer wurde gelöscht!');
                \Messages::redirect('ghru4pxsa3/users');
        
            }
        
        
        }