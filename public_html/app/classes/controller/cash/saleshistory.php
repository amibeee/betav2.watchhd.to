<?php
class Controller_Cash_Saleshistory extends Controller_Base_Affiliate
{

    /**
     *  
     */     
    public function action_index()
    {   

        $data['sales'] = array();
        foreach(Model_Affiliate_Log::query()->where('type', '=', 'sale')->where('affiliate_id', '=', $this->user['uid'])->order_by('created_at', 'desc')->get() as $sale){
            $sale->data = json_decode($sale->data);
            $data['sales'][] = $sale;
        }
        
        return Response::forge(View::forge('cash/saleshistory.html.twig', isset($data) ? $data : array(), false)); 
    
    }
    
}