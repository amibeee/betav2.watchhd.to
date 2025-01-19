<?php
class Controller_Base_Affiliate extends Controller_Base
{    
    
    /**
     * 
     */    
    public function before()
    {
        
        parent::before();
        
        if($this->user['is_affiliate'] == false)
        {
            
            \Auth::dont_remember_me();
            \Auth::logout();
            
            \Messages::error('You are not logged in or you do not have permission to access this page.');
            \Messages::redirect('/cash/login');
            
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