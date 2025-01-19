<?php
namespace Fuel\Tasks;
class Linesync2
{

	public static function run()
	{

	
	$argv = func_get_args();
	
		$user_id = isset($argv[0]) ? (int) $argv[0] : 0;


		$query = \Model_User_Line::query()->where('premium_until', '>', time());
		if($user_id){
			$query->where('user_id', $user_id);
		}
		foreach($query->order_by('id', 'DESC')->get() as $user)
		
		{

			$line['username'] = $user->username;
			$line['password'] = $user->password;

			$expire_date = $user->premium_until;

			// $bouquet_ids
			$bouquet_ids = array();
			/*
			$last_payment = \Model_User_Payment::query()->where('user_id', $user->user_id)->where('status', 'paid')->where('data', 'LIKE', '%subline%')->where('data', "LIKE", "%{$user->username}%")->order_by('id', 'desc')->get_one();
			*/
			/*
			foreach($last_payments as $payment){
				$payment_data = json_decode($payment['data'], true);
				if(isset($payment_data['buy']['line_id']) && $payment_data['buy']['line_id'] == $user->id){
					$last_payment = $payment;
				}
					
			}
			*/
			/*
			if(!$last_payment){
				\Cli::write('No valid previous data payment found');
				continue;
			}
			
			if($last_payment){
				
				$payment_data = json_decode($last_payment['data'], true);
				if(isset($payment_data['packages']) && count($payment_data['packages'])){
					echo "Setze Ids ".implode(", ", $payment_data['packages'])."\n";
					$bouquet_ids = $payment_data['packages'];
				}
				   
			}
			*/
			
			$active_packets = array();
			foreach(\Model_User_Packet::query()->where('line_id', $user->id)->where('booked_until', '>', time())->where('status', 'active')->get() as $packet){
				$active_packets[] = $packet->bouquet_id;
			}
			
			if(in_array(1, $active_packets)){
				$bouquet_ids[] = 1; // sex wird nicht geadded automatisch nur wenn aktiv
			}
			
			if(in_array(2, $active_packets)){
				$bouquet_ids[] = 2; // sex wird nicht geadded automatisch nur wenn aktiv
			}
			
			if(in_array(3, $active_packets)){
				$bouquet_ids[] = 3; // sex wird nicht geadded automatisch nur wenn aktiv
			}
			
			if(in_array(4, $active_packets)){
				$bouquet_ids[] = 4; // sex wird nicht geadded automatisch nur wenn aktiv
			}
			
			if(in_array(5, $active_packets)){
				$bouquet_ids[] = 5; // sex wird nicht geadded automatisch nur wenn aktiv
			}
			
			if(in_array(8, $active_packets)){
				$bouquet_ids[] = 8; // sex wird nicht geadded automatisch nur wenn aktiv
			}
			
			if(in_array(9, $active_packets)){
				$bouquet_ids[] = 9; // sex wird nicht geadded automatisch nur wenn aktiv
			}
			
			if(in_array(10, $active_packets)){
				$bouquet_ids[] = 10; // sex wird nicht geadded automatisch nur wenn aktiv
			}
			
			if(in_array(11, $active_packets)){
				$bouquet_ids[] = 11; // sex wird nicht geadded automatisch nur wenn aktiv
			}
			
			if(in_array(12, $active_packets)){
				$bouquet_ids[] = 12; // sex wird nicht geadded automatisch nur wenn aktiv
			}
			
			if(in_array(24, $active_packets)){
				$bouquet_ids[] = 24; // sex wird nicht geadded automatisch nur wenn aktiv
			}
			
			if(in_array(27, $active_packets)){
				$bouquet_ids[] = 27; // sex wird nicht geadded automatisch nur wenn aktiv
			}
			
			if(in_array(28, $active_packets)){
				$bouquet_ids[] = 28; // sex wird nicht geadded automatisch nur wenn aktiv
			}
			
			$bouquet_ids = array_unique($bouquet_ids);
			
			echo "[debug] adding bouquet ids ".implode(", ", $bouquet_ids)." to {$user->username}\n";
			
			$post_data = array(
			    'username' => $line['username'],
				'exp_date' => $expire_date, 
				'bouquet' => json_encode( array_values($bouquet_ids) )
    		);

			$opts = array( 'http' => array(
			        'method' => 'POST',
			        'header' => 'Content-type: application/x-www-form-urlencoded',
			        'content' => http_build_query( $post_data ) ) );

			$context = stream_context_create($opts);
			$body =  file_get_contents("http://iptv.watchhd.cc:5050/api/api.php?action=set_exp_and_bouquet", false, $context);
			$api_result = json_decode($body);
			
			echo "<pre>";
			print_r($post_data);
			echo "</pre>";

			if(isset($api_result->success) && $api_result->success)
            {
            	\Cli::write('[success] [UID:'.$user->id.'] Line Update');
            }
            else
            {
            	\Cli::write('[error] [UID:'.$user->id.'] Line Update ['.$body.']');
            }
			
			usleep(500000);

		}
	
	}

}