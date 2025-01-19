<?php
class Controller_api_v1_Channels extends Controller_Base_Api
{
    
    /**
     * 
     */
    public function action_index()
    {
        
        $channels = array();
        
        if(Input::get('category', '') != '')
        {
            $query = Model_Channel::query()->where('active', '=', 1)->order_by('position', 'ASC')->where('category', '=', Input::get('category'))->get();
        }
        else
        {
            $query = Model_Channel::query()->where('active', '=', 1)->order_by('position', 'ASC')->get();
        }
        
        foreach($query as $channel)
        {
            
            $_channel = (object)array();
            $_channel->id = $channel->id;
            $_channel->name = $channel->name;
            $_channel->category = $channel->category;
            $_channel->description = $channel->description;
            $_channel->programme = EPG::programme($channel->tvguideapi_id, 2);
            $_channel->now = EPG::nowOnTV($channel->tvguideapi_id);
            $_channel->logo = Uri::base().$channel->logo; 
            
            $channels[$channel->category][] = $_channel;
        }  
        
        return $this->response(array('success' => true, 'message' => '', 'data' => array('channels' => $channels) ));
        
    }
    
}