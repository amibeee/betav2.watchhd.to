<?php
class Controller_Ghru4pxsa3_Login extends Controller_Base_Public
{

    /**
     *  
     */     
    public function action_index()
    {   
    

            
        return Response::forge(View::forge('work_index.html', isset($data) ? $data : array(), false)); 
    
    }
    
}
