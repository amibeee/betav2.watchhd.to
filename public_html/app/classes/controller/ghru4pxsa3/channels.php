<?php
class Controller_Ghru4pxsa3_Channels extends Controller_Base_Admin
{

    /**
     *
     */
public function action_index()
{
    if (Input::method() == 'POST')
    {
        $action = Input::post('action');
        $channel_ids = Input::post('channel_ids', array());

        if ($action && !empty($channel_ids))
        {
            foreach ($channel_ids as $id)
            {
                $channel = Model_Channel::query()->where('id', '=', $id)->get_one();
                if ($channel)
                {
                    switch ($action)
                    {
                        case 'activate':
                            $channel->active = 1;
                            $channel->save();
                            break;

                        case 'deactivate':
                            $channel->active = 0;
                            $channel->save();
                            break;
                    }
                }
            }

            \Messages::success('Aktion wurde ausgeführt.');
        }
        else
        {
            \Messages::error('Bitte wählen Sie eine Aktion und mindestens einen Kanal aus.');
        }

        \Messages::redirect('ghru4pxsa3/channels');
    }

    $query = Model_Channel::query();
    if (Input::get('query', '') != '')
    {
        $query->where('name', 'LIKE', '%' . Input::get('query') . '%');
    }

    $pagination_config = array(
        'name'              => 'bootstrap3',
        'pagination_url'    => \Uri::base() . 'ghru4pxsa3/channels?query=' . Input::get('query', ''),
        'per_page'          => 100,
        'total_items'       => $query->count(),
        'num_links'         => 4,
        'uri_segment'       => 3,
        'show_first'        => true,
        'show_last'         => true,
    );

    $pagination = Pagination::forge('', $pagination_config);

    $per_page   = $pagination->per_page;
    $offset     = $pagination->offset;

    $query->order_by('name', 'DESC');
    $query->offset($offset);
    $query->limit($pagination->per_page);

    $data['pagination'] = Pagination::instance('')->render();
    $data['channels'] = array();
    foreach ($query->get() as $channel)
    {
        $channel->viewers = (int)count(DB::select('user_id')
            ->from('channel_heartbeats')
            ->distinct(true)
            ->where('channel_id', '=', $channel->id)
            ->where('created_at', 'BETWEEN', array((time()-300), time()))
            ->execute());       

        $data['channels'][] = $channel;
    }

    return Response::forge(View::forge('ghru4pxsa3/channels/list.html.twig', isset($data) ? $data : array(), false));
}

    /**
     *
     */
     public function action_schedules()
     {
         return Response::forge(View::forge('ghru4pxsa3/channels/schedules.html.twig', isset($data) ? $data : array(), false));
     }

    /**
     *
     */
    public function post_activate($channel_id)
    {



        $channel = Model_Channel::query()->where('id', '=', $channel_id)->get_one();
        if(!$channel) throw new HttpNotFoundException;

        $channel->active = 1;
        $channel->save();

        \Messages::success('Kanal wurde aktiviert.');
        \Messages::redirect('ghru4pxsa3/channels');

    }

    /**
     *
     */
    public function post_deactivate($channel_id)
    {



        $channel = Model_Channel::query()->where('id', '=', $channel_id)->get_one();
        if(!$channel) throw new HttpNotFoundException;

        $channel->active = 0;
        $channel->save();

        \Messages::success('Kanal wurde deaktiviert.');
        \Messages::redirect('ghru4pxsa3/channels');

    }

    /**
     *
     */
    public function action_removemoderator($user_id, $channel_id)
    {

        $channel = Model_Channel::query()->where('id', '=', $channel_id)->get_one();
        if(!$channel) throw new HttpNotFoundException;

        $channel_moderator = Model_Channel_Moderator::query()->where('channel_id', '=', $channel_id)->where('user_id', '=', $user_id)->get_one();
        if(!$channel_moderator) throw new HttpNotFoundException;

        $channel_moderator->delete();

        \Messages::success('Der Benutzer ist nun kein Kanalmoderator mehr.');
        \Messages::redirect('ghru4pxsa3/channel/'.$channel_id.'/moderators');

    }

