<?php
class Controller_Signup extends Controller_Base_Public
{
    public function action_index()
    {   
        if(Input::method() == 'POST')
        {
            $username = trim(Input::post('fG73qkmf3xFsk6sp5J722GqPbFyC6zdzAnfX6Rv34G', ''));
            $email = trim(Input::post('Sqx8gVsJKHpkEQyCcMBRa6mh5QjgzR6BnjwtQKhVrv', ''));
            $friend_user_id = trim(Input::get('friend'));
             
            $friend = Model_User::query()->where('id', '=', $friend_user_id)->get_one();
           
            $errors = array();

            if(!Security::check_token())
            {
                $errors[] = i18n::t('Ungültiges Formular Token. Bitte Seite neu laden und erneut versuchen.');
            }

            # username
            if(empty($username))
            {
                $errors[] = i18n::t('Bitte gebe einen Benutzernamen an.');
            }
            
            if(!empty($username) && !preg_match('/^[A-Za-z][A-Za-z0-9]{3,20}$/', $username))
            {
                $errors[] = i18n::t('Der Benutzername enthält ungültige Zeichen.');  
            }
            
            if(!empty($username) && Model_User::query()->where('username', '=', $username)->count())
            {
                $errors[] = i18n::t('Dieser Benutzername wird bereits genutzt.');
            }
        
            # email
            if(empty($email))
            {
                $errors[] = i18n::t('Bitte gebe deine Email Adresse an.');
            }
            
            if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                $errors[] = i18n::t('Bitte gebe eine gültige Email Adresse an.');
            }
            
            if(!empty($email) && Model_User::query()->where('email', '=', $email)->count())
            {
                $errors[] = i18n::t('Diese Email Adresse wird bereits genutzt.');  
            }
            
            if(Input::post('email', '') != '')
            {
                $errors[] = i18n::t('Du bist in unseren Honigtopf getreten. :p');
            }

            if(count($errors) == 0)
            {
                $activation_token = Str::random('sha1');
                $password = Str::random();
                $user = new Model_User;
                $user->username = $username;
                $user->group = 1;
                $user->email = $email;
                $user->password = Auth::instance()->hash_password($password);
                $user->salt = Str::random('sha1'); // Ensure salt is set
                $user->last_login = time();
                $user->login_hash = Str::random('sha1');
                $user->profile_fields = '';
                $user->password_recovery_token = '';
                $user->password_recovery_token_requested_at = 0;
                $user->activated = 0;
                $user->tokens = 0;
                $user->premium_until = 0;
                $user->activated_at = 0;
                $user->activation_token = $activation_token;
                $user->email_notification_premium_ends = 1;
                $user->email_notification_record_download_available = 1;
                $user->email_notification_friend_sale = 1;
                $user->referred_user_id = ($friend ? $friend->id : 0);
                $user->affiliate_tracking_id = '';
                $user->account_balance = 0.00;
                $user->reminder_send_at = 0;
                $user->pin = '';
                $user->line_password = '';

                $affiliate_id = 0;
                if($affiliate = Session::get('affiliate', false))
                {
                    if(Model_User::query()->where('id', '=', $affiliate[0])->count())
                    {
                        $affiliate_id = $affiliate[0];
                    }
                }
                
                $user->affiliate_id = $affiliate_id;
                $user->save();
                
                /* Create Line */
                $line = create_xc_user($username);
                if(!$line)
                {
                    $user->delete();
                    \Messages::error("Wir konnten dein Benutzerkonto nicht erstellen da keine Line für dich erzeugt werden konnte. Bitte wende dich an den Support oder probiere es später nochmal.");
                    \Messages::redirect('signup');
                }
                
                $user->line_password = $line['password'];
                $user->save();

                if($affiliate_id)
                {
                    $log = new Model_Affiliate_Log;
                    $log->affiliate_id = $affiliate_id;
                    $log->user_id = $user->id;
                    $log->type = 'lead';
                    $log->data = json_encode(array());
                    $log->sub_id = $affiliate[1];
                    $log->worth = 0.00;
                    $log->save();
                }

                // Email sending
                $email = Email::forge();
                $email->to($user->email);
                $email->subject('[watchhd.to] Dein Benutzerkonto');

                $email_data = array(
                    'username' => $username,
                    'password' => $password,
                    'link' => Uri::base().'verify/'.$user->id.','.$activation_token
                );

                $email->html_body(\View::forge('email/signup.html.twig', $email_data));

                try {
                    $email->send();
                    \Log::info('Email sent successfully to: ' . $user->email);
                } catch(\EmailSendingFailedException $e) {
                    \Log::error('Failed to send email to: ' . $user->email . '. Error: ' . $e->getMessage());
                    \Messages::error('Es gab ein Problem beim Versenden der E-Mail. Bitte versuche es später erneut oder wende dich an den Support.');
                }

                \Messages::success(i18n::t('Glückwunsch, dein Benutzerkonto wurde erstellt. Deine Zugangsdaten hast du soeben per E-Mail erhalten. Siehe bitte auch im Spam-Ordner nach!'));
                \Messages::redirect('');
            } else {
                Session::set_flash('input.old', Input::post());
                Session::set_flash('error', implode("<br />", $errors));
                \Messages::redirect(Uri::current());
            }
        }
        
        $data['captcha'] = Captcha::forge('recaptcha')->html('recaptcha/2015');
        return Response::forge(View::forge('signup.html', isset($data) ? $data : array(), false)); 
    }
}
