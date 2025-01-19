<?php
namespace Securelink;

class Securelink
{
    
    /**
     * 
     * 
     * 
     */
    public static function generate($link, $identifier = 'global')
    {
        
        $token = \Security::generate_token();
        \Session::set('securelink.tokens.'.sha1(trim($identifier)), array(
            'identifier' => $identifier,
            'value' => $token,
            'ttl' => time()+3600
        ));
        
        return \Uri::create($link, array(), array('token' => $token));
        
    }

    /**
     * 
     * 
     * 
     */    
    public static function validate($token, $identifier = 'global')
    {

        $token_data = \Session::get('securelink.tokens.'.sha1($identifier));
        
        if($token_data === null){ return false; }
        
        if(self::keys_exists(array('identifier', 'value', 'ttl'), $token_data) !== true)
        {
           
            \Session::delete('securelink.tokens.'.sha1($identifier));
            return false;
            
        }
        
        if($token !== $token_data['value'])
        {
            
            \Session::delete('securelink.tokens.'.sha1($identifier));
            return false; 
               
        }
        
        if(time() > $token_data['ttl'])
        { 
            
            \Session::delete('securelink.tokens.'.sha1($identifier));
            return false;
            
        }
        
        \Session::delete('securelink.tokens.'.sha1($identifier));
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