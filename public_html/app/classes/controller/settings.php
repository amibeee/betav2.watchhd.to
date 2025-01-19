<?php

class Controller_Settings extends Controller_Base_User
{
    public function action_index()
    {
        $user = Model_User::query()->where('id', '=', $this->user['uid'])->get_one();

        // If a POST request is made, handle the form submission
        if (Input::method() == 'POST') {
            $this->handlePostRequest($user);
        }

        // Prepare the data for the view
        $data['user'] = $user;

        return Response::forge(View::forge('settings.html.twig', $data, false));
    }

    private function handlePostRequest($user)
    {
        // Retrieve form inputs
        $current_password = trim(Input::post('current_password', ''));
        $password1 = trim(Input::post('password1', ''));
        $password2 = trim(Input::post('password2', ''));
        $email_notification_premium_ends = Input::post('email_notification_premium_ends', 'off') === 'on' ? 1 : 0;
        $email_notification_record_download_available = Input::post('email_notification_record_download_available', 'off') === 'on' ? 1 : 0;
        $email_notification_friend_sale = Input::post('email_notification_friend_sale', 'off') === 'on' ? 1 : 0;
        $pin1 = trim(Input::post('pin1', ''));
        $pin2 = trim(Input::post('pin2', ''));
        $enable_2fa = Input::post('enable_2fa', 'off') === 'on' ? 1 : 0;

        // Validate the settings
        $errors = $this->validateSettings($user, $current_password, $password1, $password2, $pin1, $pin2);

        // If there are no errors, update the user settings
        if (count($errors) == 0) {
            $this->updateUserSettings($user, $email_notification_premium_ends, $email_notification_record_download_available, $email_notification_friend_sale, $pin1, $pin2, $password1, $enable_2fa);
            \Messages::success(i18n::t('Änderungen wurden übernommen.'));
            \Messages::redirect('/settings');
        } else {
            // Flash the old input and display errors
            Session::set_flash('input.old', Input::post());
            \Messages::error(implode('<br />', $errors));
            \Messages::redirect(Uri::current());
        }
    }

    private function validateSettings($user, $current_password, $password1, $password2, $pin1, $pin2)
    {
        $errors = [];

        // Check if the current password is correct when changing the password
        if (!empty($password1) && Auth::instance()->hash_password($current_password) != $user->password) {
            $errors[] = i18n::t('Um die Änderung deines Passworts zu bestätigen musst du dein altes Passwort angeben.');
        }

        // Prevent certain users from changing their passwords
        if ($this->user['username'] == 'szenebox' && !empty($password1)) {
            $errors[] = i18n::t('Das Passwort für diesen User kann nicht geändert werden.');
        }

        // Validate the new password length
        if (!empty($password1) && mb_strlen($password1) < 8) {
            $errors[] = i18n::t('Dein neues Passwort muss min. 8 Zeichen lang sein.');
        }

        // Check if the new passwords match
        if ($password1 != $password2) {
            $errors[] = i18n::t('Du musst dein neues Passwort im Feld Neues Password (wiederholen) bestätigen.');
        }

        // Validate PIN changes
        if (!empty($pin2) && $pin1 != $this->user['pin']) {
            $errors[] = 'Um deine Jugendschutz PIN zu ändern musst du die aktuelle Jugendschutz PIN eingeben.';
        }

        if (!empty($pin2) && $pin1 == $this->user['pin']) {
            if (!ctype_digit($pin2) || mb_strlen($pin2) != 4) {
                $errors[] = 'Deine Jugendschutz PIN muss aus 4 Zahlen bestehen.';
            }
        }

        return $errors;
    }

    private function updateUserSettings($user, $email_notification_premium_ends, $email_notification_record_download_available, $email_notification_friend_sale, $pin1, $pin2, $password1, $enable_2fa)
    {
        // Update email notification settings
        $user->email_notification_premium_ends = $email_notification_premium_ends;
        $user->email_notification_record_download_available = $email_notification_record_download_available;
        $user->email_notification_friend_sale = $email_notification_friend_sale;

        // Update PIN if applicable
        if (!empty($pin2) && $pin1 == $this->user['pin']) {
            $user->pin = $pin2;
        }

        // Update password if provided
        if (!empty($password1)) {
            $user->password = Auth::instance()->hash_password($password1);
            $user->force_password_reset = 0; // Reset the flag if the password is updated
        }

        // Update 2FA setting
        $user->two_fa_enabled = $enable_2fa;

        // Save changes to the user model
        $user->save();
    }
}
