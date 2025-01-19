<?php

class Controller_Landing extends Controller_Base_User
{
    public function action_index()
    {
        // Check if the user is logged in
        if (!Auth::check())
        {
            Response::redirect('login');
        }

        // Get the user ID
        $user_data = Auth::get_user_id();

        if (!$user_data)
        {
            // Log this unexpected situation
            \Log::error('User data not found for authenticated session.');
            
            // Clear the auth session and redirect to login
            Auth::logout();
            Response::redirect('login');
        }

        // Extract the user ID from the user data
        $user_id = is_array($user_data) ? $user_data[1] : $user_data;

        // Fetch the user from the database
        $user = Model_User::find($user_id);

        if (!$user)
        {
            // Log this unexpected situation
            \Log::error('User not found in database for authenticated session. User ID: ' . $user_id);
            
            // Clear the auth session and redirect to login
            Auth::logout();
            Response::redirect('login');
        }

        // Check if 2FA is verified (assuming you have this field in your user model)
        if (property_exists($user, 'two_fa_verified') && !$user->two_fa_verified)
        {
            // If 2FA is not verified, redirect to 2FA verification page
            Response::redirect('verify_2fa');
        }

        $data = array(
            'user' => $user,
            'welcome_message' => Session::get_flash('welcome_message')
        );

        // If there's no specific welcome message, create a default one
        if (!$data['welcome_message']) {
            $data['welcome_message'] = "Welcome back, {$user->username}!";
        }

        // Render the landing page with data
        return Response::forge(View::forge('landing.html.twig', $data));
    }
}