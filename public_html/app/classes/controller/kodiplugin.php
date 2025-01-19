<?php
class Controller_Kodiplugin extends Controller_Base_User
{
    
    /**
     * 
     */
    public function action_index()
    {
        
        if(Input::method() == 'POST')
        {
            
            $alias = trim(Input::post('alias', ''));
            
            if(empty($alias))
            {
                
                
                \Messages::error(i18n::t('Bitte gebe dein Kodi Token Alias angeben.'));
                \Messages::redirect(Uri::current());
                
            }
            
            if(preg_match('/\s/', $alias))
            {
                
                
                \Messages::error(i18n::t('Leerzeichen sind im Alias nicht erlaubt.'));
                \Messages::redirect(Uri::current());
                
            }
            
            if(mb_strlen($alias) < 8)
            {
              
                \Messages::error(i18n::t('Alias zu kurz.'));
                \Messages::redirect(Uri::current());
                
            }
            
            $token_alias = Model_Koditokenalias::query()->where('user_id', '=', $this->user['uid'])->get_one();
            if(!$token_alias)
            {
                $token_alias = new Model_Koditokenalias;
                $token_alias->user_id = $this->user['uid'];
            } 
            $token_alias->alias = $alias;
            $token_alias->save();
            
            \Messages::success('Kodi Token Alias wurde gespeichert.');
            \Messages::redirect(Uri::current());
        }
        
        $user = Model_User::query()->where('id', '=', $this->user['uid'])->get_one();
        $data['token'] = sha1($user->username.$user->password);
        
        $data['token_alias'] = '';
        if($token_alias = Model_Koditokenalias::query()->where('user_id', '=', $this->user['uid'])->get_one())
        {
            $data['token_alias'] = $token_alias->alias;
        }
        
        return Response::forge(View::forge('kodiplugin.html.twig', isset($data) ? $data : array(), false)); 
    
    }
    
}