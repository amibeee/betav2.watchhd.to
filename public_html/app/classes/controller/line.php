<?php

class Controller_Line extends Controller_Base_User
{
    
    /**
     * 
     */
    public function action_customizer()
    {

        $line = Model_User_Line::query()->where('username', Input::post('line'))->where('user_id', $this->user['uid'])->get_one();
        if(!$line && Input::post('line') != $this->user['username'])
        {
        }
		
		$premium_until = null;
		
		if($line){
			$premium_until = $line->premium_until;
		}
		else{
			$premium_until = $this->user['premium_until'];
		}
		
		if($premium_until < time()){
			Messages::error('Du kannst für abgelaufene Lines keine Playlist erstellen. Bitte verlängere die Laufzeit für die Line um deine Playlist zu konfigurieren.');
			Messages::redirect('account');
		}

        // downloading the line
        if($line)
        {
            $url = "http://iptv.watchhd.cc:5050/get.php?username={$line->username}&password={$line->password}&type=m3u_plus&output=ts";
        }
        else{
            $user = Model_User::query()->where('id', $this->user['uid'])->get_one();
            $url = "http://iptv.watchhd.cc:5050/get.php?username={$this->user['username']}&password={$user->line_password}&type=m3u_plus&output=ts";
        }
        
        $ctx = stream_context_create(array('http'=>
            array(
                'timeout' => 10,  //1200 Seconds is 20 Minutes
            )
        ));

		try{
			$payload = explode(PHP_EOL, file_get_contents($url, false, $ctx));
		}
		catch(Exception $e){
			
			Messages::error('Daten konnten nicht geladen werden. Bitte wende dich an den Support.');
			Messages::redirect('account');
			
		}
		
		
		
        $channels = array();
        foreach($payload as $key => $channel)
        {
      
            #if(preg_match("~#EXTINF:-1,([A-Z]{2}|Swiss|Musik|AT|XXX):(.*)~", $channel, $matches))
				if(preg_match("~tvg\-name=\"([A-Z]{2}|Swiss|Musik|AT|XXX): (.*?)\"~", $channel, $matches))
            {
				
				if($matches[1] != 'Musik')
				{
				
					$channels[] = array(
						'name' => trim($matches[2]),
						'country' => trim($matches[1]),
						'type' => 'channel',
						'line' => $channel."\n".$payload[$key+1]
					);
				
				}
				else
				{
					
					$channels[] = array(
						'name' => trim($matches[2]),
						'country' => trim($matches[1]),
						'type' => 'music',
						'line' => $channel."\n".$payload[$key+1]
					);
					
				}
				
            }
            else{

                if(preg_match("~#EXTINF:-1,(.+)~", $channel, $matches))
            {
                
                $matches[1] = trim($matches[1]);
                if(empty($matches[1])) continue;

                $channels[] = array(
                    'name' => trim($matches[1]),
                    'country' => '',
                    'type' => 'vod',
                    'line' => $channel."\n".$payload[$key+1]
                );

            }

            }
        
        }


		
        // remove non channels
        foreach($channels as $key => $channel)
        {

            if(isset($channel['type']) && $channel['type'] == 'vod') continue;

            if(!in_array($channel['country'], array('DE', 'AT', 'UK', 'Swiss', 'FR', 'ES', 'PT', 'TR', 'US', 'RU', 'XXX', 'IT', 'NL', 'CTV')) && $channel['country'] != 'Musik')
            {
                $channels[$key]['type'] = 'vod';
            }
			elseif($channel['country'] == 'Musik'){
				$channels[$key]['type'] = 'music';
			}
            else
            {
                $channels[$key]['type'] = 'channel';
            }

        }
		
		
		$custom_playlist = Model_Customizeduserplaylists::query()->where('id', Input::post('cpid', ''))->where('user_id', $this->user['uid'])->get_one();
		if($custom_playlist){
			
			$data['version'] = $custom_playlist->version;
			
			$data['pre_selected_channels'] = array();
			
			$lines = explode("\n", $custom_playlist->data);
			foreach($channels as $key => $channel){
				
				foreach($lines as $line){
					
					if(preg_match("~".preg_quote($channel['name'])."~", $line)){
						
						if(preg_match("~(720p|1080p)~", $line) && preg_match("~(720p|1080p)~", $channel['name'])){
							$data['pre_selected_channels'][] = $channel['name'];
						}
						elseif(!preg_match("~(720p|1080p)~", $channel['name']) && !preg_match("~(720p|1080p)~", $line)){
							$data['pre_selected_channels'][] = $channel['name'];
						}
						else{
						
						}
						
					
					}
					
				}
				
			}
			
			$data['pre_selected_channels'] = json_encode($data['pre_selected_channels']);
			
            if(Input::post('action') == 'delete')
            {
                $custom_playlist->delete();
                Messages::success('Playlist wurde gelöscht.');
                Messages::redirect('account');
            }

		}

        if(Input::method() == 'POST')
        {

            $playlist_channels = json_decode(Input::post('channels', array()));
            $playlist_version = Input::post('version', '');

            if(count($playlist_channels) == 0)
            {
                Messages::error('Bitte füge deiner Playlist einen oder mehrere Sender hinzu.');
                Messages::redirect(Uri::current());
            }

            if(empty($playlist_version))
            {
                Messages::error('Bitte gebe einen Bezeichner für die Version an.');
                Messages::redirect(Uri::current());
            }

            if(!$custom_playlist){
				
				$custom_playlist = new Model_Customizeduserplaylists;
				$custom_playlist->public_token = $this->user['uid'].'_'.Str::random('alnum', 5);
				$custom_playlist->user_id = $this->user['uid'];

				if($line){
					$custom_playlist->username = $line->username;
				}
				else{
					$custom_playlist->username = $this->user['username'];
				}
			
			}
			
			$custom_playlist->data = '';
            foreach($playlist_channels as $key => $playlist_channel)
            {

            
                if(isset($channels[$playlist_channel]))
                {
					$custom_playlist->data .= $channels[$playlist_channel]['line']."\n";
                }

            }
            $custom_playlist->version = $playlist_version;
            $custom_playlist->save();
            
			$path = APPPATH.'tmp/'.$custom_playlist->id.'.m3u';
			file_put_contents($path , $custom_playlist->data);
			
			File::download($path, 'playlist.m3u');
			
			Messages::success('Playlist wurde generiert. Download wird gestartet.');
            Messages::redirect('account');
			
        }

        $data['channels'] = $channels;
		
		
        return Response::forge(View::forge('playlistconfig.html.twig', isset($data) ? $data : array(), false)); 
		
    }

    public function action_packages()
    {

        $line = Model_User_Line::query()->where('username', Input::post('line'))->where('user_id', $this->user['uid'])->get_one();
        if(!$line && Input::post('line') != $this->user['username'])
        {
           print_r($line);
        }

        $line_id = $line ? $line->id : $this->user['uid'];
		$user_id = $line ? $line->user_id : $this->user['uid'];
	

        $data['packages'] = Model_User_Packet::query()->where('user_id', $user_id)->where('line_id', $line_id)->order_by('name', 'asc')->get();

        return Response::forge(View::forge('packages.html.twig', isset($data) ? $data : array(), false)); 

    }

}