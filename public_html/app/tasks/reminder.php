<?php
namespace Fuel\Tasks;
class Reminder
{

    public static function run()
    {
	
		$argv = func_get_args();
		$user_id = isset($argv[0]) ? (int) $argv[0] : 0;
		
		// mainlines
		$query = \Model_User::query()->where('premium_until', '>', time());
		if($user_id){
			$query->where('id', $user_id);
		}
		
        foreach($query->get() as $user)
        {
			
			if($user->email_notification_premium_ends != 1){
				echo "User {$user->username} has disabled email reminder\n";
				continue;
			}
			
			if((time()-$user->reminder_send_at) < (6*86400)){
				echo "Line user ({$user->username}) already notificated\n";
				continue;
			}
			
			$start_date = new \DateTime(date("Y-m-d", time()));
			$end_date = new \DateTime(date("Y-m-d", $user->premium_until));
 
			//difference between two dates
			$diff = $start_date->diff($end_date);
 
			//find the number of days between two dates
			$remaining  = $diff->format("%a");
			
		
			if($remaining != 7){
				echo "Line ({$user->username}) already expired or not within the given date range for notification ({$remaining})\n";
				continue;
			}
			
			$type = (stristr($user->username, '_') ? 'subline' : 'mainline');
			$link = 'https://watchhd.to/buy?line='.$user->username.'&type='.$type;
			
			$mailer = \Email::forge();
            $mailer->to($user->email);
        
            $mailer->subject('[watchhd.to] IPTV-Ablauf');
                
                $email_data = array(
                    'username' => $user->username,
                    'link' => $link
                );
                
                $mailer->html_body(\View::forge('email/reminder.html.twig', $email_data));
                
                try
                {
                    $mailer->send();
                }
                catch(\EmailSendingFailedException $e)
                {
                    // The driver could not send the email
                }

			$user->reminder_send_at = time();
			$user->save();
			
			sleep(3);


		}
		
		// sublines
		$query = \Model_User_Line::query()->where('premium_until', '>', time());
		if($user_id){
			$query->where('user_id', $user_id);
		}
		
        foreach($query->get() as $user_line)
        {
			
			$user = \Model_User::query()->where('id', $user_line->user_id)->get_one();
			if(!$user) continue;
			if($user->email_notification_premium_ends != 1){
				echo "User {$user->username} has disabled email reminder\n";
				continue;
			}
			
			if((time()-$user_line->reminder_send_at) < (6*86400)){
				echo "Line user ({$user_line->username}) already notificated\n";
				continue;
			}
			
			$start_date = new \DateTime(date("Y-m-d", time()));
			$end_date = new \DateTime(date("Y-m-d", $user_line->premium_until));
 
			//difference between two dates
			$diff = $start_date->diff($end_date);
 
			//find the number of days between two dates
			$remaining  = $diff->format("%a");
			
		
			if($remaining != 7){
				echo "Line ({$user_line->username}) already expired or not within the given date range for notification ({$remaining})\n";
				continue;
			}
			
			$type = (stristr($user_line->username, '_') ? 'subline' : 'mainline');
			$link = 'https://watchhd.to/buy?line='.$user_line->username.'&type='.$type;
			
			$mailer = \Email::forge();
            $mailer->to($user->email);
        
            $mailer->subject('[watchhd.to] IPTV-Ablauf');
                
                $email_data = array(
                    'username' => $user->username,
                    'link' => $link
                );
                
                $mailer->html_body(\View::forge('email/reminder.html.twig', $email_data));
                
                try
                {
                    $mailer->send();
                }
                catch(\EmailSendingFailedException $e)
                {
                    // The driver could not send the email
                }

			$user_line->reminder_send_at = time();
			$user_line->save();
			
			sleep(3);


		}
		
	}
	
}