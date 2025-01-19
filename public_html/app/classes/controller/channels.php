<?php
class Controller_Channels extends Controller_Base_User
{
    
    /**
     * 
     */
    public function action_index()
    {
        
        $data['channels'] = array();
        foreach(Model_Channel::query()->where('active', '=', 1)->order_by('position', 'ASC')->get() as $channel)
        {
            $data['channels'][$channel->category][] = $channel;
        }    
        
        return Response::forge(View::forge('channels.html.twig', isset($data) ? $data : array(), false)); 
    
    }
    
}