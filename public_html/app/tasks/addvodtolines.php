<?php
namespace Fuel\Tasks;
class Addvodtolines
{

	public static function run()
	{
		
		
		$defaults = array();
                            $defaults[] = array(24, 'active', 'IPTV-DACH');
                            $defaults[] = array(35, 'active', 'IPTV-Europa & Amerika');
                            $defaults[] = array(23, 'inactive', 'IPTV-XXX');
                            $defaults[] = array(45, 'active', 'IPTV-VoD');
                            $defaults[] = array(31, 'inactive', 'IPTV-VoD Premium');
		

		$panel_url = 'http://iptv.watchhd.cc:8000/';
        	$max_connections = 1;
        	$reseller = 0;
        	$bouquet_ids = array(
            		1,
        	);
		
		// haupt lines updaten
		foreach(\Model_User_Line::query()->where('premium_until', '>', time())->get() as $user)
        	{
				
			

                            $vod_premium  = false;
				
						$last_payment = \Model_User_Payment::query()->where('user_id', $user->user_id)->where('status', 'paid')->where('data', 'LIKE', '%subline%')->order_by('id', 'desc')->get_one();
            if($last_payment){
                $data = json_decode($last_payment->data);
                if(isset($data->packages) && in_array(31, (array)$data->packages)){
                    $vod_premium = true;
                }
            }
			
			if($vod_premium){
			
			$package = $packet = \Model_User_Packet::query()->where('user_id', $user->user_id)->where('line_id', $user->id)->where('bouquet_id', 31)->get_one();
			if($package){
				$package->status = 'active';
				$package->booked_until = $user->premium_until;
				$package->save();
				echo "[debug] Vod Premium fuer user {$user->user_id} & Subline {$user->id} aktiv\n";
			}
			
			}
			else{
				echo "[debug] Vod Premium fuer user {$user->user_id} nicht aktiv\n";
			}
		
		
		}
		
		

	
	}

}