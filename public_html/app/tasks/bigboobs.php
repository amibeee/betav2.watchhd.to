<?php
namespace Fuel\Tasks;
class Bigboobs
{

	public static function run()
	{

		
		$users = \Model_User::query()->where('premium_until', '>', time())->get();
		foreach($users as $user){

			foreach(\Model_User_Packet::query()->where('user_id', $user->id)->where('line_type', 'mainline')->get() as $packet){
				
				$packet->booked_until = $user->premium_until;
				$packet->save();

			}

		}
		
		
		#$user_lines = \Model_User_Line::query()->where('user_id', 3159)->where('premium_until', '>', time())->get();
		$user_lines = \Model_User_Line::query()->where('premium_until', '>', time())->get();
		
		foreach($user_lines as $user_line){

			foreach(\Model_User_Packet::query()->where('line_id', $user_line->id)->where('line_type', 'subline')->get() as $packet)
			{
				
				$correct = date("d.m.Y", $user_line->premium_until);
				$current = date("d.m.Y", $packet->booked_until);
				
				echo "[info] update packet with id {$packet->id} successfully\n";
				echo "[info] sollte laufzeit haben bis {$correct}\n";
				echo "[info] valid until (old) {$current}\n";
				
				$packet->booked_until = $user_line->premium_until;
				$packet->save();
				
				$updated = date("d.m.Y", $packet->booked_until);
				
				echo "[info] valid until (fixed) {$updated}\n";

			}

		}

		echo "del fin";

	
	}

}