<?php
class Controller_Tvguide extends Controller_Base_User
{
    
    /**
     * 
     */
    public function action_index()
    {
    
        $data['channels'] = Model_Channel::query()->where('active', '=', 1)->order_by('name', 'ASC')->get();
            
        return Response::forge(View::forge('tvguide.html.twig', isset($data) ? $data : array(), false)); 
    
    }
    
}