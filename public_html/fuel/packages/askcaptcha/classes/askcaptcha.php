<?php
namespace Askcaptcha;

class Askcaptcha
{
    
    /**
     * 
     * 
     * 
     */
    public static function generate()
    {
        
        $challange = array();
        
        $challange[0]['question'] = "Welche Stadt hat die meisten Einwohner?"; 
        $challange[0]['solution'] = 'Tokio';
        
        $challange[1]['question'] = "Wann gewann Deutschland zuletzt die Weltmeisterschaft?"; 
        $challange[1]['solution'] = '2014';
        
        $challange[2]['question'] = "Wie viele Bundesländer hat Deutschland?"; 
        $challange[2]['solution'] = '16';
        
	shuffle($challange);

        \Session::set('askcaptcha.challenge', $challange[0]);
        
        return $challange[0]['question'];
        
    }

    /**
     * 
     * 
     * 
     */    
    public static function validate($solution)
    {

        $challange = \Session::get('askcaptcha.challenge');
        
        if($challange === null){ return false; }
        
        if(self::keys_exists(array('question', 'solution'), $challange) !== true)
        {
           
            \Session::delete('askcaptcha.challenge');
            return false;
            
        }
        
        if((int)$solution !== (int)$challange['solution'])
        {
            
            \Session::delete('askcaptcha.challenge');
            return false; 
               
        }
        
        \Session::delete('askcaptcha.challenge');
        return true;
        
    }

    /**
     * 
     * 
     * 
     */    
    protected static function keys_exists($keys = array(), $array = array())
    {
        
        foreach($keys as $key)
        {
        
            if(array_key_exists($key, $array) !== true)
            {
                return false;
            }
            
        }
        
        return true;
        
    }
    
}