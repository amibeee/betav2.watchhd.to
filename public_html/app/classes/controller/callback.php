        <?php  
        class Controller_Callback extends Controller_Base_Public
        {
        
            /**
             * 
             */
            public function post_index($token)
            {
                
                $merchant_id = 'c5563b58156c2b0522691e2043c9cd28';
                $secret = 'TGd12ksZX2ngH';
                
                if (!isset($_SERVER['HTTP_HMAC']) || empty($_SERVER['HTTP_HMAC'])) {
                    throw new \Exception("No HMAC signature sent");
                }

                $merchant = isset($_POST['merchant']) ? $_POST['merchant']:'';
                if (empty($merchant)) {
                    throw new \Exception("No Merchant ID passed");
                }

                if ($merchant != $merchant_id) {
                    throw new \Exception("Invalid Merchant ID");
                }

                $request = file_get_contents('php://input');
                if ($request === FALSE || empty($request)) {
                    throw new \Exception("Error reading POST data");
                }

                $hmac = hash_hmac("sha512", $request, $secret);
                if ($hmac != $_SERVER['HTTP_HMAC']) {
                    throw new \Exception("HMAC signature does not match");
                }

        
            # if(Input::server('PHP_AUTH_USER', '') == "4aed61cb5da26b2a03d6e21dc9de2699" && Input::server('PHP_AUTH_PW', '') == "AgZ17ksZX2ngP")
            # {
        
                    $payment = Model_User_Payment::query()->where('token', '=', $token)->where('status', '!=', 'paid')->get_one();
                    if(!$payment) throw new HttpNotFoundException;
        
                    $user = Model_User::query()->where('id', '=', $payment->user_id)->get_one();
                    if(!$user) throw new HttpServerErrorException;
        
                    // Check the original currency to make sure the buyer didn't change it. 
                    if(Input::post('currency1') != 'EUR')
                    { 
                                throw new \Exception('IPN Error: Original currency mismatch!');
                    }     
        
                    // Check amount against order total 
                    if(Input::post('amount1') < $payment->amount)
                    { 
                                throw new \Exception('IPN Error: Amount is less than order total!'); 
                    } 
        
                    if (Input::post('status') >= 100 || Input::post('status') == 2)
                    { 
        
                        $payment->status = 'paid';
                        $payment->save();
        
                        /* Sale Tracking */
                        if($affiliate = Model_User::query()->where('id', '=', $user->affiliate_id)->get_one())
                        {
        
                            $data['product'] = $payment->product;
                            $data['amount'] = $payment->amount;
                            $data['type'] = $payment->type;
                            $data['payment_id'] = $payment->id;
                            $data['method'] = $payment->method;
        
                            $log = new Model_Affiliate_Log;
                            $log->affiliate_id = $affiliate->id;
                            $log->user_id = $user->id;
                            $log->type = 'sale';
                            $log->data = json_encode($data);
                            $log->worth = $payment->amount;
                            $log->sub_id = '';
        
                            /* sub_id holen */
                            $lead = Model_Affiliate_Log::query()->where('affiliate_id', '=', $affiliate->id)->where('user_id', '=', $user->id)->get_one();
                            if($lead) $log->sub_id = $lead->sub_id;
        
                            $log->save();
        
                        }
                        /* Sale Tracking */
        
                        $data = json_decode($payment->data, true);
        
                        switch($payment->type)
                        {
        
                            case 'premium':
        
                                if(!isset($data['premium_days'])) throw new \Exception('IPN Error: Invalid Data Object!');
        
                                $add = ($data['premium_days']*86400);
        
                                if($data['buy']['type'] == 'subline')
                                {
                                    $user = Model_User_Line::query()->where('user_id', $user->id)->where('username', $data['buy']['username'])->get_one();
                                }
        
                                if($user->premium_until  == 0) 
                                {
        
                                    $base = time();
                                    $user->premium_until = ($base+$add);
        
                                }        
                                else
                                {
        
                                    if($user->premium_until > time())
                                    {
                                        $user->premium_until = ($user->premium_until+$add);
                                    }
                                    else
                                    {
        
                                        $base = time();
                                        $user->premium_until = ($base+$add);
        
                                    }
        
                                }
        
                                $user->save(); 

                                $defaults = array();			
                                $defaults[] = array(1, 'active', 'IPTV-DACH', true); 
                                $defaults[] = array(2, 'inactive', 'IPTV-DE HEVC', true); 
                                $defaults[] = array(3, 'active', 'IPTV-UK', true);
                                $defaults[] = array(4, 'active', 'IPTV-Frankreich', true);
                                $defaults[] = array(5, 'active', 'IPTV-Polen', true);
                                $defaults[] = array(8, 'active', 'IPTV-Türkei', true);
                                $defaults[] = array(9, 'active', 'IPTV-Rest von Europa', true);
                                $defaults[] = array(10, 'active', 'IPTV-USA/Canada', true);
                                $defaults[] = array(11, 'inactive', 'IPTV-Rest der Welt', true);
                                $defaults[] = array(12, 'inactive', 'IPTV-World Sport', true);
                                $defaults[] = array(24, 'active', 'IPTV-Music', true);
                                $defaults[] = array(27, 'inactive', 'IPTV-XXX', true);
                                $defaults[] = array(28, 'inactive', 'IPTV-VoD', true);
            

                                $type = isset($data['buy']['type']) && $data['buy']['type'] == 'subline' ? 'subline' : 'mainline'; 
                                $line_id = $data['buy']['line_id'];

                                foreach($defaults as $default){

                                    $packet = Model_User_Packet::query()->where('user_id', $user->id)->where('bouquet_id', $default[0])->get_one();
                                    if($packet){

                                        $booked_packages = isset($data['packages']) ? array_values($data['packages']) : array(1,3,4,5,8,9,10,24); // fas noch
                                        
                                        $booked_packages_expanded = $booked_packages;
                                        $booked_packages_expanded[] = 2;
                                        $booked_packages_expanded[] = 3;
                                        $booked_packages_expanded[] = 4;
                                        $booked_packages_expanded[] = 5;
                                        $booked_packages_expanded[] = 8; // 2 & 3 gibt es doch nimma?
                                        $booked_packages_expanded[] = 9;
                                        $booked_packages_expanded[] = 10;
                                        $booked_packages_expanded[] = 11;
                                        $booked_packages_expanded[] = 12;
                                        $booked_packages_expanded[] = 24;
                                        $booked_packages_expanded[] = 27;
                                        $booked_packages_expanded[] = 28;
                                        
                                        // dann musst du xxx & vod auch hier nach unden baccke
                                        
                                        $booked_packages_expanded = array_unique($booked_packages_expanded);
                                        $status = in_array($default[0], $booked_packages) or $default[3]  ? $default[1] : 'inactive';
                                        
                                        if(in_array($default[0], $booked_packages_expanded) or $default[3]){
                                            $packet->booked_until = ($packet->booked_until < time() ? (time()+$add) : ($packet->booked_until+$add));
                                        }
                                        $packet->status = $status;
                                        $packet->save();

                                    }
                                    else{

                                        $booked_packages = isset($data['packages']) ? array_values($data['packages']) : array(1,3,4,5,8,9,10,24); // hier auch noch
                                        
                                        $booked_packages_expanded = $booked_packages;
                                        $booked_packages_expanded[] = 2;
                                        $booked_packages_expanded[] = 3;
                                        $booked_packages_expanded[] = 4;
                                        $booked_packages_expanded[] = 5;
                                        $booked_packages_expanded[] = 8; // 2 & 3 gibt es doch nimma?
                                        $booked_packages_expanded[] = 9;
                                        $booked_packages_expanded[] = 10;
                                        $booked_packages_expanded[] = 11;
                                        $booked_packages_expanded[] = 12;
                                        $booked_packages_expanded[] = 24;
                                        $booked_packages_expanded[] = 27;
                                        $booked_packages_expanded[] = 28;
                                        $booked_packages_expanded = array_unique($booked_packages_expanded);
                                        $status = in_array($default[0], $booked_packages) or $default[3] ? $default[1] : 'inactive';
                                        
                                        $packet = new Model_User_Packet;
                                        $packet->name = $default[2];
                                        $packet->user_id = $user->id;
                                        $packet->status = $status;
                                        $packet->line_type = $type;
                                        $packet->line_id = $line_id;
                                        $packet->bouquet_id = $default[0];
                                        if(in_array($default[0], $booked_packages_expanded)  or $default[3]){
                                            
                                            /*
                                            * kannst du sagen ob es nur bei neuen packages ist oder bei existierenden? Also quasi erst bestellungen oder al
                                            */
                                            
                                            if($packet->booked_until == 0){
                                                $packet->booked_until = (time()+$add);
                                            }
                                            else{
                                                
                                                if($packet->booked_until < time()){
                                                    $packet->booked_until = time(); // also hier wird das (exp)datum auf die aktuelle zeit gesetzt
                                                }
                                                $packet->booked_until = ($packet->booked_until+$add); // und hier die gebuchte laufzeit aufgerechnet müsste doch richtig sein?
                                                
                                            }
                                            
                                        }
                                        else{
                                            $packet->booked_until = time();
                                        }
                                        $packet->save();

                                    }

                                }


                                // vom user gebuchte pakete hinzufügen

        
                            break;
        
        
                        }
                        
                        return new Response('IPN OK');
        
                    } else if (Input::post('status') == -1) {
                        
                        $payment->status = 'timeout';
                        $payment->save();
                        
                        return new Response('payment timeout');
                        
                    } else if (Input::post('status') < 0 && Input::post('status') != -1 ) { 
                        //payment error, this is usually final but payments will sometimes be reopened if there was no exchange rate conversion or with seller consent 
                        return new Response('payment error'); 
                    } else { 
                        return new Response('payment is pending'); 
                    } 
        
                    return new Response('IPN OK'); 
        
                #}
        
            }
        
        }