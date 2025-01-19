<?php
class Controller_api_v1_Getstreamurl extends Controller_Base_Api
{
    
    /**
     * 
     */
    public function action_index()
    {
        
        $channel_id = Input::get('id', '');
        $token = Input::get('token', '');
        
        $channel = Model_Channel::query()->where('id', '=', $channel_id)->get_one();
        if(!$channel) throw new HttpNotFoundException;
        
        $password_token = (Model_User::query()->where(DB::expr('SHA1(CONCAT(username, password))'), '=', $token)->and_where_open()->where('premium_until', '>', time())->or_where('group', '>', 75)->and_where_close()->count() ? true : false);
        $token_alias = false;
        
        $alias = Model_Koditokenalias::query()->where('alias', '=', $token)->get_one();
        if($alias)
        {
            
            $user = Model_User::query()->where('id', '=', $alias->user_id)->and_where_open()->where('premium_until', '>', time())->or_where('group', '>', 75)->and_where_close()->get_one();
            if($user) $token_alias = true;
            
        }
        
        if(!$password_token && !$token_alias)
        {
            throw new HttpNoAccessException;
        }   
         
        $url = parse_url($channel->url);
        $url['host'] = isset($url['host']) ? $url['host'] : '';
        $url['port'] = isset($url['port']) ? $url['port'] : '';
        $url['path'] = isset($url['path']) ? $url['path'] : '';
        
        $mysecretkey = "mysecretkey";
        $addr = isset($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : Input::ip();
        $expiry = strtotime("+1 hour");
        $b64 = base64_encode(md5($mysecretkey.ltrim($url['path'], '/').$addr.$expiry,true));
        $b64u = rtrim(str_replace(array('+','/'),array('-','_'),$b64),'=');
        
        return $this->response(array('success' => true, 'message' => '', 'data' => array('url' => "rtmp://{$url['host']}:{$url['port']}{$url['path']}?e=$expiry&st=$b64u")));
        
    }

}