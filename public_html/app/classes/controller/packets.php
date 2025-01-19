<?php 
class Controller_Ajax_Packets extends Controller_Base_Ajax
{
    
    /**
     * 
     */
    public function action_index()
    {
        
        

        $data = array('packets' =>
            Model_Product::query()->where('type', '=', Input::get('type'))->where('method', '=', Input::get('method'))->get()
        );
        $data['append'] = 0.00;
        if(!in_array(31, Session::get('packages', array()))){
            #$data['append'] = 3.00;
        }

        foreach($data['packets'] as $key => $packet){
			$packet_data = json_decode($packet->data, true);
			$months = round(($packet_data['premium_days']/30));
            $data['packets'][$key]->price = ($data['packets'][$key]->price+($months*$data['append']));
        }

        // override ackets
        if((Input::get('method') == 'psc' || Input::get('method') == 'amazoncode' || Input::get('method') == 'trc') && in_array(31, Session::get('packages', array()))){
            
            $data = array();

            $data['packets'][101] = array(
                'id' => '101',
                'name' => '1 Monat Premium',
                'type' => 'premium',
                'price' => 15,
                'data' => json_encode(array('premium_days' => 30)),
                'method' => Input::get('method'),
                'worth' => 0.00,
                'virtual' => true
            );

            $data['packets'][102] = array(
                'id' => '102',
                'name' => '2 Monate Premium',
                'type' => 'premium',
                'price' => 25.00,
                'data' => json_encode(array('premium_days' => 60)),
                'method' => Input::get('method'),
                'worth' => 0.00,
                'virtual' => true
            );

            $data['packets'][103] = array(
                'id' => '103',
                'name' => '4 Monate Premium',
                'type' => 'premium',
                'price' => 50.00,
                'data' => json_encode(array('premium_days' => 120)),
                'method' => Input::get('method'),
                'worth' => 0.00,
                'virtual' => true
            );

            Session::set('virtual_products', $data);

        }
        

        return $this->response(array('success' => true, 'message' => '', 'data'  => $data));
        
    }
    
}
