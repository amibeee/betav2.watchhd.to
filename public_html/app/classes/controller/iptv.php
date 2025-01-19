<?php
Class Controller_Iptv extends Controller_Base_User
{

    public function action_index()
    {

	if(!$this->user["is_premium"])
	{
            Messages::error("Du musst einen Premium Account haben um diese Seite sehen zu können.");
	    Messages::redirect("buy/premium");
	}


	$data['user'] = Model_User::query()->where('id', '=', $this->user['uid'])->get_one();

        return Response::forge(View::forge('m3uinfo.html.twig', isset($data) ? $data : array(), false)); 
        
    }

}