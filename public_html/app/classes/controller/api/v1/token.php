<?php
class Controller_api_v1_Token extends Controller_Base_Api
{
    
    /**
     * 
     */
    public function action_index()
    {
        
        $token = Input::post('token', '');
        if(mb_strlen($token) == 0) return $this->response(array('valid' => 'false'));
        
        $password_token = (Model_User::query()->where(DB::expr('SHA1(CONCAT(username, password))'), '=', $token)->and_where_open()->where('premium_until', '>', time())->or_where('group', '>', 75)->and_where_close()->count() ? true : false);
        $token_alias = false;
        
        $alias = Model_Koditokenalias::query()->where('alias', '=', $token)->get_one();
        if($alias)
        {
            
            $user = Model_User::query()->where('id', '=', $alias->user_id)->and_where_open()->where('premium_until', '>', time())->or_where('group', '>', 75)->and_where_close()->get_one();
            if($user) $token_alias = true;
            
        }
        
        if($password_token || $token_alias)
        {
            return $this->response(array('valid' => 'true'));
        }   
        else
        {
            return $this->response(array('valid' => 'false'));
        }  
        
    }

}