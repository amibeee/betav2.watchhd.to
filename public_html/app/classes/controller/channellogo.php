<?php
class Controller_Channellogo extends Controller
{
    
    /**
     * 
     */
    public function action_index($channel_id, $dimensions)
    {
        
        $channel = Model_Channel::query()->where('id', '=', $channel_id)->get_one();
        
        if(!$channel) throw new HttpNotFoundException;
        if(!is_file(DOCROOT.$channel->logo)) throw new HttpNotFoundException;
        if(!in_array($dimensions, array('132x26', '100x100'))) throw new HttpBadRequestException;
    
        Image::load(DOCROOT.$channel->logo)->resize(explode('x', $dimensions)[0], explode('x', $dimensions)[1])->output();
        
        
    }
    
}