<?php
class Controller_Logout extends Controller_Base_Public
{
    
    /**
     * 
     */
    public function action_index()
    {
        
        Auth::logout();
        Session::destroy();
        
        \Messages::success(i18n::t('Deine Sitzung wurde beendet. Auf Wiedersehen.'));
        \Messages::redirect('/');
        
    }
    
}