<?php
class Controller_Base_User extends Controller_Base
{    
    
    /**
     * 
     */    
    public function before()
    {
        
        parent::before();
        
        if($this->user['is_user'] == false)
        {
            
            \Auth::dont_remember_me();
            \Auth::logout();
            
            \Messages::error('Du bist nicht eingeloggt oder du hast keine Zugriffsrechte für die aufgerufene Seite.');
            Session::set('return_to', Uri::current());
            \Messages::redirect('login');
            
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