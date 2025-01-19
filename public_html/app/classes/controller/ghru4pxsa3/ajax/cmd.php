<?php
class Controller_Ghru4pxsa3_Ajax_Cmd extends Controller_Base_Ajax
{

    /**
     *
     */
     public function post_index()
     {

        if(!$this->user['is_admin'])
        {
            return $this->response(array('success' => false, 'message' => 'Du bist nicht berechtigt diese Aktion auszuführen.'));
        }

        switch(Input::post('command'))
        {

            case 'restart':

            $channel_id = (int) Input::post('channel_id', '');

            $channel = Model_Channel::query()->where('id', '=', $channel_id)->get_one();
            if(!$channel)
            {
                return $this->response(array('success' => false, 'message' => 'Diese Kanal existiert nicht oder nicht mehr.'));
            }

            $contents = file_get_contents( "http://iptv.watchhd.cc:8000/api.php?action=stream&sub=start&stream_ids[]=".$channel->url );
            
            $result = json_decode( $contents, true );

            if(json_last_error() != JSON_ERROR_NONE)
            {
                return $this->response(array('success' => false, 'message' => 'Unerwartete Antwort vom Server.'));
            }

            if($result['result'] == true)
            {
                return $this->response(array('success' => true, 'message' => 'Der Kanal wird neu gestartet.'));
            
            }
            else
            {
                return $this->response(array('success' => false, 'message' => 'Der Kanal konnte nicht neugestartet werden.'));
            
            }

            break;

        }

     }

}