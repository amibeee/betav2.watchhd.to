<?php 
class Controller_Ajax_Packets extends Controller_Base_Ajax
{
    public function action_index()
    {
        \Log::info('Ajax Packets request received');
        \Log::info('GET params: ' . json_encode(Input::get()));

        // Define the local data table instead of querying the database
        $all_packets = array(
            array('id' => 1, 'name' => '1 Monat Premium', 'type' => 'premium', 'price' => 9.00, 'data' => json_encode(array('premium_days' => 30)), 'method' => 'bitcoin', 'worth' => 0.0),
            array('id' => 4, 'name' => '2 Monate Premium', 'type' => 'premium', 'price' => 18.00, 'data' => json_encode(array('premium_days' => 60)), 'method' => 'bitcoin', 'worth' => 0.0),
            array('id' => 5, 'name' => '3 Monate Premium', 'type' => 'premium', 'price' => 25.00, 'data' => json_encode(array('premium_days' => 90)), 'method' => 'bitcoin', 'worth' => 0.0),
            array('id' => 35, 'name' => '6 Monate Premium', 'type' => 'premium', 'price' => 50.00, 'data' => json_encode(array('premium_days' => 180)), 'method' => 'bitcoin', 'worth' => 0.0),
        );

        // Get parameters
        $inputType = Input::post('type', 'premium');
        $inputMethod = Input::post('method', 'bitcoin');

        \Log::info('Input Type: ' . $inputType);
        \Log::info('Input Method: ' . $inputMethod);

        // Special pricing for Paysafe and Amazon payments
        $special_pricing_methods = array('psc', 'amazoncode');
        
        if (in_array($inputMethod, $special_pricing_methods)) {
            $data['packets'] = array(
                array(
                    'id' => 7,
                    'name' => '1 Monat Premium',
                    'type' => 'premium',
                    'price' => 15.00,
                    'data' => json_encode(array('premium_days' => 30)),
                    'method' => $inputMethod,
                    'worth' => 0.00,
                    'virtual' => true
                ),
                array(
                    'id' => 8,
                    'name' => '2 Monate Premium',
                    'type' => 'premium',
                    'price' => 25.00,
                    'data' => json_encode(array('premium_days' => 60)),
                    'method' => $inputMethod,
                    'worth' => 0.00,
                    'virtual' => true
                ),
                array(
                    'id' => 9,
                    'name' => '4 Monate Premium',
                    'type' => 'premium',
                    'price' => 50.00,
                    'data' => json_encode(array('premium_days' => 120)),
                    'method' => $inputMethod,
                    'worth' => 0.00,
                    'virtual' => true
                )
            );
        } else {
            // Filter packets based on input type and method for non-special methods
            $filtered_packets = array_filter($all_packets, function($packet) use ($inputType, $inputMethod) {
                return $packet['type'] === $inputType && $packet['method'] === $inputMethod;
            });
            $data = array('packets' => array_values($filtered_packets));
        }

        $data['append'] = 0.00;

        // Adjust the packet price based on the append value if needed
        if (!empty($data['append'])) {
            foreach ($data['packets'] as $key => $packet) {
                $packet_data = json_decode($packet['data'], true);
                $months = round(($packet_data['premium_days'] / 30));
                $data['packets'][$key]['price'] += ($months * $data['append']);
            }
        }

        if (!empty($data['packets'])) {
            Session::set('virtual_products', $data);
        }

        \Log::info('Final Response data: ' . json_encode($data));

        return Response::forge(json_encode(array(
            'success' => true,
            'message' => $inputMethod,
            'data' => $data
        )), 200, array('Content-Type' => 'application/json'));
    }
}