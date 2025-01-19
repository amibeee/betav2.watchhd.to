<?php
class Controller_Buy extends Controller_Base_User
{
    public function action_index($product = '')
    {   
        if (Input::method() == 'POST') {
            $multilineStatus = Input::post('multiline'); // Default to '0' if not set
            Session::set('multiline', $multilineStatus);
            Session::set('packages', array_keys(Input::post('paket_options_enabled', array())));
            
            // Add this line to handle the 'multiple' value
            Session::set('multiple', Input::post('multiple', false));
        } else {
            // For GET requests, try to get multiline status from query parameter
            $multilineStatus = Input::get('multiline', Session::get('multiline', '0'));
            Session::set('multiline', $multilineStatus);
            
            // Add this line to handle the 'multiple' value for GET requests
            $multipleStatus = Input::get('multiple', Session::get('multiple', false));
            Session::set('multiple', $multipleStatus);
        }
        $line = Input::post('line', '');
        $type = Input::post('type', '');
        
        if (!in_array($type, array('mainline', 'subline'))) {
            $type = 'mainline';
        }
        $line = Input::post('line', '');

        $last_payment = Model_User_Payment::query()
            ->where('data', 'LIKE', '%"username":"' . $line . '"%')
            ->where('status', 'paid')
            ->order_by('id', 'desc')
            ->get_one();
        
        $last_payment_data = isset($last_payment) ? json_decode($last_payment->data, true) : [];
        $mSTAT = isset($last_payment_data['buy']['multiline']) ? $last_payment_data['buy']['multiline'] : '0';
        $packets[1]['name'] = 'IPTV-DACH';
        $packets[1]['description'] = '';
        $packets[1]['price'] = 9.00;
        $packets[1]['turnable'] = true;
        $packets[1]['channels'] = 'Sky DE HD, HD+, DAZN, Teleclub HD, MySports HD uvm.';
        
        $packets[2]['name'] = 'IPTV-DE HEVC';
        $packets[2]['description'] = '';
        $packets[2]['price'] = 0.00;
        $packets[2]['turnable'] = true;
        $packets[2]['channels'] = 'Sky DE & HD+ HEVC';
        
        $packets[3]['name'] = 'IPTV-UK';
        $packets[3]['description'] = '';
        $packets[3]['price'] = 0.00;
        $packets[3]['turnable'] = true;
        $packets[3]['channels'] = 'Sky UK HD, EIR Sport HD uvm.';
        
        $packets[4]['name'] = 'IPTV-Frankreich';
        $packets[4]['description'] = '';
        $packets[4]['price'] = 0.00;
        $packets[4]['turnable'] = true;
        $packets[4]['channels'] = 'Sender aus Frankreich in HD u.a. Bein Sports France, RMC Sports uvm.';
        
        $packets[5]['name'] = 'IPTV-Polen';
        $packets[5]['description'] = '';
        $packets[5]['price'] = 0.00;
        $packets[5]['turnable'] = true;
        $packets[5]['channels'] = 'Sender aus Polen in HD Qualität';
        
        $packets[8]['name'] = 'IPTV-Türkei';
        $packets[8]['description'] = '';
        $packets[8]['price'] = 0.00;
        $packets[8]['turnable'] = true;
        $packets[8]['channels'] = 'Sender aus der Türkei in HD Qualität';
        
        $packets[9]['name'] = 'IPTV-Rest aus Europa';
        $packets[9]['description'] = '';
        $packets[9]['price'] = 0.00;
        $packets[9]['turnable'] = true;
        $packets[9]['channels'] = 'Sender aus Italien, Niederlande, Spanien uvm';
        
        $packets[10]['name'] = 'IPTV-USA/Canada';
        $packets[10]['description'] = '';
        $packets[10]['price'] = 0.00;
        $packets[10]['turnable'] = true;
        $packets[10]['channels'] = 'AMC, CBS, NBC, NBA, NHL, NFL, MLB, Bein Sports Connect, TSN uvm.';
        
        $packets[11]['name'] = 'IPTV-Rest der Welt';
        $packets[11]['description'] = '';
        $packets[11]['price'] = 0.00;
        $packets[11]['turnable'] = true;
        $packets[11]['channels'] = 'z.B. Supersport in HD aus Afrika (alle Premier League Spiele), Optus Sports Australia uvm.';
        
        $packets[12]['name'] = 'IPTV-World Sport';
        $packets[12]['description'] = '';
        $packets[12]['price'] = 0.00;
        $packets[12]['turnable'] = true;
        $packets[12]['channels'] = 'Sportsender aus der ganzen Welt in einem Paket zusammengefasst!';
        
        $packets[24]['name'] = 'IPTV-Music';
        $packets[24]['description'] = '';
        $packets[24]['price'] = 0.00;
        $packets[24]['turnable'] = true;
        $packets[24]['channels'] = 'Music Channels (Music Choice, MTV, NRJ Hits uvm.)';
        
        $packets[27]['name'] = 'IPTV-XXX';
        $packets[27]['description'] = '';
        $packets[27]['price'] = 0.00;
        $packets[27]['turnable'] = true;
        $packets[27]['channels'] = 'Adult Channels (Redlight HD, Hustler HD uvm.)';
        
        $packets[28]['name'] = 'IPTV-VoD';
        $packets[28]['description'] = '';
        $packets[28]['price'] = 0.00;
        $packets[28]['turnable'] = true;
        $packets[28]['channels'] = 'Ausgewählte Kinofilme und Serien in HD & 4K';

        $data = [
            'packets' => $packets,
            'last_payment' => $last_payment,
            'type' => $type,
            'line' => $line,

        ];

        switch ($product) {
            case 'premium':
                $user = null;

                if ($type == 'mainline') {
                    $user = Model_User::query()->where('username', $line)->where('id', $this->user['uid'])->get_one();
                } elseif ($type == 'subline') {
                    $user = Model_User_Line::query()->where('username', $line)->where('user_id', $this->user['uid'])->get_one();
                }

                if (!$user) {
                    $user = Model_User::find($this->user['uid']);
                    if (!$user) {
                        throw new HttpNotFoundException;
                    }
                }
                Session::set('buy', [
                    'type' => $type,
                    'line_id' => $user->id,
                    'username' => $user->username,
                ]);

                if ($type == 'subline') {
                    $basePrice = $data['packets'][1]['price'];
                    $price = $multilineStatus == '1' ? $basePrice * 1.7 : $basePrice;
                    Session::set('payment_amount', $price);
                }

                $data['type'] = ($type == 'mainline' ? 'mainline' : 'subline');
                $data['line'] = $user;
                $data['statmultiline'] = $mSTAT;
                return Response::forge(View::forge('buy/step2.html.twig', $data, false));

            case 'tokens':
                return Response::forge(View::forge('buy/step2.html.twig', $data, false));

            default:
                return Response::forge(View::forge('buy/step1.html.twig', $data, false)); 
        }
    }

}