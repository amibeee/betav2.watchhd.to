<?php
class EPG
{
    
    /**
     * 
     */
    public static function getProgrammeForDay($channel_id, $day)
    {
        
        $programme = array();
        
        $date = preg_replace('/([0-9]{2}).([0-9]{2}).([0-9]{4})/', '$3-$2-$1', $day);
        
        if(file_exists(APPPATH.'storage/tvguide/'.$date.'/'.$channel_id.'.gz.js'))
        {
                
            $_programme = json_decode(file_get_contents(APPPATH.'storage/tvguide/'.$date.'/'.$channel_id.'.gz.js'));
            if(json_last_error() == JSON_ERROR_NONE)
            {
                
                foreach($_programme->jsontv->programme as $programm)
                    {
                        $programme[] = $programm;
                    }
                 
            }
                
        }
        
        return $programme;
        
    }
    
    /**
     * 
     */
    public static function getDaysDataAvailableFor($channel_id)
    {
        
        $days = array();
        
        $timestamp = (time()-3600);
        do{
            
            $timestamp = ($timestamp+3600);
            
            if(file_exists(APPPATH.'storage/tvguide/'.date("Y-m-d", $timestamp).'/'.$channel_id.'.gz.js'))
            {
                
                $_programme = json_decode(file_get_contents(APPPATH.'storage/tvguide/'.date("Y-m-d", $timestamp).'/'.$channel_id.'.gz.js'));
                if(json_last_error() == JSON_ERROR_NONE)
                {
                    $day = date('d.m.Y', $timestamp);
                    if(!in_array($day, $days))
                    {
                        $days[] = $day;
                    }  
                }
                
            }
            
        }
        while(file_exists(APPPATH.'storage/tvguide/'.date("Y-m-d", $timestamp).'/'.$channel_id.'.gz.js'));
    
        return $days;        
    
    }
    
    /**
     * 
     */
    public static function programme($channel_id, $limit = 5)
    {
        
        $epg_list = array();
        
        $programme = array();
        
        /* yesterday */
        if(file_exists(APPPATH.'storage/tvguide/'.date("Y-m-d", strtotime('- 2 days')).'/'.$channel_id.'.gz.js'))
        {
            
            $_programme = json_decode(file_get_contents(APPPATH.'storage/tvguide/'.date("Y-m-d", strtotime('- 2 days')).'/'.$channel_id.'.gz.js'));
            if(json_last_error() == JSON_ERROR_NONE)
            {
                
                if(isset($_programme->jsontv->programme))
                {
                    
                    foreach($_programme->jsontv->programme as $programm)
                    {
                        $programme[] = $programm;
                    }
                    
                }
              
            }
            
        }
        
        /* today */
        if(file_exists(APPPATH.'storage/tvguide/'.date("Y-m-d", strtotime('- 1 day')).'/'.$channel_id.'.gz.js'))
        {
            
            $_programme = json_decode(file_get_contents(APPPATH.'storage/tvguide/'.date("Y-m-d", strtotime('- 1 day')).'/'.$channel_id.'.gz.js'));
            if(json_last_error() == JSON_ERROR_NONE)
            {
                
                if(isset($_programme->jsontv->programme))
                {
                    
                    foreach($_programme->jsontv->programme as $programm)
                    {
                        $programme[] = $programm;
                    }
                    
                }
              
            }
            
        }
        
        /* tomorrow */
        if(file_exists(APPPATH.'storage/tvguide/'.date("Y-m-d").'/'.$channel_id.'.gz.js'))
        {
            
            $_programme = json_decode(file_get_contents(APPPATH.'storage/tvguide/'.date("Y-m-d").'/'.$channel_id.'.gz.js'));
            if(json_last_error() == JSON_ERROR_NONE)
            {
                
                if(isset($_programme->jsontv->programme))
                {
                    
                    foreach($_programme->jsontv->programme as $programm)
                    {
                        $programme[] = $programm;
                    }
                    
                }
              
            }
            
        }
     
        for($i = 0; $i < count($programme); $i++)
        {
            
            if((int)$programme[$i]->start > time())
            {
                $epg_list[] = $programme[$i];
            }
            
            
            if(count($epg_list) >= $limit) break;
                    
        }
     
        return $epg_list;
        
    }
    
    /**
     * 
     */
    public static function getProgrammObjectForTimespan($channel_id, $start, $end)
    {
        
        $programme = array();
        
        $timestamp = (time()-360);
        do{
            
            $timestamp = ($timestamp+3600);
            
            if(file_exists(APPPATH.'storage/tvguide/'.date("Y-m-d", $timestamp).'/'.$channel_id.'.gz.js'))
            {
                
                $_programme = json_decode(file_get_contents(APPPATH.'storage/tvguide/'.date("Y-m-d", $timestamp).'/'.$channel_id.'.gz.js'));
                if(json_last_error() == JSON_ERROR_NONE)
                {
                    
                    if(isset($_programme->jsontv->programme))
                    {
                        
                        foreach($_programme->jsontv->programme as $programm)
                        {
                            $programme[] = $programm;
                        }
                        
                    }
                  
                }
                
            }
            
        }
        while(file_exists(APPPATH.'storage/tvguide/'.date("Y-m-d", $timestamp).'/'.$channel_id.'.gz.js'));
        
        foreach($programme as $programm)
        {
            
            
            if(((int)$programm->start == $start && (int)$programm->stop == $end) && (int)$programm->start > time())
            {
                return $programm;
            }
            
        }
        
        return false;
        
    }
    
