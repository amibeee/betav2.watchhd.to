<?php
class Controller_Playlist extends Controller_Base_Public
{

    public function action_index($public_token)
    {

        $playlist = Model_Customizeduserplaylists::query()->where('public_token', $public_token)->get_one();
        if(!$playlist)
        {
            throw new HttpNotFoundException();
        }

        return new Response($playlist->data);

    }

}