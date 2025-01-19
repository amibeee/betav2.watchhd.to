<?php

class Controller_Login extends Controller_Base_Public
{
    public function action_index()
    {
        if (Input::method() == 'POST')
        {
            $username = trim(Input::post('username', ''));
            $password = trim(Input::post('password', ''));

            // Check the CSRF token
            if (!Security::check_token())
            {
                $this->handle_invalid_form('Invalid Form Token. Please reload the page and try again.');
            }

            // Basic validation for username and password
            if (empty($username) || empty($password))
            {
                $this->handle_invalid_form('Please provide your username and password.');
            }

            // Authenticate user
            $user = $this->authenticate_user($username, $password);
            if (!$user)
            {
                $this->handle_invalid_form('Invalid login credentials.', 'login');
            }
            if($user->suspended == 1)
            {
                Session::set_flash('input.old', Input::post());
                Messages::error('Your account has been suspended. Please contact support for assistance.');
                Messages::redirect(Uri::current());
                return;
            }
            // Check if 2FA is enabled for the user
            if ($user->two_fa_enabled) 
            {
                // Generate and save 2FA token
                $token = $this->generate_2fa_token($user);

                // Attempt to send 2FA token via email
                if (!$this->send_2fa_email($user, $token))
                {
                    $this->handle_invalid_form('Failed to send 2FA token. Please try again later.', 'login');
                }

                // Store user ID in session for 2FA validation
                Session::set('2fa_user_id', $user->id);

                // Redirect to 2FA verification page
                Response::redirect('verify_2fa');
            }
            else 
            {
                // Directly log in the user if 2FA is not enabled
                $this->log_user_in($user);
                Response::redirect('account');
            }
        }

        // Render the login view
        $data['captcha'] = Captcha::forge('recaptcha')->html('recaptcha/2015');
        return Response::forge(View::forge('login.html.twig', isset($data) ? $data : array(), false));
    }

    // Log the user in and set session details
    private function log_user_in($user)
    {
        // Set necessary session data
        Session::set('user_id', $user->id);
        Session::set('username', $user->username);
        Session::set('is_logged_in', true);
    }

    // Handle invalid form submission with a message
    private function handle_invalid_form($message, $redirect_uri = null)
    {
        Session::set_flash('input.old', Input::post());
        \Messages::warning(i18n::t($message));
        Response::redirect($redirect_uri ?: Uri::current());
    }

    // Authenticate user with username and password
    private function authenticate_user($username, $password)
    {
        $auth = Auth::instance();

        // Try to log in
        if ($auth->login($username, $password))
        {
            // If login is successful, retrieve the user model
            return Model_User::query()
                ->where('username', $username)
                ->get_one();
        }

        return null;
    }

    // Generate and save a 2FA token for the user
    private function generate_2fa_token($user)
    {
        $token = bin2hex(random_bytes(16));
        $user->two_fa_token = $token;
        $user->token_expiration = time() + 300; // Token valid for 5 minutes
        $user->two_fa_verified = false;
        $user->save();
        return $token;
    }

    // Send the 2FA token via email with enhanced HTML styling
    private function send_2fa_email($user, $token)
    {
        try {
            $email = \Email::forge();
            $email->from('support@watchhd.to', 'WatchHD Support');
            $email->to($user->email, $user->username);
            $email->subject('Your 2FA Token');
            $email->html_body($this->build_email_body($user->username, $token));
            $email->send();
            return true;
        } catch (Exception $e) {
            \Log::error('Failed to send 2FA token: ' . $e->getMessage());
            return false;
        }
    }

    // Build the HTML body for the 2FA email
    private function build_email_body($username, $token)
    {
        return "
            <html>
                <body style='font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;'>
                    <div style='max-width: 600px; margin: auto; background-color: #ffffff; border: 1px solid #dddddd; border-radius: 8px; padding: 20px;'>
                        <h2 style='color: #333333; text-align: center;'>Hello, {$username}</h2>
                        
                        <p style='font-size: 16px; color: #555555; text-align: center;'>
                            Your Two-Factor Authentication (2FA) token is:
                        </p>
                        
                        <div style='text-align: center; margin: 20px 0;'>
                            <p style='font-size: 32px; color: #111111; font-weight: bold; background-color: #f9f9f9; border: 1px solid #dddddd; border-radius: 5px; padding: 10px; display: inline-block;'>
                                {$token}
                            </p>
                        </div>
                        
                        <p style='font-size: 14px; color: #888888; text-align: center;'>
                            Please enter this token in the 2FA form to complete your authentication.
                        </p>
                        
                        <p style='font-size: 14px; color: #888888; text-align: center;'>
                            If you did not request this, please contact our support team immediately.
                        </p>
                        
                        <hr style='border: none; border-top: 1px solid #eeeeee; margin: 20px 0;'>
                        
                        <p style='font-size: 12px; color: #aaaaaa; text-align: center;'>
                            Best regards,<br>
                            <strong>WatchHD Support Team</strong>
                        </p>
                        
                        <p style='font-size: 12px; color: #aaaaaa; text-align: center;'>
                            <a href='https://watchhd.to/' style='color: #007bff; text-decoration: none;'>Visit our website</a> | 
                            <a href='mailto:support@watchhd.to' style='color: #007bff; text-decoration: none;'>Contact Support</a>
                        </p>
                    </div>
                </body>
            </html>
        ";
    }
}
