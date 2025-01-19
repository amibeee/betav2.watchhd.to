<?php
class Controller_Tickets extends Controller_Base_User
{
    
    /**
     * 
     */
    public function action_index()
    {
        
        $query = Model_Ticket::query()->where('user_id', '=', $this->user['uid']);
        if(Input::post('closed', '') != '')
        {
            $query->where('closed', '=', Input::post('closed'));
        }
        $query->order_by('created_at', 'DESC')->get();
        
        $data['tickets'] = $query->get();
        
        return Response::forge(View::forge('tickets.html.twig', isset($data) ? $data : array(), false)); 
        
    }

}