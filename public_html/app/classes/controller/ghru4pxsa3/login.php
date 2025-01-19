<?php
class Controller_Ghru4pxsa3_Login extends Controller_Base_Public 
{
    /**
     * Handle login action
     */     
    public function action_index()
    {
        if(Input::method() == 'POST')
        {
            $username = trim(Input::post('username', ''));
            $password = trim(Input::post('password', ''));

            // Verify CSRF token
            if(!Security::check_token())
            {
                Session::set('input.old', Input::post());
                Messages::warning('Invalid Login Token. Please refresh the page and try again.');
                Messages::redirect(Uri::current());
                return;
            }

            // Check for empty credentials
            if(empty($username) || empty($password))
            {
                Session::set_flash('input.old', Input::post());
                Messages::warning('Please enter your username and your password.');
                Messages::redirect(Uri::current());
                return;
            }

            // Find user and check credentials
            $user = Model_User::query()
                ->where('username', '=', $username)
                ->where('password', '=', Auth::instance()->hash_password($password))
                ->where('group', '>=', 75)
                ->get_one();

            // Check if user exists and credentials are valid
            if(!$user)
            {
                Session::set_flash('input.old', Input::post());
                Messages::error('Invalid Login credentials.');
                Messages::redirect(Uri::current());
                return;
            }

            // Check if user is suspended


            // Login successful - set session and redirect
            try {
                Auth::force_login($user->id);
                Auth::remember_me();

                // Update last login timestamp
                $user->last_login = time();
                $user->save();

                Response::redirect('ghru4pxsa3/dashboard');
            } catch (Exception $e) {
                Log::error('Login failed for user ' . $username . ': ' . $e->getMessage());
                Messages::error('An error occurred during login. Please try again.');
                Messages::redirect(Uri::current());
            }
        }

        return Response::forge(View::forge('ghru4pxsa3/login.html.twig', isset($data) ? $data : array(), false));
    }
}