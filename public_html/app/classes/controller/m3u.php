<?php
Class Controller_M3u extends Controller_Base_Public
{

    public function action_info()
    {

	if(!$this->user["is_user"])
	{
            Messages::error("Du musst dich einloggen um diese Seite sehen zu können.");
	    Messages::redirect("login");
	}

        return Response::forge(View::forge('m3uinfo.html.twig', isset($data) ? $data : array(), false)); 
        
    }

}