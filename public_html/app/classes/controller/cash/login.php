<?php
class Controller_Cash_Login extends Controller_Base_Public
{

    /**
     *  
     */     
    public function action_index()
    {   
        
        if(Input::method() == 'POST')
        {
            
            $username = trim(Input::post('username', ''));
            $password = trim(Input::post('password', ''));
            
            if(!Security::check_token())
            {
                
                Session::set('input.old', Input::post());
                
                \Messages::warning('Invalid Login Token. Please refresh the page and try again.');
                \Messages::redirect(Uri::current());
                
            }
            
            if(empty($username) || empty($password))
            {
                
                Session::set_flash('input.old', Input::post());
                
                \Messages::warning('Please enter your username and your password.');
                \Messages::redirect(Uri::current());
                
            }
            
            if(!($user = Model_User::query()->where('username', '=', $username)->where('password', '=', Auth::instance()->hash_password($password))->where('group', '>=', 25)->get_one()))
            {
            
                Session::set_flash('input.old', Input::post());
            
                \Messages::error('Invalid Login credentials.');
                \Messages::redirect(Uri::current());
            
            }
            
            Auth::force_login($user->id);
            Auth::remember_me();
            
            Response::redirect('cash/dashboard');
            
        }
	            
        return Response::forge(View::forge('cash/login.html.twig', isset($data) ? $data : array(), false)); 
    
    }
    
}
