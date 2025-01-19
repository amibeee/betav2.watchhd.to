<?php 
class Controller_Ajax_Checkout extends Controller_Base_Ajax
{
    public function action_index()
    {
        try {
            \Log::info('Checkout request received');
            \Log::info('GET params: ' . json_encode(Input::get()));
            \Log::info('POST params: ' . json_encode(Input::post()));
            \Log::info('REQUEST params: ' . json_encode($_REQUEST));
            $mstatus=Input::post('multiline', Input::get('multiline', '0'));
            // Get input data
            $data = array(
                'method' => Input::post('method', Input::get('method', '')),
                'product' => Input::post('product', Input::get('product', '')),
                'multiline' => Input::post('multiline', Input::get('multiline', '0')) // Default to '0' if empty
            );
            \Log::info('Multiline status: ' . ($data['multiline'] === '1' ? 'Enabled' : 'Disabled'));

            \Log::info('Processed input data: ' . json_encode($data));
            Session::set('smulti',$mstatus);
            // Validate payment method

            // Validate product


            // Check for recent payments
            $last_payment = Model_User_Payment::query()
                ->where('user_id', '=', $this->user['uid'])
                ->order_by('created_at', 'DESC')
                ->get_one();

            // if ($last_payment && $last_payment->created_at > (time() - 600)) {
            //     throw new Exception('Wir können leider nur noch 1 Zahlung pro 10 Minuten erlauben da diese Funktion von manchen Benutzern gegen uns verwendet wurde.');
            // }

            // Check for Amazon payments with VoD Premium
            if (in_array(31, Session::get('packages', array())) && $data['method'] == "amazoncode") {
                throw new Exception('Zahlungen via Amazon sind wenn Vod Premium aktiviert ist leider nicht möglich.');
            }

            // Generate checkout token
            $checkout_token = Str::random('sha1');
            \Log::info('Generated checkout token: ' . $checkout_token);

            // Store checkout data in session
            Session::set('checkout.' . $checkout_token, $data);
            \Log::info('Checkout data stored in session');

            // Return success response
            return $this->response(array(
                'success' => true,
                'message' => 'Checkout process initiated successfully',
                'data' => array('checkout_token' => $checkout_token),
            ));

        } catch (Exception $e) {
            \Log::error('Checkout error: ' . $e->getMessage());
            return $this->response(array(
                'success' => false,
                'message' => $e->getMessage()
            ), 400);
        }
    }

    // Updated method signature to match parent class
    public function response($data = array(), $http_status = 200)
    {
        $response = Response::forge(json_encode($data), $http_status, array(
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Expires' => '0',
            'Pragma' => 'no-cache'
        ));

        return $response;
    }
}
