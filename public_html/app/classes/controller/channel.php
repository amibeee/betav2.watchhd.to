    <?php
    class Controller_Channel extends Controller_Base_Public
    {
     
        /**
         * 
         */
        public function action_index($public_id = '')
        {
     
            if(!$this->user['is_premium'])
            {
                \Messages::error(i18n::t('Für diese Funktion benötigst du einen Premium Account.'));
                \Messages::redirect('buy/premium');
            }
     
            $channel = Model_Channel::query()->where('public_id', '=', $public_id)->get_one();
            if(!$channel) throw new HttpNotFoundException;
     
    	if($channel->adult && !empty($this->user['pin']) && !Session::get('pin.'.$channel->id, false)) 
            {
     
                if(Input::method() == 'POST')
                {
     
                    $pin = trim(Input::post('pin', ''));
                    if($pin != $this->user['pin'])
                    {
     
                        \Messages::error('PIN ungültig');
                        \Messages::redirect(Uri::current());
     
                    }
     
                    Session::set('pin.'.$channel->id, true);
                    Response::redirect(Uri::current());
     
                }
     
                return Response::forge(View::forge('pin.html.twig', isset($data) ? $data : array(), false));
     
            }
     
            $channel->viewers = (int)count(DB::select(
               'user_id'
             )->from('channel_heartbeats')
             ->distinct(true)
             ->where('channel_id', '=', $channel->id)
             ->where('created_at', 'BETWEEN', array( (time()-300), time() ))
             ->execute());   
     
            $url = parse_url($channel->url);
            $url['host'] = isset($url['host']) ? $url['host'] : '';
            $url['port'] = isset($url['port']) ? $url['port'] : '';
            $url['path'] = isset($url['path']) ? $url['path'] : '';
     
            $mysecretkey = "mysecretkey";
            $addr = Input::real_ip();
            $expiry = strtotime("+1 hour");
            $b64 = base64_encode(md5($mysecretkey.ltrim($url['path'], '/').$addr.$expiry,true));
            $b64u = rtrim(str_replace(array('+','/'),array('-','_'),$b64),'=');
     
            View::set_global('stream_url', "rtmp://{$url['host']}:{$url['port']}{$url['path']}?e=$expiry&st=$b64u", false);
     
            #$channel->user_is_channel_moderator = false;
            #if(Model_Channel_Moderator::query()->where('channel_id', '=', $channel->id)->where('user_id', '=', $this->user['uid'])->count() || $this->user['is_admin']) $channel->user_is_channel_moderator = true;
     
            $data['channel'] = $channel;
     
     
            $smilies = array();
            foreach(glob(DOCROOT.'assets/img/smilies/*.png') as $smilie)
            {
     
                $identifier = str_replace('.png', '', basename($smilie));
     
                $smilies[] = array(
                    'url' => Uri::base().'assets/img/smilies/'.basename($smilie),
                    'identifier' => ":{$identifier}:"
                );
     
     
            }
     
            $data['smilies'] = $smilies;
            $data['channels'] = Model_Channel::query()->where('active', '=', 1)->order_by('name', 'ASC')->get();
     
    	$user = Model_User::query()->where('id', '=', $this->user['uid'])->get_one();
    	$arrContextOptions=array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );  
     
    	$contents = file_get_contents('https://iptv.watchhd.cc:25463/get.php?username='.$this->user['username'].'&password='.$user->line_password.'&type=m3u&output=m3u8', false, stream_context_create($arrContextOptions));
    	$lines = explode("\n", $contents);
     
    	$found = false;	
     
    	foreach($lines as $line)
    	{
     
    		if(preg_match("@\/".$channel->url."\..*@", $line))
    		{
    			$found = true;
    			$data['stream_url'] = trim($line);
    		}		
     
    	}
    	echo "<!-- {$contents} -->";
     
    /*
    	if(!$found)
    	{
    		throw new HttpNotFoundException;
    	}
    */
            return Response::forge(View::forge('channel.html.twig', isset($data) ? $data : array(), false)); 
     
        }
     
    }