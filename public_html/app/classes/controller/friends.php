<?php
class Controller_Friends extends Controller_Base_User
{
    
    /**
     * 
     */
    public function action_index()
    {
        
        $data['friends'] = Model_User::query()->where('referred_user_id', '=', $this->user['uid'])->order_by('created_at', 'DESC')->get();
            
        return Response::forge(View::forge('friends.html.twig', isset($data) ? $data : array(), false));
    
    }
    
}