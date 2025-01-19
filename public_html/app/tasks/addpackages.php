<?php
namespace Fuel\Tasks;
class Addpackages
{

	public static function run()
	{
		
		$argv = func_get_args();
	
		$user_id = isset($argv[0]) ? (int) $argv[0] : 0;
		
		
		$defaults = array();
		$defaults[] = array(1, 'active', 'IPTV-DACH'); 
        $defaults[] = array(2, 'inactive', 'IPTV-DE HEVC', true); // da steht auch inactive gell ^^
        $defaults[] = array(3, 'active', 'IPTV-UK', true);
		$defaults[] = array(4, 'active', 'IPTV-Frankreich', true);
        $defaults[] = array(5, 'active', 'IPTV-Polen', true);
		$defaults[] = array(8, 'active', 'IPTV-Türkei', true);
		$defaults[] = array(9, 'active', 'IPTV-Rest von Europa', true);
        $defaults[] = array(10, 'active', 'IPTV-USA/Canada', true);
        $defaults[] = array(11, 'inactive', 'IPTV-Rest der Welt', true);
		$defaults[] = array(12, 'inactive', 'IPTV-World Sport', true);
		$defaults[] = array(24, 'active', 'IPTV-Music', true);
        $defaults[] = array(27, 'inactive', 'IPTV-XXX', true);
        $defaults[] = array(28, 'inactive', 'IPTV-VoD', true);
	
	
		// haupt lines updaten
		
		foreach(\Model_User::query()->where('id', $user_id)->get() as $user)
        {
				
			foreach(\Model_User_Packet::query()->where('user_id', $user->id)->where('bouquet_id', 'IN', array(2,3,4,5,8,9,10,11,12,20, 18, 21, 16, 17, 26, 23, 25, 24, 22, 27, 28))->where('line_type', 'mainline')->get() as $package){
				$package->delete();
			}
			
			
			$packetstack = array();
	
				
			// hier tested doch mal damit ^^

                            #$type = isset($data['buy']['type']) && $data['buy']['type'] == 'subline' ? 'subline' : 'mineline'; 
                            #$line_id = $data['buy']['line_id'];

                            foreach($defaults as $key => $default){
						

                                $packet = \Model_User_Packet::query()->where('user_id', $user->id)->where('bouquet_id', $default[0])->where('line_type', 'mainline')->get_one();
                                if($packet){
									
									$packetstack[] = $packet->id;
									/*
                                    $booked_packages = isset($data['packages']) ? $data['packages'] : array(24, 35);
                                    $status = in_array($default[0], $booked_packages) ? $default[1] : 0;

                                    $packet->booked_until = ($packet->booked_until == 0 ? (time()+$add) : ($packet->booked_until+$add));
                                    $packet->status = $status;
                                    $packet->save();
									*/
									
									// bei status die ids rein die rein sollen & aktiv wenn der user die aktuell net hat
									// bei drunter als halt die die auch aktiv sein sollen ^^ also mit laufzeit als du verstehen als=?
									$status = in_array($default[0], array(1, 2, 3, 4, 5, 8, 9, 10, 11, 24, 27, 28)) ? $default[1] : 'inactive';
									$packet->booked_until = ($status == 'active' || in_array($default[0], array(22)) || $default[3] ? $user->premium_until : 0);
									// nein die 22 bekommt halt die aktive premium laufzeit richtig ^^
									/// keulleeee
									// wenn aktiv oder im array (22) dann premium laufzeit die aktuelle als vermutlich sind halt aktuell nur die sender aktiv? was wieß ich ^^
									$packet->save();
									
									echo "[debug] updated package {$default[0]} for user {$user->id}\n";
									
                                }
                                else{

                                    #$status = in_array($default[0], array(24, 35)) ? $default[1] : 'inactive';
                                    $status = in_array($default[0], array(1, 2, 3, 4, 5, 8, 9, 10, 11, 24, 27, 28)) ? $default[1] : 'inactive';
									
									
                                    $packet = new \Model_User_Packet;
									$packet->name = $default[2];
                                    $packet->user_id = $user->id;
                                    $packet->status = $status;
                                    $packet->line_type = 'mainline';
                                    $packet->line_id = $user->id;
                                    $packet->bouquet_id = $default[0];
                                    #$packet->booked_until = ($status == 'active' || in_array($default[0], array(45,23)) ? $user->premium_until : 0);
                                    $packet->booked_until = ($status == 'active' || in_array($default[0], array(22)) || $default[3] ? $user->premium_until : 0);
									
									$packet->save();
									
									$packetstack[] = $packet->id;
									
									echo "[debug] Added package {$default[0]} to user {$user->id}\n";

                                }

                            }
				
				/*

        		$line['username'] = $user->username;
            	$line['password'] = $user->line_password;

			$expire_date = $user->premium_until;
			
			// $bouquet_ids
			$bouquet_ids = array();

			$last_payment = \Model_User_Payment::query()->where('user_id', $user->id)->where('status', 'paid')->where('data', 'LIKE', '%mainline%')->order_by('id', 'desc')->get_one();
            if($last_payment){
				
				$payment_data = json_decode($last_payment['data'], true);
				if(isset($payment_data['packages']) && count($payment_data['packages'])){
					$bouquet_ids = $payment_data['packages'];
				}
               
			}
			if(count($bouquet_ids) == 0){
				$bouquet_ids[] = 24;
				$bouquet_ids[] = 35;
			}

            $post_data = array(
			    'username' => $line['username'],
			    'password' => $line['password'],
    			'user_data' => array(
					'exp_date' => $expire_date, 
					'bouquet' => json_encode( array_unique($bouquet_ids) )
        		) 
    		);

			$opts = array( 'http' => array(
			        'method' => 'POST',
			        'header' => 'Content-type: application/x-www-form-urlencoded',
			        'content' => http_build_query( $post_data ) ) );

			$context = stream_context_create( $opts );
			$api_result = json_decode( file_get_contents( $panel_url . "api.php?action=user&sub=edit", false, $context ) );

			if($api_result->result == true)
            {
            	\Cli::write('[success] [UID:'.$user->id.'] Line Update');
            }
            else
            {
            	\Cli::write('[error] [UID:'.$user->id.'] Line Update');
            }
			
			sleep(1);
*/
			
			foreach(\Model_User_Packet::query()->where('user_id', $user->id)->where('id', 'NOT IN', $packetstack)->where('line_type', 'mainline')->get() as $package){
				$package->delete();
			}

		
		
		}
		
		

	
	}

}