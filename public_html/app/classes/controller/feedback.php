<?php
class Controller_Feedback extends Controller_Base_Public
{
    
    /**
     * 
     */
    public function action_index($feedback_id)
    {
        
        $feedback = Model_User_Feedback::query()->where('id', '=', $feedback_id)->get_one();
        if(!$feedback) throw new HttpNotFoundException;
        
        if($feedback->done == 1)
        {
            \Messages::error(i18n::t('Wir haben dein Feedback bereits erhalten. Vielen Dank'));
            \Messages::redirect('channels');
        }
        
        if(Input::method() == 'POST')
        {
            
            $_feedback = trim(Input::post('feedback'));
            $rating = trim(Input::post('rating'));
            $reason = trim(Input::post('reason'));
            
            if($rating < 1 || $rating > 6)
            {
               \Messages::error(i18n::t('Bitte bewerte den Kaufprozess.'));
               \Messages::redirect(Uri::current()); 
            }
            
            if(!in_array($reason, array('mistaken', '404', 'boredom', 'other')))
            {
                
                \Messages::error(i18n::t('Bitte wähle einen Grund aus wieso der Zahlungsprozess fehlgeschlagen ist.'));
                \Messages::redirect(Uri::current()); 
            
            }
            
            $feedback->reason = $reason;
            $feedback->feedback = $_feedback;
            $feedback->rating = $rating;
            $feedback->done = 1;
            $feedback->save();
            
            \Messages::success(i18n::t('Vielen Dank für deine Hilfe.'));
            \Messages::redirect('channels');
            
        }
        
        return Response::forge(View::forge('feedback.html.twig', isset($data) ? $data : array(), false));
    
    }
    
}