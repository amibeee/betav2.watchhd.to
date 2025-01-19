<?php

class Controller_Pay extends Controller_Base_User
{
    private $api_key = 'ik3r4BL9WcXwVB6qa61mNaf3Tc5YUY';
    private $headers;
    private $status_mappings = [
        'new' => 'pending',
        'waiting' => 'pending',
        'confirming' => 'confirming',
        'sending' => 'processing',
        'paid partially' => 'partial',
        'finished' => 'finished',
        'failed' => 'failed',
        'expired' => 'expired',
        'halted' => 'failed',
        'refunded' => 'refunded'
    ];
    
    private $default_packages = [
        [1, 'active', 'IPTV-DACH', true],
        [2, 'inactive', 'IPTV-DE HEVC', true],
        [3, 'active', 'IPTV-UK', true],
        [4, 'active', 'IPTV-Frankreich', true],
        [5, 'active', 'IPTV-Polen', true],
        [8, 'active', 'IPTV-Türkei', true],
        [9, 'active', 'IPTV-Rest von Europa', true],
        [10, 'active', 'IPTV-USA/Canada', true],
        [11, 'inactive', 'IPTV-Rest der Welt', true],
        [12, 'inactive', 'IPTV-World Sport', true],
        [24, 'active', 'IPTV-Music', true],
        [27, 'inactive', 'IPTV-XXX', true],
        [28, 'inactive', 'IPTV-VoD', true]
    ];

    public function __construct($request)
    {
        parent::__construct($request);
        $this->headers = [
            'http' => [
                'header' => 'API-Key: ' . $this->api_key
            ]
        ];
    }

