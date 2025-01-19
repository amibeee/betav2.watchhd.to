<?php
namespace Fuel\Tasks;
class Updatepackages
{

	public static function run()
	{
		
		$times = array();
		
		$payments = \Model_User_Payment::query()->where('status', 'paid')->order_by('created_at', 'DESC')->group_by('user_id')->get();
		if($payments){
		
			foreach($payments as $payment){
				
				echo "processing payment with id {$payment->id}\n";
				
				$data = json_decode($payment->data, true);
				$data['packages'] = isset($data['packages']) ? $data['packages'] : array();
				$packages = array_values($data['packages']);
				if(in_array(31, $packages)){
					
					echo "package 31 found in payment with id {$payment->id}\n";
					
					
					if($data['buy']['type'] == 'mainline'){
					
						$overall_booked = 0;
						$packets = \Model_User_Packet::query()->where('line_type', 'mainline')->where('bouquet_id', '!=', 31)->where('line_id', $data['buy']['line_id'])->get();
						foreach($packets as $packet){
							$overall_booked = $packet->booked_until;
							break;
						}
						
						$overall_booked = $overall_booked > 0 ? $overall_booked : time();
						$add = ($data['premium_days']*86400);
						$booked_until = ($overall_booked+$add);
						\DB::update('user_packets')->set(array('booked_until' => $overall_booked))->where('line_type', 'mainline')->where('bouquet_id', 31)->where('line_id', $data['buy']['line_id'])->execute();
						
						
					}
					else{
						
						$overall_booked = 0;
						$packets = \Model_User_Packet::query()->where('line_type', 'subline')->where('bouquet_id', '!=', 31)->where('line_id', $data['buy']['line_id'])->get();
						foreach($packets as $packet){
							$overall_booked = $packet->booked_until;
							break;
						}
						
						$overall_booked = $overall_booked > 0 ? $overall_booked : time();
						$add = ($data['premium_days']*86400);
						$booked_until = ($overall_booked+$add);
						\DB::update('user_packets')->set(array('booked_until' => $booked_until))->where('line_type', 'subline')->where('bouquet_id', 31)->where('line_id', $data['buy']['line_id'])->execute();
						
					}
					
					echo "add {$data['premium_days']} days to vod premium package for {$data['buy']['type']} with id {$data['buy']['line_id']}\n";
					$times[$payment->user_id][] = $payment->created_at;
				}
				else{
					echo "package 31 NOT found in payment with id {$payment->id}\n";
				}
				
			}
		
		}
		
		echo "<pre>";
		print_r([$times, count($times)]);

	
	}

}