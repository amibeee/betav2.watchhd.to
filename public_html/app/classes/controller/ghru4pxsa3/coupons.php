<?php
class Controller_Ghru4pxsa3_Coupons extends Controller_Base_Admin
{

    /**
     *  
     */     
    public function action_index()
    {   
        
        if(Input::method() == 'POST' && Input::post('btn', '') == 'do it so')
        {
            
            foreach(Input::post('id', array()) as $id => $value)
            {
                
                $coupon = Model_Coupon::query()->where('id', '=', $id)->get_one();
                if(!$coupon) continue;
                
                if(Input::post('option', '') == 'delete')
                {
                    $coupon->delete();
                }
                
            }
            
        }
        
        if(Input::method() == 'POST' && Input::post('btn', '') == 'Gutscheine erstellen')
        {
            
            $discount = (int)trim(Input::post('discount', ''));
            $coupons = (int)trim(Input::post('coupons', ''));
            
            $errors = array();
            
            if($discount == 0)
            {
                $errors[] = 'Bitte gebe einen gültigen Wert für Rabatt an.';
            }
            
            if($discount > 25)
            {
                $errors[] = 'Coupons können einen max. Rabatt von 25% haben.';
            }
            
            if($coupons == 0)
            {
                $errors[] = 'Bitte wähle aus wieviele Coupons erstellt werden sollen.';
            }
            
            if(count($errors) == 0)
            {
                
                for($i = 0; $i < $coupons; $i++)
                {
                    
                    $coupon = new Model_Coupon;
                    do{
                        $coupon->code = Str::random('uuid');
                    }
                    while(Model_Coupon::query()->where('code', '=', $coupon->code)->count());
                    $coupon->discount = $discount;
                    $coupon->save();
                    
                }
                
                \Messages::success($coupons.' Coupons wurden erstellt.');
                \Messages::redirect('ghru4pxsa3/coupons');
                
            } 
            else
            {
                
                \Session::set_flash('input.old', Input::post());
                \Messages::error(implode("<br />", $errors));
                \Messages::redirect('ghru4pxsa3/coupons');
    
            }
            
            
        }
        
        $query = Model_Coupon::query();
        
        $pagination_config = array(
            'name'              => 'bootstrap3',
	    	'pagination_url' 	=> \Uri::base().'ghru4pxsa3/coupons',
	    	'per_page' 		 	=> 100,
	    	'total_items' 		=> $query->count(),
    		'num_links'   		=> 4,
    		'uri_segment' 		=> 3,
    		'show_first'        => true,
			'show_last'         => true,
    	);
        
        $pagination = Pagination::forge('', $pagination_config);
        
        $per_page 	= $pagination->per_page;
        $offset 	= $pagination->offset;
   
        $query->order_by('created_at', 'DESC');
        $query->offset($offset);
        $query->limit($pagination->per_page);
        
        $data['pagination'] = Pagination::instance('')->render();
        
        $data['coupons'] = $query->get();
        
        return Response::forge(View::forge('ghru4pxsa3/coupons/list.html.twig', isset($data) ? $data : array(), false)); 
    
    }
    
    /**
     * 
     */
    public function post_delete($coupon_id)
    {
        
        
        $coupon = Model_Coupon::query()->where('id', '=', $coupon_id)->get_one();
        if(!$coupon) throw new HttpNotFoundException;
                
        $coupon->delete();
        
        \Messages::success('Coupon wurde gelöscht.');
        \Messages::redirect('ghru4pxsa3/coupons');
        
    }
    
}