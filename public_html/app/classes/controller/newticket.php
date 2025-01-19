<?php
class Controller_Newticket extends Controller_Base_Public
{
    
    /**
     * 
     */
    public function action_index()
    {
        
        if(Input::method() == 'POST')
        {
            
            $name = trim(Input::post('name', ''));
            $email = trim(Input::post('email', ''));
            $subject = trim(Input::post('subject', ''));
            $category = trim(Input::post('category', ''));
            $message = trim(Input::post('message', ''));
            $solution = trim(Input::post('solution', ''));
            
            $errors = array();
            
            if(empty($name))
            {
                $errors[] = i18n::t('Bitte teile uns mit wie wir dich ansprechen sollen.');
            }
            
            if(!filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                $errors[] = i18n::t('Bitte teile uns deine Emailadresse mit.');
            }
            
            if(empty($subject))
            {
                $errors[] = i18n::t('Bitte teile uns in einem kurzen Satz mit um was es geht. Das sehen wir zuerst.');
            }
            
            if(empty($message))
            {
                $errors[] = i18n::t('Bitte teile uns mit was du uns mitteilen möchtest.');
            }
            

            
            if(count($errors) == 0)
            {
                
                $ticket = new Model_Ticket;
                do{
                    $ticket->public_id = Str::random('sha1');
                }
                while(Model_Ticket::query()->where('public_id', '=', $ticket->public_id)->count());
                $ticket->user_id = $this->user['uid'];
                $ticket->category = $category;
                $ticket->subject = $subject;
                $ticket->contents = $message;
                $ticket->name = $name;
                $ticket->email = $email;
                $ticket->closed = 0;
                $ticket->updated_at = time();
		$ticket->status = 'wartend auf Antwort';
                $ticket->save();
                
                $email = Email::forge();
                $email->to($ticket->email);
        
                $email->subject('[watchhd.to] Dein Support Ticket wurde erstellt');
                
                $email_data = array(
                    'name' => $name,
                    'link' => Uri::base().'ticket/'.$ticket->public_id
                );
                
                $email->html_body(\View::forge('email/newticket.html.twig', $email_data));
                
                try
                {
                    $email->send();
                }
                catch(\EmailSendingFailedException $e)
                {
                    // The driver could not send the email
                }
                
                \Messages::success(i18n::t('Dein Support Ticket wurde erstellt. Wir werden uns bald um dein Anliegen kümmern.'));
                \Messages::redirect('ticket/'.$ticket->public_id);
                
            }
            else
            {
                
                Session::set_flash('input.old', Input::post());
                
                \Messages::error(implode('<br />', $errors));
                \Messages::redirect(Uri::current());
                
            }
            
        }
        
        // $data['captcha'] = Captcha::forge('recaptcha')->html('recaptcha/2015');
        
        return Response::forge(View::forge('newticket.html.twig', isset($data) ? $data : array(), false)); 
    
    }
    
}