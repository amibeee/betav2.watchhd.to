<?php
class Controller_Base extends Controller
{

    /**
     *
     *
     *
     */
	public $user = null;


    /**
     *
     *
     *
     */
    public function before()
	{

        parent::before();

        /**
         *
         *  apc workaround :|
         *
         */
        if(Fuel::$env === Fuel::DEVELOPMENT && (extension_loaded('apc') && ini_get('apc.enabled')))
        {

            apc_clear_cache('user');
            apc_clear_cache();

        }
		
		if(Uri::segment(1) != 'ghru4pxsa3' && @file_get_contents(DOCROOT.'maintenance.dat') == '1'){
			Response::redirect('work_index.html');
		}

        /**
         *
         * User Array ...
         *
         */
        $this->user = array(
            'is_authenticated' => \Auth::check(),
            'is_admin' => (\Auth::get('group') >= 100 ? true : false),
            'is_user' => (\Auth::get('group') > 0 ? true : false),
            'is_affiliate' => (\Auth::get('group') >= 25 ? true : false),
            'is_mod' => (\Auth::get('group') >= 75 ? true : false),
            'uid' => isset(\Auth::get_user_id()[1]) ? \Auth::get_user_id()[1] : 0,
            'username' => (\Auth::check() ? \Auth::get_screen_name() : 'Anonymus (Gast)'),
            'email' => \Auth::get_email(),
            'api_access_key' => Auth::get('api_access_key'),
            'tokens' => Auth::get('tokens'),
            'group' => Auth::get('group'),
            'suspended' => Auth::get('suspended'),
            'is_premium' => ((Auth::get('premium_until') > time() || \Auth::get('group') >= 75) ? true : false),
            'premium_until' => Auth::get('premium_until'),
            'account_balance' => Auth::get('account_balance'),
            'pin' => Auth::get('pin'),
            'line_password' => Auth::get('line_password'),
			'force_password_reset' => Auth::get('force_password_reset'),
            'two_fa_verified' => Auth::get('two_fa_verified'),
            'two_fa_enabled' => Auth::get('two_fa_enabled')


        );
        View::set_global('current_user', $this->user, false);

        /**
         *
         * Affiliate Tracking
         *
         */
        if(Input::get('aid', '') != '')
        {

            if($affiliate = Model_User::query()->where('affiliate_tracking_id', '=', Input::get('aid'))->get_one())
            {
                Session::set('affiliate', array($affiliate->id, Input::get('sub_id', '')));
            }

        }

         /**
         *
         * prepare status messages ...
         *
         */
        $messages = array();
		foreach(array('error', 'warning', 'success', 'info') as $type)
		{

            foreach(\Messages::instance()->get($type) as $message)
			{

             $message['type'] = $message['type'] == 'error' ? 'danger' : $message['type'];

			 array_push(
                    $messages,
                    '<div class="alert alert-'.trim($message['type']).'">',
                    $message['body'],
                    '<a href="#" class="x" data-dismiss="alert">&times;</a></div>'
                );

            }

        }
        if($this->user['suspended'])
        {
            \Auth::logout();
            \Messages::error('Dein Account wurde gesperrt. Bitte kontaktiere den Support.');
            Response::redirect('login');
        }
        if ($this->user['is_authenticated'] && !$this->user['two_fa_verified']) {
            if ($this->user['is_admin']) {
                \Log::info('Admin user bypassed 2FA verification.');
            } else if ($this->user['two_fa_enabled']) {
                // If 2FA is enabled, enforce verification
                if (Uri::segment(1) != 'verify_2fa' && Uri::segment(1) != 'ghru4pxsa3') {
                    \Auth::logout(); // Log out the user
                    \Messages::warning('Bitte vervollständige die Zwei-Faktor-Authentifizierung, um fortzufahren.');
                    Response::redirect('login');
                } else {
                    \Log::debug('Currently on 2FA verification page.');
                }
            } else {
                // 2FA is not enabled, allow access
                \Log::debug('User authenticated, 2FA is not enabled.');
            }
        }
        if ($this->user['is_authenticated'] && ($this->user['two_fa_verified'] || $this->user['is_admin'])) {
            if ((Uri::segment(1) == 'verify_2fa') || (Uri::segment(1) == 'login')) {
                \Messages::info('Du bist bereits authentifiziert.');
                Response::redirect('landing');
            } else {
                \Log::debug('User authenticated and verified or admin, continuing.');
            }
        }

		if(!in_array(Uri::segment(1), array('login', 'settings')) && ($this->user['is_user'] && $this->user['force_password_reset']))
		{
			Messages::warning('Um die Seite weiter nutzten zu können musst du dein Passwort ändern.');
			Messages::redirect('settings');
		}
	
       
        View::set_global('messages', $messages, false);
		\Messages::reset();

        View::set_global('premium_users', Model_User::query()->where('premium_until', '>', time())->count(), false);
		View::set_global('premium_vod_users', Model_User_Packet::query()->where('bouquet_id', 31)->where('booked_until', '>', time())->count(), false);
	
		if(Uri::segment(1) != 'backend' && Uri::segment(1) != 'callback' && Uri::segment(1) != 'page' && !Cookie::get('page-announcement')){
			#Response::redirect('page/announcement');
		}
		
		if(file_get_contents(DOCROOT.'paysafecard_allowed.dat') == '1'){
			$this->paysafecard_allowed = 1;
		}
		else{
			$this->paysafecard_allowed = 0;
		}
		
		if(file_get_contents(DOCROOT.'amazon_allowed.dat') == '1'){
			$this->amazon_allowed = 1;
		}
		else{
			$this->amazon_allowed = 0;
		}
		
		View::set_global('paysafecard_allowed', $this->paysafecard_allowed, false);
		View::set_global('amazon_allowed', $this->amazon_allowed, false);
	
    }

    /**
     *
     *
     *
     */
    public function router($method, $params)
    {

        $controller_method = strtolower(\Input::method()) . '_' . $method;

        if(!method_exists($this, $controller_method))
        {
            $controller_method = 'action_'.$method;
        }

        if(method_exists($this, $controller_method))
        {
            return call_user_func_array(array($this, $controller_method), $params);
        }
        else
        {
            throw new \HttpNotFoundException();
        }

    }

    /**
     *
     *
     *
     */
	public function after($response)
	{
		parent::after($response);
		return $response;
	}

}

