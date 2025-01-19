<?php

class Controller_Verify2FA extends Controller_Base_Public
{
    public function action_index()
    {
        if (Input::method() == 'POST')
        {
            $user_id = Session::get('2fa_user_id');
            if (!$user_id)
            {
                \Messages::error(i18n::t('Session expired or 2FA user ID not found.'));
                \Log::warning('2FA user ID not found in session.');
                return Response::redirect('login');
            }

            $entered_token = trim(Input::post('token', ''));
            if (empty($entered_token))
            {
                \Messages::error(i18n::t('Please enter your 2FA token.'));
                return Response::redirect('verify_2fa');
            }

            $user = Model_User::find($user_id);
            if (!$user)
            {
                \Messages::error(i18n::t('User not found.'));
                \Log::warning("User with ID {$user_id} not found.");
                return Response::redirect('login');
            }

            if ($user->two_fa_token === $entered_token && time() <= $user->token_expiration)
            {
                $user->two_fa_token = null;
                $user->token_expiration = null;
                $user->two_fa_verified = true; // Mark 2FA as verified
                $user->save();

                Auth::force_login($user->id);
                Auth::remember_me();

                \Log::info("User ID {$user->id} successfully verified 2FA and logged in.");

                \Messages::success(i18n::t('2FA token verified successfully!'));

                return Response::redirect('/landing');
            }
            else
            {
                \Messages::error(i18n::t('Invalid or expired 2FA token.'));
                \Log::warning("Invalid or expired 2FA token entered for user ID {$user->id}.");

                return Response::redirect('verify_2fa');
            }
        }

        return Response::forge(View::forge('verify_2fa.html.twig'));
    }
}