    /**
     *
     */
    public function action_moderators($channel_id)
    {

        $channel = Model_Channel::query()->where('id', '=', $channel_id)->get_one();
        if(!$channel) throw new HttpNotFoundException;

        if(Input::method() == 'POST')
        {

            $user_id = Input::post('user_id', '');

            if(Model_User::query()->where('id', '=', $user_id)->count() == 0)
            {
                \Messages::error('Dieser Benutzer existiert nicht.');
                \Messages::redirect(Uri::current());
            }

            if(Model_Channel_Moderator::query()->where('channel_id', '=', $channel_id)->where('user_id', '=', $user_id)->count())
            {
                \Messages::error('Dieser Benutzer ist bereits Kanalmoderator.');
                \Messages::redirect(Uri::current());
            }

            $channel_moderator = new Model_Channel_Moderator;
            $channel_moderator->user_id = $user_id;
            $channel_moderator->channel_id = $channel_id;
            $channel_moderator->save();

            \Messages::success('Benutzer wurde zum Kanalmoderator befördert.');
            \Messages::redirect(Uri::current());

        }

        $query = DB::select('users.*')->from('users');
        $query->where(DB::expr('(IF((SELECT COUNT(channel_moderators.user_id) FROM channel_moderators WHERE channel_moderators.user_id = users.id AND channel_moderators.channel_id = '.$channel_id.'), 1, 0))'), '=', 1);
        $query->order_by('users.username', 'ASC');

        $data['moderators'] = $query->execute();

        $query = DB::select('users.*')->from('users');
        $query->where(DB::expr('(IF((SELECT COUNT(channel_moderators.user_id) FROM channel_moderators WHERE channel_moderators.user_id = users.id AND channel_moderators.channel_id = '.$channel_id.'), 1, 0))'), '=', 0);
        $query->order_by('users.username', 'ASC');

        $data['users'] = $query->execute();

        $data['channel'] = $channel;

        return Response::forge(View::forge('ghru4pxsa3/channels/moderators.html.twig', isset($data) ? $data : array(), false));

    }

    /**
     *
     */
    public function action_edit($channel_id)
    {

        $channel = Model_Channel::query()->where('id', '=', $channel_id)->get_one();
        if(!$channel) throw new HttpNotFoundException;

        if(Input::method() == 'POST')
        {

            $name = trim(Input::post('name', ''));
            $description = trim(Input::post('description', ''));
            $tvguideapi_id = trim(Input::post('tvguideapi_id', ''));
            $category = trim(Input::post('category', ''));
            $url = trim(Input::post('url', ''));
			$server_id = trim(Input::post('server_id', ''));
            $position = trim(Input::post('position', ''));
            $adult = trim(Input::post('adult', ''));

            $errors = array();

            if(empty($name))
            {
                $errors[] = 'Bitte Kanal Namen angeben.';
            }

            if(!empty($name) && Model_Channel::query()->where('name', '=', $name)->where('id', '!=', $channel_id)->count())
            {
                $errors[] = 'Es existiert bereits ein Kanal mit diesem Namen.';
            }

            if(empty($category))
            {
                $errors[] = 'Bitte Kategorie auswählen.';
            }

            if(!ctype_digit($position))
            {
                $errors[] = 'Bitte Position angeben';
            }
			
			if(!ctype_digit($server_id))
            {
                $errors[] = 'Bitte Server ID angeben';
            }

            if(count($errors) == 0)
            {

                $channel->name = $name;
                $channel->description = $description;
                $channel->tvguideapi_id = $tvguideapi_id;
                $channel->category = $category;
                $channel->url = $url;
                $channel->position = $position;
				$channel->server_id = $server_id;
                $channel->adult = ($adult == '1' ? 1 : 0);
                $channel->save();

                $config = array(
                    'path' => DOCROOT.'assets/img/channellogos',
                    'randomize' => true,
                    'ext_whitelist' => array('img', 'jpg', 'jpeg', 'gif', 'png'),
                );

                Upload::process($config);

                if(Upload::is_valid())
                {

                    $files = Upload::get_files();
                    foreach($files as $key => $file)
                    {

                        if($file['field'] == 'logo')
                        {

                            if(is_file(DOCROOT.$channel->logo))
                            {
                                unlink(DOCROOT.$channel->logo);
                            }

                            Upload::save(DOCROOT.'assets/img/channels/logos', $key);

                            $file = Upload::get_files()[$key];

                            $channel->logo = 'assets/img/channels/logos/'.$file['saved_as'];
                            $channel->save();

                        }

                        if($file['field'] == 'player')
                        {

                            if(is_file(DOCROOT.$channel->player_cover))
                            {
                                unlink(DOCROOT.$channel->player_cover);
                            }

                            Upload::save(DOCROOT.'assets/img/channels/player_backgrounds', $key);

                            $file = Upload::get_files()[$key];

                            $channel->player_cover = 'assets/img/channels/player_backgrounds/'.$file['saved_as'];
                            $channel->save();

                        }

                    }


                }

                \Messages::success('Kanal Informationen wurden aktualisiert.');
                \Messages::redirect('ghru4pxsa3/channels');

            }
            else
            {

                Session::set_flash('input.old', Input::post());
                \Messages::error(implode('<br />', $errors));
                \Messages::redirect(Uri::current());

            }

        }

        $data['channel'] = $channel;

        return Response::forge(View::forge('ghru4pxsa3/channels/edit.html.twig', isset($data) ? $data : array(), false));

    }


