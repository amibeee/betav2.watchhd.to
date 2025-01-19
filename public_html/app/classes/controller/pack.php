<?php
class Controller_Line extends Controller_Base_User
{

 public function action_packages()
 {

     $line = Model_User_Line::query()->where('username', Input::get('line'))->where('user_id', $this->user['uid'])->get_one();
     if(!$line && Input::get('line') != $this->user['username'])
     {
         echo($this->username['username']);
     }

     $line_id = $line ? $line->id : $this->user['uid'];
     $user_id = $line ? $line->user_id : $this->user['uid'];
 

     $data['packages'] = Model_User_Packet::query()->where('user_id', $user_id)->where('line_id', $line_id)->order_by('name', 'asc')->get();

     return Response::forge(View::forge('packages.html.twig', isset($data) ? $data : array(), false)); 

 }
}