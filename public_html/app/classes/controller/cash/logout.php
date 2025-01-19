<?php
class Controller_Cash_Logout extends Controller_Base_Public
{
    
    /**
     * 
     */
    public function action_index()
    {
        
        Auth::logout();
        Session::destroy();
        
        \Messages::success('Deine Sitzung wurde beendet. Auf Wiedersehen.');
        \Messages::redirect('/cash/login');
        
    }
    
}