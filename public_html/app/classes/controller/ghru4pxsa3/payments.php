






<?php
    class Controller_Ghru4pxsa3_Payments extends Controller_Base_Admin
    {
     
        /**
         *  
         */     
public function action_index()
{
    // Base query to retrieve payments and join with users
    $query = DB::select('user_payments.*', 'users.username')->from('user_payments');
    $query->join('users', 'LEFT');
    $query->on('users.id', '=', 'user_payments.user_id');

    // Adding search filter based on 'query' input from user
    if (Input::post('query', '') != '') {
        $search = Input::post('query');
        $query->where_open();
        $query->where('users.username', 'LIKE', '%' . $search . '%');
        $query->or_where('users.email', 'LIKE', '%' . $search . '%');
        $query->or_where('user_payments.method', 'LIKE', '%' . $search . '%');
        $query->where_close();
    }

    // Additional filters for method and status
    if (Input::post('m', '') != '') {
        $query->where('user_payments.method', '=', Input::post('m'));
    }

    if (Input::post('status', '') != '') {
        $query->where('user_payments.status', '=', Input::post('status'));
    }

    // Configure pagination
    $pagination_config = array(
        'name' => 'bootstrap3',
        'pagination_url' => \Uri::base() . 'ghru4pxsa3/payments?method=' . Input::post('m') . '&status=' . Input::post('status') . '&query=' . Input::post('query', ''),
        'per_page' => 100,
        'total_items' => count($query->execute()),
        'num_links' => 4,
        'uri_segment' => 3,
        'show_first' => true,
        'show_last' => true,
    );

    $pagination = Pagination::forge('', $pagination_config);

    $per_page = $pagination->per_page;
    $offset = $pagination->offset;

    $query->order_by('user_payments.created_at', 'DESC');
    $query->offset($offset);
    $query->limit($pagination->per_page);

    $data['pagination'] = Pagination::instance('')->render();
    $data['payments'] = $query->execute();
    
    // Handle form submission via POST


        $method = Input::post('method', '');
        $from = Input::post('from', '');
        $till = Input::post('till', '');
        $errors = array();

    if ($method != '' && $from!= '' && $till!='') {

        // Validate the form inputs
        if (!in_array($method, array('psc', 'bitcoin', 'monero', 'amazoncode', 'all', 'trocador'))) {
            $errors[] = 'Unbekannte Währung.';
        }

        if (!preg_match("~[0-9]{4}-[0-9]{2}-[0-9]{2}~", $from)) {
            $errors[] = 'Ungültiges Datem Format (links)';
        }

        if (!preg_match("~[0-9]{4}-[0-9]{2}-[0-9]{2}~", $till)) {
            $errors[] = 'Ungültiges Datem Format (rechts)';
        }

        // Proceed if there are no errors
        if (count($errors) == 0) {

            // Deleting records based on the filter
            $query = DB::delete('user_payments');
            if ($method != 'all') {
                $query->where('method', $method);
            }
            $query->where(DB::expr('FROM_UNIXTIME(created_at)'), 'BETWEEN', array($from, $till))->execute();

            Messages::success('Zahlungen wurden bereinigt.');
        } else {
            Messages::error(implode('<br />', $errors));
        }

        // Avoid redirect loop: only redirect after processing, not when GET request is active
        return Response::forge(View::forge('ghru4pxsa3/payments/list.html.twig', isset($data) ? $data : array(), false));
    }

    // Return view if the method is GET or no form is submitted
    return Response::forge(View::forge('ghru4pxsa3/payments/list.html.twig', isset($data) ? $data : array(), false));
}

        
        /**
         * 
         */
        public function action_view($payment_id)
        {
     
     
            $payment = Model_User_Payment::query()->where('id', '=', $payment_id)->get_one();
            if(!$payment) throw new HttpNotFoundException;
     
            $data['payment'] = $payment;
     
            return Response::forge(View::forge('ghru4pxsa3/payments/view.html.twig', isset($data) ? $data : array(), false)); 
     
        }
     
        /**
         * 
         */
        public function post_close($payment_id)
        {

     
            $payment = Model_User_Payment::query()->where('id', '=', $payment_id)->get_one();
            if(!$payment) throw new HttpNotFoundException;
     
            if($payment->status == 'paid')
            {
                \Messages::warning('Diese Zahlung wurde bereits abgeschlossen.');
                \Messages::redirect('ghru4pxsa3/payments');
            }
     
            $user = Model_User::query()->where('id', '=', $payment->user_id)->get_one();
            if(!$user) throw new HttpServerErrorException;
            $_user = $user;
     
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
                        $log->sub_id = '';
    					$log->worth = ($payment->amount/100*15);
     
                        /* sub_id holen */
                        $lead = Model_Affiliate_Log::query()->where('affiliate_id', '=', $affiliate->id)->where('user_id', '=', $user->id)->where('type', '=', 'lead')->get_one();
                        if($lead) $log->sub_id = $lead->sub_id;
     
                        $log->save();
     
                    }
                    /* Sale Tracking */
     
    				$data = json_decode($payment->data, true);
     
                    switch($payment->type)
                    {
     
                        case 'premium':
     
                            if(!isset($data['premium_days'])) throw new HttpBadRequestException;
     
                            $add = ($data['premium_days']*86400);
							
							// pakete virtuell aufschalten ..

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
							
							$exp_date = null;
							if($type == 'mainline'){
								$exp_date = $_user->premium_until;
                                

							}
							
							if($type == 'subline'){
								
								$user_line = Model_User_Line::query()->where('id', $line_id)->get_one();
								if($user_line){
									$exp_date = $user_line->premium_until; 
								}
								
							}
							
	
							
							// hey? wenn dann $exp_date unter aktuellen timestamp ist vermutlich auf aktuellen setzen oder?
							if(is_null($exp_date)) throw new \Exception('muhhh');
							
							if($exp_date < time()) $exp_date = time();
							$exp_date = ($exp_date+$add);

                            foreach($defaults as $default){

                                $packet = Model_User_Packet::query()->where('user_id', $user->id)->where('line_id', $line_id)->where('bouquet_id', $default[0])->get_one();
                                if($packet){
									
                                    $booked_packages = isset($data['packages']) ? array_values($data['packages']) : array(1,3,4,5,8,9,10,24); // da müssen die default rein
		
									/*
									if($default[0] == 1){
										$packet->status = in_array(1, $booked_packages) ? 'active' : 'inactive';
										$packet->booked_until = $exp_date;
									}
									*/
									
									$packet->status = in_array($default[0], $booked_packages) ? 'active' : 'inactive';
									$packet->booked_until = $exp_date;
									
									/*
									if($default[0] == 2){
										$packet->status = in_array(2, $booked_packages) ? 'active' : 'inactive'; 
										if($packet->booked_until == 0){
											$packet->booked_until = (time()+$add);
										}
										else{
											if($packet->booked_until < time()){
												$packet->booked_until = time();
											}
											$packet->booked_until = ($packet->booked_until+$add);
										}
									}
									
									if($default[0] == 3){
										$packet->status = in_array(3, $booked_packages) ? 'active' : 'inactive'; 
										if($packet->booked_until == 0){
											$packet->booked_until = (time()+$add);
										}
										else{
											if($packet->booked_until < time()){
												$packet->booked_until = time();
											}
											$packet->booked_until = ($packet->booked_until+$add);
										}
									}
									
									if($default[0] == 11){mer 
										$packet->status = in_array(11, $booked_packages) ? 'active' : 'inactive'; 
										if($packet->booked_until == 0){
											$packet->booked_until = (time()+$add);
										}
										else{
											if($packet->booked_until < time()){
												$packet->booked_until = time();
											}
											$packet->booked_until = ($packet->booked_until+$add);
										}
									}
									*/
									/*
									if($default[0] == 11){
										$packet->status = in_array(11, $booked_packages) ? 'active' : 'inactive';
										if($packet->booked_until == 0){
											$packet->booked_until = (time()+$add);
											
										}
										else{
											if($packet->booked_until < time()){
												$packet->booked_until = time();
											}
											$packet->booked_until = ($packet->booked_until+$add);
										}
										/*
										if(in_array(31, $booked_packages)){
											//$packet->booked_until = ($packet->booked_until == 0 ? (time()+$add) : (time()+$add));
										
										}
										else{
											$packet->booked_until = ($packet->booked_until > 0 ? $packet->booked_until : 0);
										}
										*/
										/*
										if(in_array($default[0], $booked_packages)){
										
											if($packet->booked_until == 0){
												$packet->booked_until = (time()+$add);
											}
											else{
											
												if($packet->booked_until < time()){
													$packet->booked_until = time();
												}
												$packet->booked_until = ($packet->booked_until+$add);
											}
										
										}
									
																				
									}
									*/
									
									/*
									if($default[0] == 8){
										$packet->status = in_array(8, $booked_packages) ? 'active' : 'inactive'; 
										$packet->booked_until = ($packet->booked_until == 0 ? (time()+$add) : ($packet->booked_until+$add));
									}
									*/
									
									
									
									$packet->save();
									
									
                                }
                                else{
									/*
									$booked_packages = isset($data['packages']) ? array_values($data['packages']) : array(24, 35);
                                    $status = in_array($default[0], $booked_packages) ? $default[1] : 'inactive';
                                    
                                    $packet = new Model_User_Packet;
                                    $packet->user_id = $user->id;
									$packet->name = $default[2];
                                    $packet->status = $status;
                                    $packet->line_type = $type;
                                    $packet->line_id = $line_id;
                                    $packet->bouquet_id = $default[0];
                                    //$packet->booked_until = (time()+$add);
									if(in_array($default[0], $booked_packages) or in_array($default[0], array(24,35,23,45) )){
										$packet->booked_until = (time()+$add);
									}
									if(in_array($default[0], $booked_packages)){
										$packet->booked_until = ($packet->booked_until == 0 ? (time()+$add) : ($packet->booked_until+$add));
									}
									else{
										$packet->booked_until = 0;
									}
                                    $packet->save();
									*/
									
									$booked_packages = isset($data['packages']) ? array_values($data['packages']) : array(1,3,4,5,8,9,10,24);
								
									$packet = new Model_User_Packet;
                                    $packet->user_id = $user->id;
									$packet->name = $default[2];
                                    $packet->line_type = $type;
                                    $packet->line_id = $line_id;
                                    $packet->bouquet_id = $default[0];
								
									if($default[0] == 1){
										$packet->status = in_array(1, $booked_packages) ? 'active' : 'inactive';
										$packet->booked_until = $exp_date;
									}
									
									if($default[0] == 2){
										$packet->status = in_array(2, $booked_packages) ? 'active' : 'inactive'; 
										$packet->booked_until = $exp_date;
									}
									
									if($default[0] == 3){
										$packet->status = in_array(3, $booked_packages) ? 'active' : 'inactive'; 
										$packet->booked_until = $exp_date;
									}
									
									if($default[0] == 4){
										$packet->status = in_array(4, $booked_packages) ? 'active' : 'inactive'; 
										$packet->booked_until = $exp_date;
									}
				
									if($default[0] == 5){
										$packet->status = in_array(5, $booked_packages) ? 'active' : 'inactive'; 
										$packet->booked_until = $exp_date;
									}
									
									if($default[0] == 8){
										$packet->status = in_array(8, $booked_packages) ? 'active' : 'inactive'; 
										$packet->booked_until = $exp_date;
									}
									
									if($default[0] == 9){
										$packet->status = in_array(9, $booked_packages) ? 'active' : 'inactive'; 
										$packet->booked_until = $exp_date;
									}
									
									if($default[0] == 10){
										$packet->status = in_array(10, $booked_packages) ? 'active' : 'inactive'; 
										$packet->booked_until = $exp_date;
									}
									
									if($default[0] == 11){
										$packet->status = in_array(11, $booked_packages) ? 'active' : 'inactive'; 
										$packet->booked_until = $exp_date;
									}
									
									if($default[0] == 12){
										$packet->status = in_array(12, $booked_packages) ? 'active' : 'inactive'; 
										$packet->booked_until = $exp_date;
									}
									
									if($default[0] == 24){
										$packet->status = in_array(24, $booked_packages) ? 'active' : 'inactive'; 
										$packet->booked_until = $exp_date;
									}
									
									if($default[0] == 27){
										$packet->status = in_array(27, $booked_packages) ? 'active' : 'inactive'; 
										$packet->booked_until = $exp_date;
									}
									
									if($default[0] == 28){
										$packet->status = in_array(28, $booked_packages) ? 'active' : 'inactive'; 
										$packet->booked_until = $exp_date;
									}
									
									$packet->save();
									
                                }

                            }
							

                            // vom user gebuchte pakete hinzufügen
                            $payment = Model_User_Payment::find($payment_id);
                            $payment_data = json_decode($payment->data, true);
                            
                            // Access the multiline value
                            if(method_exists($payment_data['buy'], 'multiline')){
                                $multiline_value = $payment_data['buy']['multiline'];
                            }
                            if($data['buy']['type'] == 'subline')
                            {
                                $user_line = Model_User_Line::query()->where('user_id', $user->id)->where('username', $data['buy']['username'])->get_one();
                                if($user_line->premium_until  == 0 || $user_line->premium_until < time()) $user_line->premium_until  = time();
                                $user_line->premium_until = ($user_line->premium_until+$add);
                                if(isset($multiline_value)){
                                    $user_line->multiline = $multiline_value;
                                }
                                $user_line->save(); 

                            }
                            else if($data['buy']['type'] == 'mainline') {
                                if($user->premium_until  == 0 || $user->premium_until < time()) $user->premium_until  = time();
                                $user->premium_until = ($user->premium_until+$add);
                                
                                if(isset($multiline_value)){        
                                    $user->multiline = $multiline_value;
                                }
                                $user->save();                          
                            }
     

                            if($_user->referred_user_id)
                            {
     
                                $referred_user = Model_User::query()->where('id', '=', $_user->referred_user_id)->get_one();
                                if($referred_user)
                                {
     
                                    if(Model_User_Payment::query()->where('user_id', '=', $_user->id)->where('type', '=', 'premium')->where('status', '=', 'paid')->count() == 1)
                                    {
     
                                       if($referred_user->premium_until  == 0) $referred_user->premium_until  = time();
                                        $add = (3*86400);
                                        $referred_user->premium_until = ($referred_user->premium_until+$add);
                                        $referred_user->save(); 
     
                                    }
                                    else
                                    {
     
                                        if($referred_user->premium_until  == 0) $referred_user->premium_until  = time();
                                        $add = (1*86400);
                                        $referred_user->premium_until = ($referred_user->premium_until+$add);
                                        $referred_user->save(); 
     
                                    }
     
                                }
     
                            }
     
                        break;
     
                        case 'tokens':
     
                            if(!isset($data['recorder_tokens'])) throw HttpBadRequestException;
     
                            $user->tokens = ($user->tokens+$data['recorder_tokens']);
                            $user->save(); 
     
                        break;
     
                    }
     
     
            \Messages::success('Bestellung wurde mit Gutschrift abgeschlossen!');
            \Messages::redirect('ghru4pxsa3/payments');

        }
		
		/**
         * 
         */
        public function post_exchange($payment_id)
        {
     
            $payment = Model_User_Payment::query()->where('id', '=', $payment_id)->get_one();
            if(!$payment) throw new HttpNotFoundException;
     
            if($payment->method != 'psc'&& $payment->method != 'amazoncode')
            {
     
                \Messages::error('Nur Paysafecard / Amazon Zahlungen können als im Exchange markiert werden.');
                \Messages::redirect('ghru4pxsa3/payments');
     
            }
     
            $payment->status = 'in_exchange';
            $payment->save();
     
            \Messages::success('Zahlung wurde als im Exchange markiert.');
            \Messages::redirect('ghru4pxsa3/payments');
     
        }
     
        /**
         * 
         */
        public function post_decline($payment_id)
        {
     
 
     
            $payment = Model_User_Payment::query()->where('id', '=', $payment_id)->get_one();
            if(!$payment) throw new HttpNotFoundException;
     
            if($payment->method != 'psc'&& $payment->method != 'amazoncode')
            {
     
                \Messages::error('Nur Paysafecard Zahlungen können abgelehnt werden.');
                \Messages::redirect('ghru4pxsa3/payments');
     
            }
     
            $reason = Input::post('reason', '');
     
            $payment->status = 'declined';
            $payment->decline_reason = $reason;
            $payment->save();
     
            \Messages::success('Zahlung wurde als abgelehnt markiert.');
            \Messages::redirect('ghru4pxsa3/payments');
     
        }
     
        /**
         * 
         */
        public function post_delete($payment_id)
        {
     
   
	 
            $payment = Model_User_Payment::query()->where('id', '=', $payment_id)->get_one();
            if(!$payment) throw new HttpNotFoundException;
     
            $payment->delete();
     
            \Messages::success('Zahlung wurde gelöscht!');
            \Messages::redirect('ghru4pxsa3/payments');
     
        }
     
     
    }