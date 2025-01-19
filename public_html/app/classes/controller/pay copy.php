<?php
class Controller_Pay extends Controller_Base_User
{
	public function action_index($pid)
	{
		$user_payment = Model_User_Payment::query()->where('id', '=', $pid)->where('user_id', '=', $this->user['uid'])->get_one();
		if (!$user_payment) throw new HttpNotFoundException;
		
		$code = $this->get_crypto_code($user_payment->method);
		
		$anonpay_url = $this->generate_anonpay_url($user_payment, $code);
		
		$data = [
			'payment' => $user_payment,
			'anonpay_url' => $anonpay_url,
		];
		
		return $this->render_payment_view($code, $data);
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

	
    private function generate_anonpay_url($payment, $code)
    {
        $base_url = "https://trocador.app/anonpay/";
        $params = [
            'ticker_to' => 'xmr',
            'network_to' => 'Mainnet',
            'address' => '4681QYGKRAmDznzgJxN4D2P1PSa2ekDTjgLz1k6X4v82CJcsf94t4nWVsXu2ddzhPBF541MbCMJzbALfvQjKGzW464apf1Z',
            'fiat_equiv' => 'EUR',
            'amount' => number_format($payment->amount, 2, '.', ''),
            'name' => 'WatchHD',
            'description' => '#' . $payment->id,
            'direct' => 'false'  // Changed to 'false' to get JSON response
        ];

        if ($payment->data && isset($payment->data['multiline']) && $payment->data['multiline']) {
            $params['description'] .= ' Multiline';
        }

        $url = $base_url . '?' . http_build_query($params);
        
        // Fetch the JSON response
        $response = file_get_contents($url);
        $json_data = json_decode($response, true);
        
        // Return both the full JSON data and the specific URL
        return [
            'json_data' => $json_data,
            'iframe_url' => $json_data['url'] ?? ''
        ];
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

        // Pass the full JSON data to the view
        $data['anonpay_data'] = $data['anonpay_url']['json_data'];
        $data['anonpay_iframe_url'] = $data['anonpay_url']['iframe_url'];

        return Response::forge(View::forge($view, $data, false));
    }
    public function action_callback($token)
    {
        $payment = Model_User_Payment::query()->where('token', '=', $token)->get_one();
        if (!$payment) {
            return new Response('Payment not found', 404);
        }

        $input = Input::json();
        
        if ($input['status'] == 'finished' && $payment->status != 'paid') {
            $payment->status = 'paid';
            try {
                $this->process_successful_payment($payment);
            } catch (Exception $e) {
                \Log::error('Failed to process callback payment: ' . $e->getMessage());
                return new Response('Internal server error', 500);
            }
        } elseif (in_array($input['status'], ['failed', 'expired', 'refunded'])) {
            $payment->status = 'failed';
        }

        $payment->save();

        return new Response('OK', 200);
    }

    private function process_successful_payment($payment)
    {
        try {
            DB::start_transaction();

            $user = Model_User::find($payment->user_id);
            $user->credits += $payment->amount;
            $user->save();

            $transaction = new Model_Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $payment->amount;
            $transaction->type = 'credit';
            $transaction->description = 'Payment #' . $payment->id;
            $transaction->save();

            if ($payment->package_id) {
                $package = Model_User_Package::find($payment->package_id);
                if ($package) {
                    $package->status = 'active';
                    $package->activated_at = time();
                    $package->expires_at = time() + (30 * 24 * 60 * 60); // 30 days package
                    $package->save();
                }
            }

            DB::commit_transaction();

            \Log::info('Successfully processed payment #' . $payment->id . ' for user #' . $user->id);

        } catch (Exception $e) {
            DB::rollback_transaction();
            \Log::error('Failed to process payment #' . $payment->id . ': ' . $e->getMessage());
            throw $e;
        }
    }
}