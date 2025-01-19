<?php
class Controller_Language extends Controller_Base_Public
{
    
    /**
     * 
     */
    public function action_index($language)
    {
        
        Session::set('language', $language);
        Response::redirect('/');
        
    }
    
}