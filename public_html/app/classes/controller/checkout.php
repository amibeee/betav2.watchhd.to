    <?php
    class Controller_Checkout extends Controller_Base_User
    {
     
        /**
         * 
         */
        public function action_index($checkout_token)
        {
     
            if(Session::get('checkout.'.$checkout_token, false) == false) throw new HttpNotFoundException;
     
            $data = Session::get('checkout.'.$checkout_token);

			if($data['method'] == 'psc' && !$this->paysafecard_allowed){
				
				\Messages::error("Zahlungen via Paysafecard sind aktuell nicht möglich.");
                \Messages::redirect('account');
				
			}
			
			if($data['method'] == 'amazoncode' && !$this->amazon_allowed){
				
				\Messages::error("Zahlungen via Amazon Geschenkekarte sind aktuell nicht möglich.");
                \Messages::redirect('account');
				
			}

            if($data['product'] > 100){
                $virtual_products = Session::get('virtual_products', array());
               
                $product = isset($virtual_products['packets'][$data['product']]) ? (object)$virtual_products['packets'][$data['product']] : null;
            }
            else{
                $product = Model_Product::query()->where('id', '=', $data['product'])->get_one();    
            }
      
            if(!$product) throw new HttpNotFoundException;
            

            // validate packages
            $packages = array(1,2,3,4,5,8,9,10,11,12,24,27,28); // musst anpassen
			
            $valid_packages = array();
			#$valid_packages[] = 1; // default pakete (pro zeile 1)
			#$valid_packages[] = 18; // default pakete (pro zeile 1)
			#$valid_packages[] = 21; // default pakete (pro zeile 1)
			#$valid_packages[] = 16; // default pakete (pro zeile 1)
			#$valid_packages[] = 17; // default pakete (pro zeile 1)
			#$valid_packages[] = 26; // default pakete (pro zeile 1)
			#$valid_packages[] = 23; // default pakete (pro zeile 1)
			#$valid_packages[] = 24; // default pakete (pro zeile 1)
			
            foreach(Session::get('packages', array()) as $package){
				
                if(in_array($package, $packages)){
                    $valid_packages[] = $package;
                }
				
            }

            
            $valid_packages = array_unique($valid_packages);
			
			/*
            if(in_array(31, $valid_packages)){
                
                if (($key = array_search(45, $valid_packages)) !== false) {
                    unset($valid_packages[$key]);
                }

            }
			*/
            
            $is_virtual_product = false;
            if(isset($product->virtual) && $product->virtual){
                $is_virtual_product = true;
            }

			$append = 0.00;
			if(in_array(31, Session::get('packages', array())) && $is_virtual_product == false){
				#$append = 3.00;
			}
			
			
			$packet_data = json_decode($product->data, true);
			$months = round(($packet_data['premium_days']/30));
            $product->price = ($product->price+($months*$append));
      

			//$product->price = ($product->price+$append);

            $data['data']['product'] = $product; 
            $data['buy'] = Session::get('buy');
			
			/*
            if($data['method'] == 'psc')
            {
     
                if(Input::method() == 'POST')
                {
     
                    $packets = array();
     
                    $errors = array();
     
                    foreach(Input::post('codes', array()) as $id => $code)
                    {
     
                        if(empty($code)) continue;
     
                        $amount = Input::post('amount.'.$id, '');
     
                        if(!ctype_digit($amount) || $amount == 0)
                        {
                            $errors[] = i18n::t('Du hast den Wert für den %s Paysafecard Coupon nicht angegeben.', array($id));
                        }
     
                        if(ctype_digit($amount) && $amount > 0)
                        {
                            $packets[] = array($code, $amount);
                        }
     
                    }
     
                    if(count($packets) == 0)
                    {
                        $errors[] = i18n::t('Du musst min. einen Paysafecard Coupon angeben.');
                    }
     
                    if(count($errors) == 0)
                    {
     
                        $product->data = json_decode($product->data, true);
                        $product->data['buy'] = Session::get('buy');
                        $product->data['packages'] = $valid_packages;
     
                        $payment = new Model_User_Payment;
                        $payment->amount = $product->price;   
                        do{
                            $payment->token = Str::random('sha1');  
                        }
                        while(Model_User_Payment::query()->where('token', '=', $payment->token)->count());
                        $payment->product = $product->name;
                        $payment->user_id = $this->user['uid'];
                        $payment->status = 'pending';
                        $payment->type = $product->type;
                        $payment->data = json_encode($product->data);
                        $payment->method = 'psc';
                        $payment->worth = $product->price;
    		            $payment->decline_reason = ''; 
                        foreach($packets as $packet)
                        {
                            $payment->notice = $payment->notice."Code: ".$packet[0]." Wert:".$packet[1]."\n";    
                        }
                        $payment->save();

                        Session::set('packages', array());
     
                        \Messages::success(i18n::t('Wir haben deine Bestellung erhalten und werden uns zeitnahe um diese kümmern.'));
                        \Messages::redirect('/account');
     
                    }
                    else
                    {
     
                        Session::set('input.old', Input::post());
                        \Messages::error(implode('<br />', $errors));
                        \Messages::redirect(Uri::current());
     
                    }
     
                }
     
                return Response::forge(View::forge('checkout/psc.html.twig', isset($data) ? $data : array(), false));
     
            }
			*/
			if($data['method'] == 'psc')
            {
     
                if(Input::method() == 'POST')
                {
     
                    $packets = array();
     
                    $errors = array();
     
                    foreach(Input::post('codes', array()) as $id => $code)
                    {
     
                        if(empty($code)) continue;
     
                        $amount = Input::post('amount.'.$id, '');
						$image = Input::post('images.'.$id, '');
		
                        if(!ctype_digit($amount) || $amount == 0)
                        {
                            $errors[] = i18n::t('Du hast den Wert für den %s Amazon Guthaben Code nicht angegeben.', array($id));
                        }
						
						if(empty($image) || !filter_var($image, FILTER_VALIDATE_URL))
						{
							$errors[] = i18n::t('Du hast keinen Link für den %s Paysafecard Scan (Foto) angegeben.', array($id));
						}
						
						/*
						if(strtoupper(substr($code, 0, 1)) != '0'){
							$errors[] = i18n::t('Paysafecard Code ungülig.', array($id));
						}
						*/
						
						if(
							(substr($image, 0, 20) != 'https://epvpimg.com/'
							&&
							substr($image, 0, 19) != 'http://epvpimg.com/'
							&&
							substr($image, 0, 22) != 'https://i.epvpimg.com/'
							&&
							substr($image, 0, 21) != 'http://i.epvpimg.com/')
							&&
							(substr($image, 0, 17) != 'http://www.xup.in'
							&&
							substr($image, 0, 18) != 'https://www.xup.in'
							&&
							substr($image, 0, 19) != 'https://www1.xup.in')
							
						)
						{
							$errors[] = 'Ungültiger Bildlink. Es sind nur Links zu https://epvpimg.com/ bzw https://i.epvpimg.com/ oder xup.in / xup.to erlaubt';
						}
						
						if(ctype_digit($amount) && $amount > 0)
                        {
                            $packets[] = array($code, $amount, $image);
                        }
     
                    }
     
                    if(count($packets) == 0)
                    {
                        $errors[] = i18n::t('Du musst min. eine valide Paysafecard angeben.');
                    }
     
                    if(count($errors) == 0)
                    {
     
                        $product->data = json_decode($product->data, true);
                        $product->data['buy'] = Session::get('buy');
                        $product->data['packages'] = $valid_packages;
     
                        $payment = new Model_User_Payment;
                        $payment->amount = $product->price;   
                        do{
                            $payment->token = Str::random('sha1');  
                        }
                        while(Model_User_Payment::query()->where('token', '=', $payment->token)->count());
                        $payment->product = $product->name;
                        $payment->user_id = $this->user['uid'];
                        $payment->status = 'pending';
                        $payment->type = $product->type;
                        $payment->data = json_encode($product->data);
                        $payment->method = 'psc';
                        $payment->worth = $product->price;
    		            $payment->decline_reason = ''; 
                        foreach($packets as $packet)
                        {
                            $payment->notice = $payment->notice."Code: ".$packet[0]." Link: ".$packet[2]." Wert:".$packet[1]."\n";    
                        }
                        $payment->save();

                        Session::set('packages', array());
     
                        \Messages::success(i18n::t('Wir haben deine Bestellung erhalten und werden uns zeitnahe um diese kümmern.'));
                        \Messages::redirect('/account');
     
                    }
                    else
                    {
     
                        Session::set('input.old', Input::post());
                        \Messages::error(implode('<br />', $errors));
                        \Messages::redirect(Uri::current());
     
                    }
     
                }
     
                return Response::forge(View::forge('checkout/psc.html.twig', isset($data) ? $data : array(), false));
     
            }
			
			if($data['method'] == 'amazoncode')
            {
     
                if(Input::method() == 'POST')
                {
     
                    $packets = array();
     
                    $errors = array();
     
                    foreach(Input::post('codes', array()) as $id => $code)
                    {
     
                        if(empty($code)) continue;
     
                        $amount = Input::post('amount.'.$id, '');
						$image = Input::post('images.'.$id, '');
		
                        if(!ctype_digit($amount) || $amount == 0)
                        {
                            $errors[] = i18n::t('Du hast den Wert für den %s Amazon Guthaben Code nicht angegeben.', array($id));
                        }
						
						if(empty($image) || !filter_var($image, FILTER_VALIDATE_URL))
						{
							$errors[] = i18n::t('Du hast keinen Link für den %s Amazon Guthaben Code angegeben.', array($id));
						}
						
				
						
						if(
							(substr($image, 0, 20) != 'https://epvpimg.com/'
							&&
							substr($image, 0, 19) != 'http://epvpimg.com/'
							&&
							substr($image, 0, 22) != 'https://i.epvpimg.com/'
							&&
							substr($image, 0, 21) != 'http://i.epvpimg.com/')
							&&
							(substr($image, 0, 17) != 'http://www.xup.in'
							&&
							substr($image, 0, 18) != 'https://www.xup.in'
							&&
							substr($image, 0, 19) != 'https://www1.xup.in')
						)
						{
							$errors[] = 'Ungültiger Bildlink. Es sind nur Links zu https://epvpimg.com/ bzw https://i.epvpimg.com/ erlaubt';
						}
						
						if(ctype_digit($amount) && $amount > 0)
                        {
                            $packets[] = array($code, $amount, $image);
                        }
     
                    }
     
                    if(count($packets) == 0)
                    {
                        $errors[] = i18n::t('Du musst min. einen Amazon Geschenke Gutschein Code angeben.');
                    }
     
                    if(count($errors) == 0)
                    {
     
                        $product->data = json_decode($product->data, true);
                        $product->data['buy'] = Session::get('buy');
                        $product->data['packages'] = $valid_packages;
     
                        $payment = new Model_User_Payment;
                        $payment->amount = $product->price;   
                        do{
                            $payment->token = Str::random('sha1');  
                        }
                        while(Model_User_Payment::query()->where('token', '=', $payment->token)->count());
                        $payment->product = $product->name;
                        $payment->user_id = $this->user['uid'];
                        $payment->status = 'pending';
                        $payment->type = $product->type;
                        $payment->data = json_encode($product->data);
                        $payment->method = 'amazoncode';
                        $payment->worth = $product->price;
    		            $payment->decline_reason = ''; 
                        foreach($packets as $packet)
                        {
                            $payment->notice = $payment->notice."Code: ".$packet[0]." Link: ".$packet[2]." Wert:".$packet[1]."\n";    
                        }
                        $payment->save();

                        Session::set('packages', array());
     
                        \Messages::success(i18n::t('Wir haben deine Bestellung erhalten und werden uns zeitnahe um diese kümmern.'));
                        \Messages::redirect('/account');
     
                    }
                    else
                    {
     
                        Session::set('input.old', Input::post());
                        \Messages::error(implode('<br />', $errors));
                        \Messages::redirect(Uri::current());
     
                    }
     
                }
     
                return Response::forge(View::forge('checkout/amazon.html.twig', isset($data) ? $data : array(), false));
     
            }
     
            if($data['method'] == 'bitcoin')
            {
     
                if(Input::method() == 'POST')
                {
     
                    $voucher = trim(Input::post('voucher', ''));
     
                    $coupon = Model_Coupon::query()->where('code', '=', $voucher)->get_one();
     
                    if($product->type == 'premium')
                    {
     
                        if(!empty($voucher) && !$coupon)
                        {
     
                            Session::set_flash('input.old', Input::post());
     
                            \Messages::error(i18n::t('Ungültiger Gutschein Code.'));
                            \Messages::redirect(Uri::current());
     
                        }
     
                    }
     
                    $product->data = json_decode($product->data, true);
                    
                    $product->data['buy'] = Session::get('buy');
                    $product->data['packages'] = $valid_packages;
                   // $product->data['multiline'] = Session::get('smulti');
                    $product->data['buy']['multiline'] = Session::get('smulti');
                    $payment = new Model_User_Payment;
                    if($coupon && $product->type == 'premium')
                    {
     
                        $discount = (($product->price/100)*$coupon->discount);
                        $payment->amount = ($product->price-$discount); 
     
                    }
                    else
                    {
                        $payment->amount = $product->price;   
                    }
                    do{
                        $payment->token = Str::random('sha1');  
                    }
                    while(Model_User_Payment::query()->where('token', '=', $payment->token)->count());
                    $payment->product = $product->name;
                    $payment->user_id = $this->user['uid'];
                    $payment->status = 'pending';
                    $payment->type = $product->type;
                    $payment->data = json_encode($product->data);
                    $payment->method = 'bitcoin';
                    $payment->notice = '';
                    $x= Session::get('smulti');
                    if ($x){
                        $payment->worth = $product->price*1.7;
                        $payment->amount= $product->price*1.7;
                    }else{
                        $payment->worth = $product->price;
                        $payment->amount= $product->price;

                    }
    		        
    		        $payment->decline_reason = '';
                    $payment->save();
     
                    if($coupon) $coupon->delete();

                    Session::set('packages', array());
     
                    Response::redirect('pay/'.$payment->id);
     
                }    
     
                return Response::forge(View::forge('checkout/bitcoin.html.twig', isset($data) ? $data : array(), false));
     
     
            }
     
			if($data['method'] == 'monero')
            {
     
                if(Input::method() == 'POST')
                {
     
                    $voucher = trim(Input::post('voucher', ''));
     
                    $coupon = Model_Coupon::query()->where('code', '=', $voucher)->get_one();
     
                    if($product->type == 'premium')
                    {
     
                        if(!empty($voucher) && !$coupon)
                        {
     
                            Session::set_flash('input.old', Input::post());
     
                            \Messages::error(i18n::t('Ungültiger Gutschein Code.'));
                            \Messages::redirect(Uri::current());
     
                        }
     
                    }
     
                    $product->data = json_decode($product->data, true);
                    $product->data['buy'] = Session::get('buy');
                    $product->data['packages'] = $valid_packages;
     
                    $payment = new Model_User_Payment;
                    if($coupon && $product->type == 'premium')
                    {
     
                        $discount = (($product->price/100)*$coupon->discount);
                        $payment->amount = ($product->price-$discount); 
     
                    }
                    else
                    {
                        $payment->amount = $product->price;   
                    }
                    do{
                        $payment->token = Str::random('sha1');  
                    }
                    while(Model_User_Payment::query()->where('token', '=', $payment->token)->count());
                    $payment->product = $product->name;
                    $payment->user_id = $this->user['uid'];
                    $payment->status = 'pending';
                    $payment->type = $product->type;
                    $payment->data = json_encode($product->data);
                    $payment->method = 'monero';
                    $payment->notice = '';
                    $payment->worth = $product->price;
                    $payment->decline_reason = '';
                    $payment->save();
     
                    if($coupon) $coupon->delete();

                    Session::set('packages', array());
     
                    Response::redirect('pay/'.$payment->id);
     
                }    
     
                return Response::forge(View::forge('checkout/monero.html.twig', isset($data) ? $data : array(), false));
     
     
            }
			
			if($data['method'] == 'tokenpay')
            {
     
                if(Input::method() == 'POST')
                {
     
                    $voucher = trim(Input::post('voucher', ''));
     
                    $coupon = Model_Coupon::query()->where('code', '=', $voucher)->get_one();
     
                    if($product->type == 'premium')
                    {
     
                        if(!empty($voucher) && !$coupon)
                        {
     
                            Session::set_flash('input.old', Input::post());
     
                            \Messages::error(i18n::t('Ungültiger Gutschein Code.'));
                            \Messages::redirect(Uri::current());
     
                        }
     
                    }
     
                    $product->data = json_decode($product->data, true);
                    $product->data['buy'] = Session::get('buy');
                    $product->data['packages'] = $valid_packages;
     
                    $payment = new Model_User_Payment;
                    if($coupon && $product->type == 'premium')
                    {
     
                        $discount = (($product->price/100)*$coupon->discount);
                        $payment->amount = ($product->price-$discount); 
     
                    }
                    else
                    {
                        $payment->amount = $product->price;   
                    }
                    do{
                        $payment->token = Str::random('sha1');  
                    }
                    while(Model_User_Payment::query()->where('token', '=', $payment->token)->count());
                    $payment->product = $product->name;
                    $payment->user_id = $this->user['uid'];
                    $payment->status = 'pending';
                    $payment->type = $product->type;
                    $payment->data = json_encode($product->data);
                    $payment->method = 'tokenpay';
                    $payment->notice = '';
                    $payment->worth = $product->price;
                    $payment->decline_reason = '';
                    $payment->save();
     
                    if($coupon) $coupon->delete();

                    Session::set('packages', array());
     
                    Response::redirect('pay/'.$payment->id);
     
                }    
     
                return Response::forge(View::forge('checkout/tokenpay.html.twig', isset($data) ? $data : array(), false));
     
     
            }
     
     
           
		
		if($data['method'] == 'ethereum')
            {
     
                if(Input::method() == 'POST')
                {
     
                    $voucher = trim(Input::post('voucher', ''));
     
                    $coupon = Model_Coupon::query()->where('code', '=', $voucher)->get_one();
     
                    if($product->type == 'premium')
                    {
     
                        if(!empty($voucher) && !$coupon)
                        {
     
                            Session::set_flash('input.old', Input::post());
     
                            \Messages::error(i18n::t('Ungültiger Gutschein Code.'));
                            \Messages::redirect(Uri::current());
     
                        }
     
                    }
     
                    $product->data = json_decode($product->data, true);
                    $product->data['buy'] = Session::get('buy');
                    $product->data['packages'] = $valid_packages;
     
                    $payment = new Model_User_Payment;
                    if($coupon && $product->type == 'premium')
                    {
     
                        $discount = (($product->price/100)*$coupon->discount);
                        $payment->amount = ($product->price-$discount); 
     
                    }
                    else
                    {
                        $payment->amount = $product->price;   
                    }
                    do{
                        $payment->token = Str::random('sha1');  
                    }
                    while(Model_User_Payment::query()->where('token', '=', $payment->token)->count());
                    $payment->product = $product->name;
                    $payment->user_id = $this->user['uid'];
                    $payment->status = 'pending';
                    $payment->type = $product->type;
                    $payment->data = json_encode($product->data);
                    $payment->method = 'ethereum';
                    $payment->notice = '';
                    $payment->worth = $product->price;
                    $payment->decline_reason = '';
                    $payment->save();
     
                    if($coupon) $coupon->delete();

                    Session::set('packages', array());
     
                    Response::redirect('pay/'.$payment->id);
     
                }    
     
                return Response::forge(View::forge('checkout/ethereum.html.twig', isset($data) ? $data : array(), false));
     
     
            }

            if($data['method'] == 'bitcoincash')
            {
     
                if(Input::method() == 'POST')
                {
     
                    $voucher = trim(Input::post('voucher', ''));
     
                    $coupon = Model_Coupon::query()->where('code', '=', $voucher)->get_one();
     
                    if($product->type == 'premium')
                    {
     
                        if(!empty($voucher) && !$coupon)
                        {
     
                            Session::set_flash('input.old', Input::post());
     
                            \Messages::error(i18n::t('Ungültiger Gutschein Code.'));
                            \Messages::redirect(Uri::current());
     
                        }
     
                    }
     
                    $product->data = json_decode($product->data, true);
                    $product->data['buy'] = Session::get('buy');
                    $product->data['packages'] = $valid_packages;
     
                    $payment = new Model_User_Payment;
                    if($coupon && $product->type == 'premium')
                    {
     
                        $discount = (($product->price/100)*$coupon->discount);
                        $payment->amount = ($product->price-$discount); 
     
                    }
                    else
                    {
                        $payment->amount = $product->price;   
                    }
                    do{
                        $payment->token = Str::random('sha1');  
                    }
                    while(Model_User_Payment::query()->where('token', '=', $payment->token)->count());
                    $payment->product = $product->name;
                    $payment->user_id = $this->user['uid'];
                    $payment->status = 'pending';
                    $payment->type = $product->type;
                    $payment->data = json_encode($product->data);
                    $payment->method = 'bitcoincash';
                    $payment->notice = '';
                    $payment->worth = $product->price;
                    $payment->decline_reason = '';
                    $payment->save();
     
                    if($coupon) $coupon->delete();

                    Session::set('packages', array());
     
                    Response::redirect('pay/'.$payment->id);
     
                }    
     
                return Response::forge(View::forge('checkout/bitcoincash.html.twig', isset($data) ? $data : array(), false));
     
     
            }

            if($data['method'] == 'litecoin')
            {
     
                if(Input::method() == 'POST')
                {
     
                    $voucher = trim(Input::post('voucher', ''));
     
                    $coupon = Model_Coupon::query()->where('code', '=', $voucher)->get_one();
     
                    if($product->type == 'premium')
                    {
     
                        if(!empty($voucher) && !$coupon)
                        {
     
                            Session::set_flash('input.old', Input::post());
     
                            \Messages::error(i18n::t('Ungültiger Gutschein Code.'));
                            \Messages::redirect(Uri::current());
     
                        }
     
                    }
     
                    $product->data = json_decode($product->data, true);
                    $product->data['buy'] = Session::get('buy');
                    $product->data['packages'] = $valid_packages;
     
                    $payment = new Model_User_Payment;
                    if($coupon && $product->type == 'premium')
                    {
     
                        $discount = (($product->price/100)*$coupon->discount);
                        $payment->amount = ($product->price-$discount); 
     
                    }
                    else
                    {
                        $payment->amount = $product->price;   
                    }
                    do{
                        $payment->token = Str::random('sha1');  
                    }
                    while(Model_User_Payment::query()->where('token', '=', $payment->token)->count());
                    $payment->product = $product->name;
                    $payment->user_id = $this->user['uid'];
                    $payment->status = 'pending';
                    $payment->type = $product->type;
                    $payment->data = json_encode($product->data);
                    $payment->method = 'litecoin';
                    $payment->notice = '';
                    $payment->worth = $product->price;
                    $payment->decline_reason = '';
                    $payment->save();
     
                    if($coupon) $coupon->delete();

                    Session::set('packages', array());
     
                    Response::redirect('pay/'.$payment->id);
     
                }    
     
                return Response::forge(View::forge('checkout/litecoin.html.twig', isset($data) ? $data : array(), false));
     
     
            }

            if($data['method'] == 'verge')
            {
     
                if(Input::method() == 'POST')
                {
     
                    $voucher = trim(Input::post('voucher', ''));
     
                    $coupon = Model_Coupon::query()->where('code', '=', $voucher)->get_one();
     
                    if($product->type == 'premium')
                    {
     
                        if(!empty($voucher) && !$coupon)
                        {
     
                            Session::set_flash('input.old', Input::post());
     
                            \Messages::error(i18n::t('Ungültiger Gutschein Code.'));
                            \Messages::redirect(Uri::current());
     
                        }
     
                    }
     
                    $product->data = json_decode($product->data, true);
                    $product->data['buy'] = Session::get('buy');
                    $product->data['packages'] = $valid_packages;
     
                    $payment = new Model_User_Payment;
                    if($coupon && $product->type == 'premium')
                    {
     
                        $discount = (($product->price/100)*$coupon->discount);
                        $payment->amount = ($product->price-$discount); 
     
                    }
                    else
                    {
                        $payment->amount = $product->price;   
                    }
                    do{
                        $payment->token = Str::random('sha1');  
                    }
                    while(Model_User_Payment::query()->where('token', '=', $payment->token)->count());
                    $payment->product = $product->name;
                    $payment->user_id = $this->user['uid'];
                    $payment->status = 'pending';
                    $payment->type = $product->type;
                    $payment->data = json_encode($product->data);
                    $payment->method = 'verge';
                    $payment->notice = '';
                    $payment->worth = $product->price;
                    $payment->decline_reason = '';
                    $payment->save();
     
                    if($coupon) $coupon->delete();

                    Session::set('packages', array());
     
                    Response::redirect('pay/'.$payment->id);
     
                }    

    
     
                return Response::forge(View::forge('checkout/verge.html.twig', isset($data) ? $data : array(), false));
     
     
            }
     
     
         
		}
     
    }