    /**
     * 
     */
    public static function nowOnTV($channel_id)
    {
        
        $programme = array();
        
        $files = array();
        
        /* yesterday */
        $files[] = APPPATH.'storage/tvguide/'.date("Y-m-d", strtotime('- 2 days')).'/'.$channel_id.'.gz.js';
        if(file_exists(APPPATH.'storage/tvguide/'.date("Y-m-d", strtotime('- 2 days')).'/'.$channel_id.'.gz.js'))
        {
            
            $_programme = json_decode(file_get_contents(APPPATH.'storage/tvguide/'.date("Y-m-d", strtotime('- 2 days')).'/'.$channel_id.'.gz.js'));
            if(json_last_error() == JSON_ERROR_NONE)
            {
                
                if(isset($_programme->jsontv->programme))
                {
                    
                    foreach($_programme->jsontv->programme as $programm)
                    {
                        $programme[] = $programm;
                    }
                    
                }
              
            }
            
        }
        
        /* today */
        $files[] = APPPATH.'storage/tvguide/'.date("Y-m-d", strtotime('- 1 day')).'/'.$channel_id.'.gz.js';
        if(file_exists(APPPATH.'storage/tvguide/'.date("Y-m-d", strtotime('- 1 day')).'/'.$channel_id.'.gz.js'))
        {
            
            $_programme = json_decode(file_get_contents(APPPATH.'storage/tvguide/'.date("Y-m-d", strtotime('- 1 day')).'/'.$channel_id.'.gz.js'));
            if(json_last_error() == JSON_ERROR_NONE)
            {
                
                if(isset($_programme->jsontv->programme))
                {
                    
                    foreach($_programme->jsontv->programme as $programm)
                    {
                        $programme[] = $programm;
                    }
                    
                }
              
            }
            
        }
        
        /* tomorrow */
        $files[] = APPPATH.'storage/tvguide/'.date("Y-m-d").'/'.$channel_id.'.gz.js';
        if(file_exists(APPPATH.'storage/tvguide/'.date("Y-m-d").'/'.$channel_id.'.gz.js'))
        {
            
            $_programme = json_decode(file_get_contents(APPPATH.'storage/tvguide/'.date("Y-m-d").'/'.$channel_id.'.gz.js'));
            if(json_last_error() == JSON_ERROR_NONE)
            {
                
                if(isset($_programme->jsontv->programme))
                {
                    
                    foreach($_programme->jsontv->programme as $programm)
                    {
                        $programme[] = $programm;
                    }
                    
                }
              
            }
            
        }
        
        foreach($programme as $programm)
        {
            
            if((int)$programm->start < time() && (int)$programm->stop > time())
            {
                return $programm;
            }
                    
        }
        
        
     
        return 'EPG Informationen nicht verfügbar';
        
    }
    
    /**
     * 
     */
    public static function nextOnTV($channel_id)
    {
        
        
        $programme = array();
        
        /* yesterday */
        if(file_exists(APPPATH.'storage/tvguide/'.date("Y-m-d", strtotime('- 2 days')).'/'.$channel_id.'.gz.js'))
        {
            
            $_programme = json_decode(file_get_contents(APPPATH.'storage/tvguide/'.date("Y-m-d", strtotime('- 2 days')).'/'.$channel_id.'.gz.js'));
            if(json_last_error() == JSON_ERROR_NONE)
            {
                
                if(isset($_programme->jsontv->programme))
                {
                    
                    foreach($_programme->jsontv->programme as $programm)
                    {
                        $programme[] = $programm;
                    }
                    
                }
              
            }
            
        }
        
        /* today */
        if(file_exists(APPPATH.'storage/tvguide/'.date("Y-m-d", strtotime('- 1 day')).'/'.$channel_id.'.gz.js'))
        {
            
            $_programme = json_decode(file_get_contents(APPPATH.'storage/tvguide/'.date("Y-m-d", strtotime('- 1 day')).'/'.$channel_id.'.gz.js'));
            if(json_last_error() == JSON_ERROR_NONE)
            {
                
                if(isset($_programme->jsontv->programme))
                {
                    
                    foreach($_programme->jsontv->programme as $programm)
                    {
                        $programme[] = $programm;
                    }
                    
                }
              
            }
            
        }
        
        /* tomorrow */
        if(file_exists(APPPATH.'storage/tvguide/'.date("Y-m-d").'/'.$channel_id.'.gz.js'))
        {
            
            $_programme = json_decode(file_get_contents(APPPATH.'storage/tvguide/'.date("Y-m-d").'/'.$channel_id.'.gz.js'));
            if(json_last_error() == JSON_ERROR_NONE)
            {
                
                if(isset($_programme->jsontv->programme))
                {
                    
                    foreach($_programme->jsontv->programme as $programm)
                    {
                        $programme[] = $programm;
                    }
                    
                }
              
            }
            
        }
        
        foreach($programme as $programm)
        {
            
            if((int)$programm->start > time())
            {
                
                $p = $programm;
                
                //return $programm;
                foreach($programme as $programm)
                {
                    if((int)$programm->start > (int)$p->start)
                    {
                        return $programm;
                    }
                }
                
            }
                    
        }
     
        return 'Information nicht verfügbar';
        
    }
    
}