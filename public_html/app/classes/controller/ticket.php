<?php
class Controller_Ticket extends Controller_Base_Public
{

    /**
     *  
     */     
    public function action_index($ticket_public_id, $action = '')
    {
        
        $ticket = Model_Ticket::query()->where('public_id', '=', $ticket_public_id)->get_one();
        if(!$ticket) throw new HttpNotFoundException;
        
        switch($action)
        {
            
            case 'open':
            
                $ticket->closed = 0;
		$ticket->status = 'wartend auf Antwort';
                $ticket->save();
                
                \Messages::success(i18n::t('Ticket wurde geöffnet.'));
                \Messages::redirect('ticket/'.$ticket_public_id);
            
            break;
            
            case 'close':
            
                $ticket->closed = 1;
		$ticket->status = 'geschlossen';
                $ticket->save();
                
                \Messages::success(i18n::t('Ticket wurde geschlossen.'));
                \Messages::redirect('ticket/'.$ticket_public_id);
            
            break;
            
        }
        
        /* Save Reply */
        if(Input::method() == 'POST')
        {
            
            $contents = trim(Input::post('contents', ''));
            
            if(!empty($contents))
            {
                
                $reply = new Model_Ticket_Reply;
                $reply->ticket_id = $ticket->id;
                $reply->user_id = $this->user['uid'];
                $reply->name = $ticket->name;
                $reply->contents = $contents;
                $reply->save();
                
                $ticket->updated_at = time();
		$ticket->status = 'wartend auf Support Antwort';
                $ticket->save();
                
                \Messages::success(i18n::t('Ticket Antwort wurde gespeichert.'));
                \Messages::redirect(Uri::current());
                
            }
            
            
        }
        /* Save Reply */
            
        $query = DB::select(
            'users.username',
            array('users.id', 'uid'),
            'ticket_replies.*'
        )->from('ticket_replies');
        $query->join('users', 'LEFT');
        $query->on('ticket_replies.user_id', '=', 'users.id');
        $query->where('ticket_replies.ticket_id', '=', $ticket->id);
        $query->order_by('ticket_replies.created_at', 'ASC');
        
        $ticket->replies = $query->as_object()->execute();
        $data['ticket'] = $ticket;

        return Response::forge(View::forge('ticket.html.twig', isset($data) ? $data : array(), false)); 
        
    }
    
    
}