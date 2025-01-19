<?php
class Controller_Recorder extends Controller_Base_User
{
    
    /**
     * 
     */
    public function action_index()
    {

        
        return Response::forge(View::forge('recorder.html.twig', isset($data) ? $data : array(), false)); 
    }
    
}