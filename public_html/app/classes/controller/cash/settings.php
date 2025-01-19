<?php
class Controller_Cash_Settings extends Controller_Base_Affiliate
{

    /**
     *  
     */     
    public function action_index()
    {   
        
        $user = Model_User::query()->where('id', '=', $this->user['uid'])->get_one();
        
        if(Input::method() == 'POST')
        {
            
			$email = trim(Input::post('email', ''));
            $current_password = trim(Input::post('current_password', ''));
            $password1 = trim(Input::post('password1', ''));
            $password2 = trim(Input::post('password2', ''));
            
            $errors = array();
            
			if(empty($email))
			{
				$errors[] = i18n::t('Bitte gebe eine Email Adresse an.');
            }
			
			if(!empty($email) && Model_User::query()->where('email', $email)->where('id', '!=', $this->user['uid'])->count())
			{
				$errors[] = i18n::t('Diese Emailadresse wurde bereits verwendet.');
			}
			
            if(Auth::instance()->hash_password($current_password) != $user->password)
            {
                $errors[] = i18n::t('Um die Änderungen zu bestätigen musst du dein altes Passwort angeben.');
            }
            
            if(!empty($password1) && mb_strlen($password1) < 8)
            {
                $errors[] = i18n::t('Dein neues Passwort muss min. 8 Zeichen lang sein.');
            }
            
            if($password1 != $password2)
            {
                $errors[] = i18n::t('Du musst dein neues Passwort im Feld Neues Password (wiederholen) bestätigen.');
            }
            
           
            if(count($errors) == 0)
            {
                       
                if(!empty($password1))
                {
                    $user->password = Auth::instance()->hash_password($password1);
					$user->force_password_reset = 0;
                }
				$user->email = $email;
                $user->save();    
                
                \Messages::success(i18n::t('Änderungen wurde übernommen.'));
                \Messages::redirect('/cash/settings');
                
            }
            else
            {
                
                Session::set_flash('input.old', Input::post());
                \Messages::error(implode('<br />', $errors));
                \Messages::redirect(Uri::current());
                
            }
            
        }
        
        $data['user'] = $user;
            
        return Response::forge(View::forge('cash/settings.html.twig', isset($data) ? $data : array(), false)); 
    
    }
    
}