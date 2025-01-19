<?php
namespace Fuel\Tasks;
class Migratepackages
{

	public static function run()
	{

		try{
        $packets[24]['name'] = 'IPTV-DACH';
		$packets[24]['description'] = '';
		$packets[24]['price'] = 7.00;
		$packets[24]['turnable'] = false;
		$packets[24]['channels'] = 'Sky DE, HD+, DAZN, Teleclub HD, MySports HD uvm.';
			
		$packets[35]['name'] = 'IPTV-Europa & Amerika';
		$packets[35]['description'] = '';
		$packets[35]['price'] = 0.00;
		$packets[35]['turnable'] = true;
		$packets[35]['channels'] = 'Sky UK, Bein Sports France, LaLiga TV, Lig TV, NFL Gamepass, ESPN uvm.';
			
		$packets[23]['name'] = 'IPTV-XXX';
		$packets[23]['description'] = '';
		$packets[23]['price'] = 0.00;
		$packets[23]['turnable'] = true;
		$packets[23]['channels'] = 'Redlight HD, Hustler TV, Penthouse HD, RealityKings uvm.';
			
		$packets[45]['name'] = 'IPTV-VoD';
		$packets[45]['description'] = '';
		$packets[45]['price'] = 0.00;
		$packets[45]['turnable'] = true;
		$packets[45]['channels'] = 'ca. 30-40 Kinohits in 720p';
			
		$packets[31]['name'] = 'IPTV-VoD Premium';
		$packets[31]['description'] = '';
		$packets[31]['price'] = 3.00;
		$packets[31]['turnable'] = true;
		$packets[31]['channels'] = 'über 3000+ Filme  in 4K, 3D, 1080p und 720p';
        
        foreach(\Model_User_Line::query()->where('migrated', 0)->get() as $user_line){

            if(\Model_User_Packet::query()->where('user_id', $user_line->user_id)->where('line_type', 'mainline')->count() < 50000){

				$user = \Model_User::query()->where('id', $user_line->user_id)->get_one();
				if($user){

					foreach($packets as $bouquet_id => $packet){

						$user_packet = \Model_User_Packet::query()->where('line_type', 'mainline')->where('user_id', $user_line->user_id)->where('bouquet_id', $bouquet_id)->get_one();
						if(!$user_packet){
							$user_packet = new \Model_User_Packet;
							$user_packet->user_id =  $user_line->user_id;
							$user_packet->status = (in_array($bouquet_id, array(31, 23)) ? 'inactive' : 'active');
							$user_packet->booked_until = ($bouquet_id != 31 ? $user->premium_until : 0);
							$user_packet->name = $packet['name'];
							$user_packet->line_id = $user_line->id;
							$user_packet->line_type = 'mainline';
							$user_packet->bouquet_id = $bouquet_id;
							$user_packet->save();
							#sleep(1);
						}
						else{
							$user_packet->line_id = $user_line->id;
							$user_packet->save();
							#sleep(1);
						}
	
					}

					$user_line->migrated = 1;
					$user_line->save();

					echo "Packages for user {$user->username} successfully updated\n";

            		sleep(1);
				
				}

                

            }
            

            

            
        
		}
		
	}
	catch(Exception $e){
		var_dump($e);
	}
			
		
	
	}

}