<?php 
Class Controller_Pin extends Controller_Base_Public
{

    /**
     * 
     */
    public function action_index()
    {
        
       
        return Response::forge(View::forge('pin.html.twig', isset($data) ? $data : array(), false)); 
    
    }

}