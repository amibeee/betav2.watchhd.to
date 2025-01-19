<?php
namespace Fuel\Tasks;
class Defaultlines
{

	public static function run()
	{

		$panel_url = 'http://iptv.watchhd.cc:8000/';
        	$max_connections = 1;
        	$reseller = 0;
        	$bouquet_ids = array(
            		1,
        	);
		
		// haupt lines updaten
		foreach(\Model_User::query()->where('premium_until', '>', time())->get() as $user)
        	{

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

		}

	
	}

}