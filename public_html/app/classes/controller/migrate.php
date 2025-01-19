<?php
class Controller_Migrate extends Controller_Base_Public
{
    
    /**
     * 
     */
    public function action_index()
    {
        
        if(Input::method() == 'POST')
        {
            
            $user = trim(Input::post('user', ''));
            
            if(empty($user))
            {
                
                Session::set_flash('input.old', Input::post());
                
                \Messages::error(i18n::t('Bitte gebe deinen Benutzernamen oder deine Emailadresse an.'));
                \Messages::redirect(Uri::current());
                
            }
            
            if(!($user = Model_User::query()->where_open()->where('username', '=', $user)->or_where('email', '=', $user)->where_close()->get_one()))
            {
                
                Session::set_flash('input.old', Input::post());
                
                \Messages::error(i18n::t('Es ist ein Fehler aufgetreten. Bitte versuche es erneut.'));
                \Messages::redirect(Uri::current());
                
            }
            
            $token = new Model_Token;
            $token->token = sha1(uniqid().microtime().$user->id);
            $token->user_id = $user->id;
            $token->type = 'migrate';
            $token->data = json_encode(array());
            $token->save();
            
            $email = Email::forge();
            $email->to($user->email);
        
            $email->subject('[watchhd.biz] Dein Benutzerkotno');
                
            $email_data = array(
                'username' => $user->username,
                'link' => Uri::base().'passwordreovery/'.$user->id.','.$token->token
            );
                
            $email->html_body(\View::forge('email/migrate.html.twig', $email_data));
                
            try
            {
                $email->send();
            }
            catch(\EmailSendingFailedException $e)
            {
                // The driver could not send the email
            }
            
            \Messages::success(i18n::t('Wir haben dir einen Link geschickt über den du dein Passwort für dein Benutzerkonto bestimmen kannst.'));
            \Messages::redirect('/');
            
        }
            
        return Response::forge(View::forge('migrate.html.twig', isset($data) ? $data : array(), false)); 
    
    }
    
    /**
     * 
     */
    public function action_setpassword($token)
    {
    
        $user_id = explode(',', $token)[0];
        $token = explode(',', $token)[1];
        
        if(!($token = Model_Token::query()->where('user_id', '=', $user_id)->where('type', '=', 'migrate')->where('token', '=', $token)->get_one()))
        {
            \Messages::error(i18n::t('Token nicht gefunden / Token abgelaufen.'));
            \Messages::redirect('/migrate');
        }
        
        $user = Model_User::query()->where('id', '=', $user_id)->get_one();
        
        if(Input::method() == 'POST')
        {
            
            $password1 = trim(Input::post('password1', ''));
            $password2 = trim(Input::post('password2', ''));
            
            if(empty($password1))
            {
                
                Session::set_flash('input.old', Input::post());
                
                \Messages::error(i18n::t('Bitte teile uns dein neues Passwort mit.'));
                \Messages::redirect(Uri::current());
                
            }
            
            if(mb_strlen($password1) < 8)
            {
                
                Session::set_flash('input.old', Input::post());
                
                \Messages::error(i18n::t('Dieses Passwort ist zu kurz.'));
                \Messages::redirect(Uri::current());
                
            }
            
            if(!empty($password1) && $password1 != $password2)
            {
                
                Session::set_flash('input.old', Input::post());
                
                \Messages::error('Passwort und Passwort wiederholen müssen übereinstimmen.');
                \Messages::redirect(Uri::current());
                
            }
            
            $user->password = Auth::instance()->hash_password($password1);
            $user->save();
            
            $token->delete();
            
            \Messages::success(i18n::t('Dein Passwort wurde geändert. Du kannst dich nun einloggen'));
            \Messages::redirect('/login');
            
        }
            
        return Response::forge(View::forge('setpassword.html.twig', isset($data) ? $data : array(), false)); 
    
    }
    
}