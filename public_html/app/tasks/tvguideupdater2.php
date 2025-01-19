<?php
namespace Fuel\Tasks;
set_time_limit(0);
function get_http_response_code($url)
{
    $headers = get_headers($url);
    return substr($headers[0], 9, 3);
}


class Tvguideupdater2
{
    
    public static function run()
	{
        
        foreach(glob(APPPATH.'cache/tvguide/*.gz') as $source)
        {
            
            $packages = array();
                   
            $xml = simplexml_load_file("compress.zlib://".$source);
            foreach($xml->programme as $programm)
            {
                            
                $packages[sha1(date('Y-m-d', strtotime((string)$programm['start'])).'_'.(string)$programm['channel'])][] = array(
                    'title' => array('de' => (string)$programm->title),
                    'start' => strtotime((string)$programm['start']),
                    'stop' => strtotime((string)$programm['stop']),
                    'channel' => (string)$programm['channel']
                );
                      
            }
            unset($xml);
            
            foreach($packages as $id => $bundle)
        {
            
            $date = date('Y-m-d', $bundle[0]['start']);
            
            $package['jsontv']['programme'] = $bundle;
            
            if(!is_dir(APPPATH.'storage/tvguide/'.$date)) @mkdir(APPPATH.'storage/tvguide/'.$date, 0777, true);
            file_put_contents(APPPATH.'storage/tvguide/'.$date.'/'.str_replace('/', '-', $bundle[0]['channel']).'.gz.js', json_encode($package));
                      
        }     
            
        }
	   
    }   
    
}