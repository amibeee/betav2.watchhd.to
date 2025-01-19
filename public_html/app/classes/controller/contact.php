<?php
class Controller_Contact extends Controller_Base_Public
{
    
    /**
     * 
     */
    public function action_index()
    {
        return Response::forge(View::forge('contact.html.twig', isset($data) ? $data : array(), false));
    }
    
}