<?php
namespace Fuel\Tasks;
class Addpackagestolines
{
	
	// das hier für hppppp lines

	public static function run()
	{
		
		$argv = func_get_args();
	
		$line_id = isset($argv[0]) ? (int) $argv[0] : 0;
		
		$defaults = array();
							
		// setz mal am ende in das array true
		/// wenn inactive gesetzt ist und true dann wird trotzdem die laufzeit gesetzt in meinem kopf ^^ als mach mal die liste fertig walsadfaf
			$defaults[] = array(1, 'active', 'IPTV-DACH', true); 
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
		foreach(\Model_User_Line::query()->where('id', $line_id)->get() as $user)
        {
				
				$the_user = \Model_User::query()->where('id', $user->user_id)->get_one();
				if(!$the_user){
					echo "[debug] connected user does not exsits\n";
					continue;
				}
				
				// alte löschen da steht pakete löschen es geht nur drum pakete zu köschen die weg soollen und 8 adenn mann das doch nur anzeige kacke
			/// pack mal ins arrax die puckets die gelöscht werden sollen als
			foreach(\Model_User_Packet::query()->where('user_id', $user->user_id)->where('line_id', $user->id)->where('bouquet_id', 'IN', array(1,2,3,4,5,8,9,10,11,12,20, 18, 21, 16, 17, 26, 23, 25, 24, 22, 27, 28))->where('line_type', 'subline')->get() as $package){
				$package->delete();
				
			}
			
			$packetstack = array();
			
                            #$type = isset($data['buy']['type']) && $data['buy']['type'] == 'subline' ? 'subline' : 'mineline'; 
                            #$line_id = $data['buy']['line_id'];

                            foreach($defaults as $key => $default){
							

                                $packet = \Model_User_Packet::query()->where('user_id', $user->user_id)->where('line_id', $user->id)->where('bouquet_id', $default[0])->get_one();
                                if($packet){
									
									$packetstack[] = $packet->id;
									
									// müsste passen, checke nur nicht wieso er hier also wieso...
									
									// das auskommentiert is_a
									
									/*
                                    $booked_packages = isset($data['packages']) ? $data['packages'] : array(24, 35);
                                    $status = in_array($default[0], $booked_packages) ? $default[1] : 0;

                                    $packet->booked_until = ($packet->booked_until == 0 ? (time()+$add) : ($packet->booked_until+$add));
                                    $packet->status = $status;
                                    $packet->save();
									*/
									
                                }
                                else{

                                    #$status = in_array($default[0], array(24, 35)) ? $default[1] : 'inactive';
                                    $status = in_array($default[0], array(1, 2, 3, 4, 5, 8, 9, 10, 11, 24, 27, 28)) ? $default[1] : 'inactive';
									
                                    $packet = new \Model_User_Packet;
									$packet->name = $default[2];
                                    $packet->user_id = $user->user_id;
                                    $packet->status = $status;
                                    $packet->line_type = 'subline';
                                    $packet->line_id = $user->id;
                                    $packet->bouquet_id = $default[0];
                                    #$packet->booked_until = ($status == 'active' ? $user->premium_until : 0);
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

/// es werden hier doch alle accs gelöscht ^^ denk i
			echo sprintf('Pakete löschen.  %s Pakete behalten.', count($packetstack));
			$packages = \Model_User_Packet::query()->where('line_id', $user->id)->where('line_type', 'subline')->get();
			echo count($packages);
			
			foreach($packages as $package){
				
				if(in_array($package->id, array_values($packetstack))){
					echo sprintf('%s is wichgit kunge.', $package->id).PHP_EOL;
					continue;
				}
				///wtf oO;
				
				echo sprintf('%s löschen.', $package->id).PHP_EOL;			
			// where('id', 'NOT IN', $packetstack)
				#$package->delete();
			}

		
		
		}
		
		

	
	}

}