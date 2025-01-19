<?php
class Controller_Premium extends Controller_Base_User
{

    /**
     *  
     */     
    public function action_index()
    {   
            
        return Response::forge(View::forge('premium.html.twig', isset($data) ? $data : array(), false)); 
    
    }
    
}