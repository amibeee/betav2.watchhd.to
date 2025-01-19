<?php
class Controller_Recorderwizward extends Controller_Base_User
{
    
    /**
     * 
     */
    public function action_index()
    {
        
        $data['channels'] = Model_Channel::query()->where('active', '=', 1)->order_by('name', 'ASC')->get();
        
        return Response::forge(View::forge('recorderwizard.html.twig', isset($data) ? $data : array(), false)); 
    }
    
}