<?php
namespace Fuel\Tasks;
set_time_limit(0);
function get_http_response_code($url)
{
    $headers = get_headers($url);
    return substr($headers[0], 9, 3);
}


class Tvguideupdater
{

    
    public static function searchChannelProgrammFiles($search, $list)
    {
        
        
       if(preg_match_all("/<a href=\".*\">(.*)<\/a>/", $list, $matches))
        {
        
            $files = array();
            foreach($matches[1] as $file)
            {
                
                if(!empty($search))
                {
                    if(stristr($file, $search)) $files[] = $file;    
                }
                
                
            }
            
            return $files;
          
        }
        
        return array();
        
    }
    
	public static function run()
	{
	   
        $list = file_get_contents('http://json.xmltv.se/');
        
        $i = 0;
        foreach(\Model_Channel::query()->get() as $channel)
        {
            
            \Cli::write($channel->tvguideapi_id);
            $programmFiles = self::searchChannelProgrammFiles($channel->tvguideapi_id, $list);
            foreach($programmFiles as $tvguidefile)
            {
                
                \Cli::write('checking: '.$tvguidefile);
                $date = str_replace('js.gz', '', explode('_', $tvguidefile)[1]);                
                $date = str_replace('.', '', $date);
                
                if(file_exists(APPPATH.'storage/tvguide/'.$date.'/'.explode('_', $tvguidefile)[0].'.gz.js')) 
        		{
        		  
        			\Cli::write(APPPATH.'storage/tvguide/'.$date.'/'.explode('_', $tvguidefile)[0].'.gz.js exits. Continue');
        			continue;
                    
        		}
        		
                
                $reader = file_get_contents('http://json.xmltv.se/'.$tvguidefile, false, stream_context_create(array('http' => array('ignore_errors' => true))));
                
                json_decode($reader);
                if(json_last_error() != JSON_ERROR_NONE) continue;
                
                if(!is_dir(APPPATH.'storage/tvguide/'.$date)) @mkdir(APPPATH.'storage/tvguide/'.$date, 0777, true);
                \Cli::write('Download: http://json.xmltv.se/'.$tvguidefile);
                file_put_contents(APPPATH.'storage/tvguide/'.$date.'/'.explode('_', $tvguidefile)[0].'.gz.js', $reader);
                \Cli::write('Download: http://json.xmltv.se/'.$tvguidefile.' (done)');
                \Cli::write($i);
                $i++;
                
              
                
            }
           
     
        }
        
	}

}