    public function action_index($pid)
    {
        try {
            $user_payment = $this->get_payment($pid);
            if (!$user_payment) {
                throw new HttpNotFoundException;
            }

            $code = $this->get_crypto_code($user_payment->method);
            $anonpay_data = $this->generate_anonpay_url($user_payment, $code);

            $data = [
                'payment' => $user_payment,
                'anonpay_url' => $anonpay_data,
            ];
            
            if (isset($anonpay_data['json_data']['ID'])) {
                $this->update_payment_status($user_payment, $anonpay_data);
            }
            
            return $this->render_payment_view($code, $data);
        } catch (Exception $e) {
            \Log::error('Payment processing error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function get_payment($pid)
    {
        return Model_User_Payment::query()
            ->where('id', '=', $pid)
            ->where('user_id', '=', $this->user['uid'])
            ->get_one();
    }

    private function update_payment_status($user_payment, $anonpay_data)
    {
        try {
            $user_payment->idtrc = $anonpay_data['json_data']['ID'];
            
            // Get status from Trocador
            $stat = $anonpay_data['json_data']['status_url'];
            $context = stream_context_create($this->headers);
            $resp_stat = file_get_contents($stat, false, $context);
            $stat_data = json_decode($resp_stat, true);
            
            if (!$stat_data) {
                throw new Exception('Failed to decode status response');
            }

            // Map and update status
            $trocador_status = $stat_data['Status'];
            $mapped_status = $this->status_mappings[$trocador_status] ?? 'pending';
            
            $user_payment->status_trc = $trocador_status;
            $user_payment->status = $mapped_status;
            
            // Process completed payments
            if ($mapped_status === 'finished' && $user_payment->status !== 'paid') {
                $this->process_completed_payment($user_payment);
            }
            
            $user_payment->save();
            
            \Log::info('Payment status updated', [
                'payment_id' => $user_payment->id,
                'trocador_status' => $trocador_status,
                'mapped_status' => $mapped_status
            ]);
            
        } catch (Exception $e) {
            \Log::error('Status update error: ' . $e->getMessage(), [
                'payment_id' => $user_payment->id
            ]);
            throw $e;
        }
    }

    private function process_completed_payment($payment)
    {
        try {
            $data = json_decode($payment->data, true);
            if (!isset($data['premium_days'])) {
                throw new Exception('Invalid premium payment data');
            }

            // Begin transaction
            Database::begin();

            // Update user premium status
            $this->update_user_premium($payment->user_id, $data['premium_days']);
            
            // Update user packages
            $this->update_user_packages($payment->user_id, $data);
            
            // Process affiliate commission if applicable
            $this->process_affiliate_commission($payment);
            
            // Mark payment as paid
            $payment->status = 'paid';
            $payment->save();

            Database::commit();

        } catch (Exception $e) {
            Database::rollback();
            \Log::error('Payment completion error: ' . $e->getMessage(), [
                'payment_id' => $payment->id
            ]);
            throw $e;
        }
    }

    private function update_user_premium($user_id, $days)
    {
        $premium_duration = $days * 86400;
        $user = Model_User::find($user_id);
        
        if (!$user) {
            throw new Exception('User not found');
        }

        $current_time = time();
        $new_premium_until = max($current_time, (int)$user->premium_until);
        $user->premium_until = $new_premium_until + $premium_duration;
        $user->save();
    }

    private function update_user_packages($user_id, $payment_data)
    {
        $premium_duration = $payment_data['premium_days'] * 86400;
        $type = isset($payment_data['buy']['type']) && $payment_data['buy']['type'] == 'subline' ? 'subline' : 'mainline';
        $line_id = $payment_data['buy']['line_id'] ?? null;
        
        // Get booked packages
        $booked_packages = isset($payment_data['packages']) 
            ? array_values($payment_data['packages']) 
            : [1, 3, 4, 5, 8, 9, 10, 24];
        
        $booked_packages_expanded = array_unique(
            array_merge($booked_packages, [2, 3, 4, 5, 8, 9, 10, 11, 12, 24, 27, 28])
        );

        foreach ($this->default_packages as $package) {
            $this->update_single_package(
                $user_id,
                $package,
                $booked_packages,
                $booked_packages_expanded,
                $premium_duration,
                $type,
                $line_id
            );
        }
    }

    private function update_single_package($user_id, $package, $booked_packages, $booked_packages_expanded, 
                                        $premium_duration, $type, $line_id)
    {
        $existing_packet = Model_User_Packet::query()
            ->where('user_id', '=', $user_id)
            ->where('bouquet_id', '=', $package[0])
            ->get_one();

        $status = in_array($package[0], $booked_packages) || $package[3] 
            ? $package[1] 
            : 'inactive';

        $current_time = time();
        $duration = (in_array($package[0], $booked_packages_expanded) || $package[3]) 
            ? $premium_duration 
            : 0;

        if ($existing_packet) {
            $this->update_existing_package($existing_packet, $status, $duration);
        } else {
            $this->create_new_package($user_id, $package, $status, $duration, $type, $line_id);
        }
    }

    private function update_existing_package($packet, $status, $duration)
    {
        $current_time = time();
        $new_until = max($current_time, (int)$packet->booked_until);
        
        $packet->status = $status;
        $packet->booked_until = $duration > 0 ? $new_until + $duration : $packet->booked_until;
        $packet->save();
    }

    private function create_new_package($user_id, $package, $status, $duration, $type, $line_id)
    {
        $packet = new Model_User_Packet();
        $packet->user_id = $user_id;
        $packet->name = $package[2];
        $packet->status = $status;
        $packet->line_type = $type;
        $packet->line_id = $line_id;
        $packet->bouquet_id = $package[0];
        $packet->booked_until = $duration > 0 ? time() + $duration : time();
        $packet->save();
    }

    private function process_affiliate_commission($payment)
    {
        $user = Model_User::find($payment->user_id);
        
        if ($user && $user->affiliate_id) {
            $log = new Model_Affiliate_Log();
            $log->affiliate_id = $user->affiliate_id;
            $log->user_id = $user->id;
            $log->type = 'sale';
            $log->data = json_encode([
                'product' => $payment->product,
                'amount' => $payment->amount,
                'type' => $payment->type,
                'payment_id' => $payment->id,
                'method' => $payment->method
            ]);
            $log->worth = $payment->amount;
            $log->sub_id = '';
            $log->save();
        }
    }

    private function generate_anonpay_url($payment, $code)
    {
        $base_url = "https://trocador.app/anonpay/";
        
        $params = [
            'ticker_to' => 'xmr',
            'network_to' => 'Mainnet',
            'address' => '4681QYGKRAmDznzgJxN4D2P1PSa2ekDTjgLz1k6X4v82CJcsf94t4nWVsXu2ddzhPBF541MbCMJzbALfvQjKGzW464apf1Z',
            'fiat_equiv' => 'EUR',
            'name' => 'WatchHD',
            'description' => '#' . $payment->id,
            'direct' => 'false'
        ];
        
        // Calculate amount based on payment type
        $amount = $this->calculate_payment_amount($payment);
        $params['amount'] = $amount;
        
        if ($payment->data) {
            $data = json_decode($payment->data, true);
            if (isset($data['multiline']) && $data['multiline']) {
                $params['description'] .= ' Multiline';
            }
        }
        
        $url = $base_url . '?' . http_build_query($params);
        $context = stream_context_create($this->headers);
        
        $response = file_get_contents($url, false, $context);
        $json_data = json_decode($response, true);
        
        return [
            'json_data' => $json_data,
            'iframe_url' => $json_data["url"],
            'calculated_amount' => $amount
        ];
    }

    private function calculate_payment_amount($payment)
    {
        $data = json_decode($payment->data, true);
        if ($data && isset($data['multiline']) && $data['multiline']) {
            return number_format($payment->amount * 0.7, 2, '.', '');
        }
        return number_format($payment->amount, 2, '.', '');
    }

    private function get_crypto_code($method)
    {
        $codes = [
            'bitcoin' => 'BTC',
            'monero' => 'XMR',
            'ethereum' => 'ETH',
            'litecoin' => 'LTC',
            'bitcoincash' => 'BCH',
            'verge' => 'XVG',
            'tokenpay' => 'TPAY'
        ];

        return isset($codes[$method]) ? $codes[$method] : 'BTC';
    }

    private function render_payment_view($code, $data)
    {
        $views = [
            'BTC' => 'pay.html.twig',
            'XMR' => 'pay_xmr.html.twig',
            'ETH' => 'pay_eth.html.twig',
            'BCH' => 'pay_bch.html.twig',
            'LTC' => 'pay_ltc.html.twig',
            'XVG' => 'pay_xvg.html.twig',
            'TPAY' => 'pay_tpay.html.twig'
        ];

        $view = isset($views[$code]) ? $views[$code] : 'pay.html.twig';

        $data['anonpay_data'] = $data['anonpay_url']['json_data'];
        $data['anonpay_iframe_url'] = $data['anonpay_url']['iframe_url'];

        return Response::forge(View::forge($view, $data, false));
    }
}
