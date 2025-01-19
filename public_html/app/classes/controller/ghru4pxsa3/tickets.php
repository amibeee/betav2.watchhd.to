<?php
class Controller_Ghru4pxsa3_Tickets extends Controller_Base_Admin
{

    /**
     *
     */
    public function action_index()
    {


        // execute actions
        if(Input::method() == 'POST')
        {

            $action = Input::post('action', '');

            switch($action)
            {
                
                case 'delete_30Days':
								
					foreach(Model_Ticket::query()->where(DB::expr('DATE(FROM_UNIXTIME(created_at))'), '<', DB::expr('NOW() - INTERVAL 30 DAY'))->get() as $ticket)
					{
						DB::delete('ticket_replies')->where('ticket_id', $ticket->id)->execute();
						$ticket->delete();
					}
					
					Messages::success('Tickets älter als 30 Tage wurden gelöscht.');
					Messages::redirect('ghru4pxsa3/tickets');
					
                break;

                case 'delete_60Days':
					
					foreach(Model_Ticket::query()->where(DB::expr('DATE(FROM_UNIXTIME(created_at))'), '<', DB::expr('NOW() - INTERVAL 60 DAY'))->get() as $ticket)
					{
						DB::delete('ticket_replies')->where('ticket_id', $ticket->id)->execute();
						$ticket->delete();
					}
					
					Messages::success('Tickets älter als 60 Tage wurden gelöscht.');
					Messages::redirect('ghru4pxsa3/tickets');
				
                break;

                case 'delete_90Days':
				
					foreach(Model_Ticket::query()->where(DB::expr('DATE(FROM_UNIXTIME(created_at))'), '<', DB::expr('NOW() - INTERVAL 90 DAY'))->get() as $ticket)
					{
						DB::delete('ticket_replies')->where('ticket_id', $ticket->id)->execute();
						$ticket->delete();
					}
					
					Messages::success('Tickets älter als 90 Tage wurden gelöscht.');
					Messages::redirect('ghru4pxsa3/tickets');
				
                break;

            }

        }

        $search_query= trim(Input::post('query', ''));

        $query = DB::select(
            'users.username',
            'tickets.*'
        )->from('tickets');
        $query->join('users', 'LEFT');
        $query->on('tickets.user_id', '=', 'users.id');
        $query->where('tickets.closed', '=', 0);

        if(!empty($search_query))
        {

            $query->where_open();
            $query->where('subject', 'LIKE', "%{$search_query}%");
            $query->or_where('users.username', 'LIKE', "%{$search_query}%");
            $query->where_close();

        }

        $pagination_config = array(
            'name'              => 'bootstrap3',
	    	'pagination_url' 	=> \Uri::base().'ghru4pxsa3/tickets',
	    	'per_page' 		 	=> 100,
	    	'total_items' 		=> count($query->execute()),
    		'num_links'   		=> 4,
    		'uri_segment' 		=> 3,
    		'show_first'        => true,
			'show_last'         => true,
    	);

        $pagination = Pagination::forge('', $pagination_config);

        $per_page 	= $pagination->per_page;
        $offset 	= $pagination->offset;

        $query->order_by('updated_at', 'DESC');
        $query->offset($offset);
        $query->limit($pagination->per_page);

        $data['pagination'] = Pagination::instance('')->render();
        $data['tickets'] = $query->as_object()->execute();

        return Response::forge(View::forge('ghru4pxsa3/tickets/list.html.twig', isset($data) ? $data : array(), false));

    }

    /**
     *
     */
    public function action_read($ticket_id, $action = '')
    {
        $ticket_id = (int)$ticket_id;
        
        $ticket = Model_Ticket::query()->where('id', '=', $ticket_id)->get_one();
        if(!$ticket) throw new HttpNotFoundException;

        $ticket->user = Model_User::query()->where('id', '=', $ticket->user_id)->get_one();

        /* Save Reply */
        if(Input::method() == 'POST')
        {

            $contents = trim(Input::post('contents', ''));

            if(!empty($contents))
            {

                $reply = new Model_Ticket_Reply;
                $reply->ticket_id = $ticket_id;
                $reply->user_id = $this->user['uid'];
                $reply->contents = $contents;
                $reply->name = $this->user['username'];
                $reply->save();

                $email = Email::forge();
                $email->to($ticket->email);

                $email->subject('[watchhd.to] Dein Support Ticket');

                $email_data = array(
                    'name' => $ticket->name,
                    'link' => Uri::base().'ticket/'.$ticket->public_id
                );

                $email->html_body(\View::forge('email/ticketreply.html.twig', $email_data));

                try
                {
                    $email->send();
                }
                catch(\EmailSendingFailedException $e)
                {
                    // The driver could not send the email
                }

                $ticket->updated_at =  time();
                $ticket->save();

                \Messages::success('Ticket Antwort wurde gespeichert.');
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
        $query->where('ticket_replies.ticket_id', '=', $ticket_id);
        $query->order_by('ticket_replies.created_at', 'ASC');

        $ticket->replies = $query->as_object()->execute();
        $data['ticket'] = $ticket;

        return Response::forge(View::forge('ghru4pxsa3/tickets/read.html.twig', isset($data) ? $data : array(), false));

    }

    /**
     *
     */
    public function action_close($ticket_id)
    {

        $ticket = Model_Ticket::query()->where('id', '=', $ticket_id)->get_one();
        if(!$ticket) throw new HttpNotFoundException;

        $ticket->closed = 1;
	    $ticket->status = 'geschlossen';
        $ticket->save();

        \Messages::success('Ticket wurde geöffnet.');
        \Messages::redirect('ghru4pxsa3/ticket/'.$ticket_id.'/read');

    }

    /**
     *
     */
    public function action_open($ticket_id)
    {

        $ticket = Model_Ticket::query()->where('id', '=', $ticket_id)->get_one();
        if(!$ticket) throw new HttpNotFoundException;

        $ticket->closed = 0;
	    $ticket->status = 'wartend auf Antwort';
        $ticket->save();

        \Messages::success('Ticket wurde geöffnet.');
        \Messages::redirect('ghru4pxsa3/ticket/'.$ticket_id.'/read');

    }

    /**
     *
     */
    public function post_delete($ticket_id)
    {



        $ticket = Model_Ticket::query()->where('id', '=', $ticket_id)->get_one();
        if(!$ticket) throw new HttpNotFoundException;

        DB::delete('ticket_replies')->where('ticket_id', '=', $ticket_id)->execute();
        $ticket->delete();

        \Messages::success('Ticket wurde gelöscht!');
        \Messages::redirect('ghru4pxsa3/tickets');

    }


}
