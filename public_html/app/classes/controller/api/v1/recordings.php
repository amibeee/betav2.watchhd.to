<?php
class Controller_api_v1_Recordings extends Controller_Base_Public
{
    
    /**
     * 
     */
    public function action_index()
    {
        
        $flextime = (5*60);
        
        $buffer = '';
    
        foreach(Model_Recording::query()->where('created_at', '>', time())->get() as $recording)
        {
            $buffer .= $recording->channel_id.";".($recording->start-$flextime).";".($recording->end+$flextime).";".preg_replace('/\.+/', '.', Inflector::friendly_title($recording->title, '.'))."\n";
        }
        
        return new Response($buffer);
        
    }
    
}