    /**
     *
     */
    public function action_create()
    {

        if(Input::method() == 'POST')
        {

            $name = trim(Input::post('name', ''));
            $description = trim(Input::post('description', ''));
            $tvguideapi_id = trim(Input::post('tvguideapi_id', ''));
            $category = trim(Input::post('category', ''));
            $url = trim(Input::post('url', ''));
            $position = trim(Input::post('position', ''));
			$server_id = trim(Input::post('server_id', ''));
            $adult = trim(Input::post('adult', ''));
            

            $errors = array();

            if(empty($name))
            {
                $errors[] = 'Bitte Kanal Namen angeben.';
            }

            if(!empty($name) && Model_Channel::query()->where('name', '=', $name)->count())
            {
                $errors[] = 'Es existiert bereits ein Kanal mit diesem Namen.';
            }

            if(empty($category))
            {
                $errors[] = 'Bitte Kategorie auswählen.';
            }

            if(!ctype_digit($position))
            {
                $errors[] = 'Bitte Position angeben';
            }
			
			if(!ctype_digit($server_id))
            {
                $errors[] = 'Bitte Server ID angeben';
            }

            if(count($errors) == 0)
            {

                $channel = new Model_Channel;
                do{
                    $channel->public_id = Str::random('uuid');
                }
                while(Model_Channel::query()->where('public_id', '=', $channel->public_id)->count());
                $channel->name = $name;
                $channel->description = $description;
                $channel->tvguideapi_id = $tvguideapi_id;
                $channel->active = 1;
                $channel->url = $url;
                $channel->logo = '';
                $channel->player_cover = '';
                $channel->category = $category;
                $channel->position = $position;
                $channel->adult = ($adult == '1' ? 1 : 0);
				$channel->server_id = $server_id;
                $channel->save();

                $config = array(
                    'path' => DOCROOT.'assets/img/channellogos',
                    'randomize' => true,
                    'ext_whitelist' => array('img', 'jpg', 'jpeg', 'gif', 'png'),
                );

                Upload::process($config);

                if(Upload::is_valid())
                {

                    $files = Upload::get_files();
                    foreach($files as $key => $file)
                    {

                        if($file['field'] == 'logo')
                        {

                            if(is_file(DOCROOT.$channel->logo))
                            {
                                unlink(DOCROOT.$channel->logo);
                            }

                            Upload::save(DOCROOT.'assets/img/channels/logos', $key);

                            $file = Upload::get_files()[$key];

                            $channel->logo = 'assets/img/channels/logos/'.$file['saved_as'];
                            $channel->save();

                        }

                        if($file['field'] == 'player')
                        {

                            if(is_file(DOCROOT.$channel->player_cover))
                            {
                                unlink(DOCROOT.$channel->player_cover);
                            }

                            Upload::save(DOCROOT.'assets/img/channels/player_backgrounds', $key);

                            $file = Upload::get_files()[$key];

                            $channel->player_cover = 'assets/img/channels/player_backgrounds/'.$file['saved_as'];
                            $channel->save();

                        }

                    }


                }

                \Messages::success('Kanal wurde hinzugefügt.');
                \Messages::redirect('ghru4pxsa3/channels');

            }
            else
            {

                Session::set_flash('input.old', Input::post());
                \Messages::error(implode('<br />', $errors));
                \Messages::redirect(Uri::current());

            }

        }

        return Response::forge(View::forge('ghru4pxsa3/channels/create.html.twig', isset($data) ? $data : array(), false));

    }

