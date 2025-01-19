<?php
namespace Fuel\Tasks;
class Migratemainlines
{

	public static function run()
	{
        
        foreach(\Model_User::query()->where('migrated', 0)->get() as $user){

            $line = new \Model_User_Line;
            $line->user_id = $user->id;
            $line->username = $user->username;
            $line->password = $user->line_password;
            $line->premium_until = $user->premium_until;
            $line->display_name = '';
            $line->save();

            $user->migrated = 1;
            $user->save();

            echo "Line fuer user {$user->username} migriert\n";

            #sleep(1);
        
        }
			
		
	
	}

}