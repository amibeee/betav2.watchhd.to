<?php
class Controller_Ghru4pxsa3_Ajax_Bonustime extends Controller_Base_Ajax
{
    public function post_index()
    {
        \Log::info('post_index method called', __METHOD__);

        if(!$this->user['is_admin']) {
            \Log::warning('Access denied for non-admin user', __METHOD__);
            return $this->response(array('success' => false, 'message' => 'Access denied'));
        }

        \Log::info('Admin user confirmed', __METHOD__);

        $defaults = array();
        $defaults[] = array(1, 'active', 'IPTV-DACH'); 
        $defaults[] = array(2, 'inactive', 'IPTV-DE HEVC', true);
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

        $user_id = (int)Input::post('user_id');
        $bonustime = (int)Input::post('bonustime');
        \Log::info("Received input: user_id={$user_id}, bonustime={$bonustime}", __METHOD__);

        $user = Model_User::query()->where('id', '=', $user_id)->get_one();
        if(!$user) {
            \Log::error("User not found: user_id={$user_id}", __METHOD__);
            return $this->response(array('success' => false, 'message' => 'User not found'));
        }

        \Log::info("User found: " . json_encode($user), __METHOD__);

        if($user->premium_until == 0) {
            $user->premium_until = time();
            \Log::info("User premium_until set to current time: " . time(), __METHOD__);
        }

        $user->premium_until += $bonustime;
        $user->save();
        \Log::info("Updated premium_until: {$user->premium_until}", __METHOD__);

        $bouquet_ids = array();

        foreach($defaults as $key => $default) {
            $packet = Model_User_Packet::query()->where('user_id', $user->id)->where('bouquet_id', $default[0])->get_one();

            if($packet) {
                $status = in_array($default[0], array(1,3,4,5,8,9,10,24)) ? $default[1] : 'inactive';
                $packet->booked_until = $user->premium_until;
                $packet->status = $status;
                $packet->save();

                \Log::info("Updated packet: " . json_encode($packet), __METHOD__);

                if($status == 'active') {
                    $bouquet_ids[] = $default[0];
                }
            } else {
                $status = in_array($default[0], array(1,3,4,5,8,9,10,24)) ? $default[1] : 'inactive';
                $packet = new Model_User_Packet;
                $packet->name = $default[2];
                $packet->user_id = $user->id;
                $packet->status = $status;
                $packet->line_type = 'mainline';
                $packet->line_id = $user->id;
                $packet->bouquet_id = $default[0];
                $packet->booked_until = $user->premium_until;
                $packet->save();

                \Log::info("Created new packet: " . json_encode($packet), __METHOD__);

                if($status == 'active') {
                    $bouquet_ids[] = $default[0];
                }
            }
        }

        $line = Model_User_Line::query()->where('user_id', $user->id)->get_one();
        if($line) {
            \Log::info("User line found: " . json_encode($line), __METHOD__);

            $post_data = array(
                'username' => $line->username,
                'exp_date' => $user->premium_until,
                'bouquet' => json_encode(array_values($bouquet_ids))
            );

            $opts = array('http' => array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($post_data)
            ));

            $context = stream_context_create($opts);
            $body = file_get_contents("http://iptv.watchhd.cc:5050/api/api.php?action=set_exp_and_bouquet", false, $context);
            $api_result = json_decode($body);

            \Log::info('Data sent to API: ' . json_encode($post_data), __METHOD__);
            \Log::info('API response: ' . $body, __METHOD__);

            if(!isset($api_result->success) || !$api_result->success) {
                \Log::error("Line sync failed: " . $body, __METHOD__);
                return $this->response(array('success' => false, 'message' => 'Line sync failed: ' . $body));
            }
        } else {
            \Log::warning("No line found for user_id={$user->id}", __METHOD__);
        }

        \Log::info('Post index completed successfully', __METHOD__);
        return $this->response(array('success' => true, 'message' => ''));
    }
}
