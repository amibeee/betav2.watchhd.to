<?php
Class Controller_Changeemail extends Controller_Base_User
{
    
    /**
     * 
     */
    public function action_index()
    {
        
        if(Input::method() == 'POST')
        {
            
            $email1 = trim(Input::post('email1', ''));
            $email2 = trim(Input::post('email2', ''));
            
            $errors = array();
            
            # email1 & email2
            if(empty($email1))
            {
                $errors[] = i18n::t('Bitte gebe deine neue Emailadresse an.');
            }
            
            if(!empty($email1) && !filter_var($email1, FILTER_VALIDATE_EMAIL))
            {
                $errors[] = i18n::t('Bitte gebe eine gültige Emailadresse an.');
            }
            
            if($email1 != $email2)
            {
                $errors[] = i18n::t('Bitte bestätige im Feld Email Adresse wiederholen deine Emailadresse.');
            }
            
            if(count($errors) == 0)
            {
                
                $token = new Model_Token;
                $token->token = sha1(uniqid().microtime().$this->user['uid']);
                $token->user_id = $this->user['uid'];
                $token->type = 'changeemail';
                $token->data = json_encode(array(
                    'email' => $email1
                ));
                $token->save();
                
                $email = Email::forge();
                $email->to($this->user['email']);
            
                $email->subject('[watchhd.biz] Dein Benutzerkotno');
                    
                $email_data = array(
                    'username' => $this->user['username'],
                    'link' => Uri::base().'changeemail/'.$this->user['uid'].','.$token->token
                );
                    
                $email->html_body(\View::forge('email/changeemail.html.twig', $email_data));
                    
                try
                {
                    $email->send();
                }
                catch(\EmailSendingFailedException $e)
                {
                    // The driver could not send the email
                }
                
                \Messages::success(i18n::t('Wir haben dir einen Link geschickt über den du dein Passwort ändern kannst.'));
                \Messages::redirect('/account');
                
            }
            else
            {
                
                Session::set('input.old', Input::post());
                \Messages::error(implode('<br />', $errors));
                \Messages::redirect(Uri::current());
                
            }
            
        }    
        
        return Response::forge(View::forge('changeemail.html.twig', isset($data) ? $data : array(), false)); 
    
    
    }
    
    /**
     * 
     */
    public function action_changeemail($token)
    {
    
        $user_id = explode(',', $token)[0];
        $token = explode(',', $token)[1];
        
        if(!($token = Model_Token::query()->where('user_id', '=', $user_id)->where('token', '=', $token)->where('type', '=', 'changeemail')->where('created_at', '>', (time()-86400))->get_one()))
        {
            \Messages::error(i18n::t('Token nicht gefunden / Token abgelaufen.'));
            \Messages::redirect('/changeemail');
        }
        
        $user = Model_User::query()->where('id', '=', $user_id)->get_one();
        
        $data = json_decode($token->data);
        
        $user->email = $data->email;
        $user->save();
        
        \Messages::success(i18n::t('Deine Emailadresse wurde geändert.'));
        \Messages::redirect('/channels');
        
    }
    
}