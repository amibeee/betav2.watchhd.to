<?php
class Controller_Base_Mod extends Controller_Base
{    
    
    /**
     * 
     */    
    public function before()
    {
        
        parent::before();
        
        if($this->user['is_mod'] == false)
        {
            
            \Auth::dont_remember_me();
            \Auth::logout();
            
            \Messages::error('You are not logged in or you do not have permission to access this page.');
            \Messages::redirect('/mod/login');
            
        }
              
    }
    
    /**
     * 
     */    
    public function after($response)
	{
        parent::after($response);
        return $response;
	}

}