    /**
     *
     */
    public function action_stats($channel_id)
    {

        $channel = Model_Channel::query()->where('id', '=', $channel_id)->get_one();
        if(!$channel) throw new HttpNotFoundException;

        $_month = Input::get('month', date('m-Y'));

        $month = explode('-', $_month)[0];
        $year = explode('-', $_month)[1];

        $data['points'] = array();

        for($i = 1; $i <= date('t', strtotime("01.{$month}.{$year} 00:00:00")); $i++)
        {

            $timestamp = strtotime("{$i}.{$month}.{$year} 00:00:00");

            /* Get Peek Hour */
            $query = DB::select(
                array(DB::expr('COUNT(user_id)'), 'peek'),
                array(DB::expr('HOUR(FROM_UNIXTIME(created_at))'), 'hour')
            )->from('channel_heartbeats');
            $query->where(DB::expr('DAY(FROM_UNIXTIME(created_at))'), '=', date("d", $timestamp));
            $query->where(DB::expr('MONTH(FROM_UNIXTIME(created_at))'), '=', date("m", $timestamp));
            $query->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date("Y", $timestamp));

            $query->where('channel_heartbeats.channel_id', '=', $channel_id);
            $query->group_by(DB::expr('HOUR(FROM_UNIXTIME(created_at))'));
            $query->group_by(DB::expr('DAY(FROM_UNIXTIME(created_at))'));
            $query->group_by(DB::expr('MONTH(FROM_UNIXTIME(created_at))'));
            $query->group_by(DB::expr('YEAR(FROM_UNIXTIME(created_at))'));
            $query->group_by('user_id');

            $peek = $query->execute()[0]['peek'].' ('.$query->execute()[0]['hour'].' Uhr)';
            /* GET Peek Hour */

            $data['points'][date('d.m.Y', $timestamp)] = array(
                'total' => (int)Model_Channel_Heartbeat::query()->where('channel_id', '=', $channel_id)->where(DB::expr('DAY(FROM_UNIXTIME(created_at))'), '=', date("d", $timestamp))->where(DB::expr('MONTH(FROM_UNIXTIME(created_at))'), '=', date("m", $timestamp))->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date("Y", $timestamp))->group_by('user_id')->count(),
                'peek' => $peek
            );

        }

        $data['channel'] = $channel;

        return Response::forge(View::forge('ghru4pxsa3/channels/stats.html.twig', isset($data) ? $data : array(), false));

    }

    /**
     *
     */
    public function post_delete($channel_id)
    {


        $channel = Model_Channel::query()->where('id', '=', $channel_id)->get_one();
        if(!$channel) throw new HttpNotFoundException;

        DB::delete('channel_heartbeats')->where('channel_id', '=', $channel_id)->execute();

        if(is_file(DOCROOT.$channel->cover))
        {
            unlink(DOCROOT.$channel->cover);
        }

        $channel->delete();

        \Messages::success('Kanal wurde deaktiviert.');
        \Messages::redirect('ghru4pxsa3/channels');

    }
    

}
