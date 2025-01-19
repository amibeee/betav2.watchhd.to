<?php
namespace Mathcaptcha;

class Mathcaptcha
{
    
    /**
     * 
     * 
     * 
     */
    public static function generate()
    {
        
        $challange = array();
        
        switch(rand(1, 2))
        {
            
            case 1:
                
                $first = rand(1, 100);
                $second = rand(1, 100);
                
                $challange['math'] = "{$first} + {$second} ="; 
                $challange['solution'] = $first+$second;
                
            break;
            
            case 2:
                
                $first = rand(1, 100);
                $second = rand(1, $first);
                
                $challange['math'] = "{$first} - {$second} ="; 
                $challange['solution'] = $first-$second;
            
            break;
            
        }
        
        \Session::set('mathcaptcha.challenge', $challange);
        
        return $challange['math'];
        
    }

    /**
     * 
     * 
     * 
     */    
    public static function validate($solution)
    {

        $challange = \Session::get('mathcaptcha.challenge');
        
        if($challange === null){ return false; }
        
        if(self::keys_exists(array('math', 'solution'), $challange) !== true)
        {
           
            \Session::delete('mathcaptcha.challenge');
            return false;
            
        }
        
        if((int)$solution !== (int)$challange['solution'])
        {
            
            \Session::delete('mathcaptcha.challenge');
            return false; 
               
        }
        
        \Session::delete('mathcaptcha.challenge');
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