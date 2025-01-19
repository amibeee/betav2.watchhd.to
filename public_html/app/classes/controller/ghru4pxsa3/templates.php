<?php
class Controller_Ghru4pxsa3_Templates extends Controller_Base_Admin
{

    /**
     *  
     */     
    public function action_index()
    {   
        
        $data['templates'] = Model_Template::query()->order_by('name', 'DESC')->get();

            
        return Response::forge(View::forge('ghru4pxsa3/templates/list.html.twig', isset($data) ? $data : array(), false)); 
    
    }


    /**
     *  
     */     
    public function action_create()
    {   
        
        if(Input::method() == 'POST')
        {

            $name = Input::post('name', '');
            $channels = Input::post('channels', array());
            
            //Debug::dump(Input::post());
            $errors = array();

            if(empty($name))
            {
                $errors[] = 'Bitte Name angeben.';
            }

            $valid_channels = array();
            foreach($channels as $channel)
            {
                
                if(Model_Channel::query()->where('id', $channel)->count() == 0) continue;
                if(!preg_match("~[0-9]{2}:[0-9]{2}~", Input::post('start_time.'.$channel))) continue;
                if(!preg_match("~[0-9]{2}:[0-9]{2}~", Input::post('end_time.'.$channel))) continue;

                $valid_channels[] = array(

                    'channel_id' => $channel,
                    'start_time' => Input::post('start_time.'.$channel),
                    'end_time' => Input::post('end_time.'.$channel),

                );

            }

            if(count($valid_channels) == 0)
            {
                $errors[] = 'Bitte wähle min. 1 Sender aus.';
            }

            if(count($errors) == 0)
            {

                $template = new Model_Template;
                $template->name = $name;
                $template->channels = json_encode($valid_channels);
                $template->starts = '';
                $template->ends = '';
                $template->save();

                Messages::success('Template wurde erstellt');
                Messages::redirect('ghru4pxsa3/templates');

            }
            else
            {

                Messages::error(implode("<br />", $errors));
                Messages::redirect(Uri::current());

            }

        }

        $data['channels'] = Model_Channel::query()->order_by('name', 'DESC')->get();
         
        return Response::forge(View::forge('ghru4pxsa3/templates/create.html.twig', isset($data) ? $data : array(), false)); 
    
    }


    /**
     *  
     */     
    public function action_edit($template_id)
    {   

        $template = Model_Template::query()->where('id', $template_id)->get_one();
        if(!$template)
        {
            throw new NotFoundException;
        }
        
        if(Input::method() == 'POST')
        {

            $name = Input::post('name', '');
            $channels = Input::post('channels', array());

            //Debug::dump(Input::post());
            $errors = array();

            if(empty($name))
            {
                $errors[] = 'Bitte Name angeben.';
            }

            $valid_channels = array();
            foreach($channels as $channel)
            {
                
                if(Model_Channel::query()->where('id', $channel)->count())
                {
                    $valid_channels[] = $channel;
                }

            }

            if(count($valid_channels) == 0)
            {
                $errors[] = 'Bitte wähle min. 1 Sender aus.';
            }

            if(count($errors) == 0)
            {

                $template->name = $name;
                $template->channels = json_encode($valid_channels);
                $template->starts = '';
                $template->ends = '';
                $template->save();

                Messages::success('Änderungen wurden erfolgreich gespeichert');
                Messages::redirect('ghru4pxsa3/templates');

            }
            else
            {

                Messages::error(implode("<br />", $errors));
                Messages::redirect(Uri::current());

            }

        }

        $data['channels'] = Model_Channel::query()->order_by('name', 'DESC')->get();
        $data['template'] = $template;
        $data['selected_channels'] = json_decode($template->channels);
          
        return Response::forge(View::forge('ghru4pxsa3/templates/edit.html.twig', isset($data) ? $data : array(), false)); 
    
    }

    /**
     *  
     */     
    public function action_setup($template_id)
    {   

        $template = Model_Template::query()->where('id', $template_id)->get_one();
        if(!$template)
        {
            throw new NotFoundException;
        }
        
        if(Input::method() == 'POST')
        {

            $name = Input::post('name', '');
            $channels = Input::post('channels', array());
   
            $start_date = Input::post('start_date', '');
            $start_time = Input::post('start_time', array());
            $end_date = Input::post('end_date', '');
            $end_time = Input::post('end_time', array());

            $errors = array();

            if(!preg_match("~[0-9]{4}-[0-9]{2}-[0-9]{2}~", Input::post('start_date')))
            {
                $errors[] = 'Bitte wähle ein gültiges Startdatum.';
            }

            if(!preg_match("~[0-9]{4}-[0-9]{2}-[0-9]{2}~", Input::post('end_date')))
            {
                $errors[] = 'Bitte wähle ein gültiges Enddatum.';
            }

            $valid_channels = array();
            foreach($channels as $channel)
            {

                if(Model_Channel::query()->where('id', $channel)->count() == 0) continue;
                if(!preg_match("~[0-9]{2}:[0-9]{2}~", Input::post('start_time.'.$channel))) continue;
                if(!preg_match("~[0-9]{2}:[0-9]{2}~", Input::post('end_time.'.$channel))) continue;

                $valid_channels[] = array(

                    'channel_id' => $channel,
                    'start_date' => Input::post('start_date'),
                    'start_time' => Input::post('start_time.'.$channel),
                    'end_date' => Input::post('end_date'),
                    'end_time' => Input::post('end_time.'.$channel),

                );

            }

            if(count($valid_channels) == 0)
            {
                $errors[] = 'Bitte wähle min. 1 Sender aus.';
            }
            
            if(count($errors) == 0)
            {

                foreach($valid_channels as $channel)
                {
                    
                    $cs = new Model_Channel_Switch;
                    $cs->channel_id = $channel['channel_id'];
                    $cs->switch = 'on';
                    $cs->date = strtotime("{$channel['start_date']} {$channel['start_time']}");
                    $cs->save();

                    $cs = new Model_Channel_Switch;
                    $cs->channel_id = $channel['channel_id'];
                    $cs->switch = 'off';
                    $cs->date = strtotime("{$channel['end_date']} {$channel['end_time']}");
                    $cs->save();

                }

                Messages::success('Schalter wurden konfiguriert');
                Messages::redirect('ghru4pxsa3/templates');

            }
            else
            {

                Messages::error(implode("<br />", $errors));
                Messages::redirect(Uri::current());

            }

        }


        $data['template'] = $template;
        
        $data['channels'] = array();


        $channels = array();
        $channels = array_map(function($channel) use($channels){
            $result = Model_Channel::query()->where('id', $channel['channel_id'])->get_one();
            $result->attributes = $channel;
            return $result;
        }, json_decode($template->channels, true));
        
        foreach($channels as $channel)
        {
            $data['channels'][] = $channel;
        }
          
        return Response::forge(View::forge('ghru4pxsa3/templates/setup.html.twig', isset($data) ? $data : array(), false)); 
    
    }

/**
     *
     */
    public function post_delete($template_id)
    {



        $template = Model_Template::query()->where('id', $template_id)->get_one();
        if(!$template)
        {
            throw new NotFoundException;
        }

        $template->delete();

        \Messages::success('Template wurde gelöscht!');
        \Messages::redirect('ghru4pxsa3/templates');

    }
    
    
}
