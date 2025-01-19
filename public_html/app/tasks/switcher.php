<?php
namespace Fuel\Tasks;
class Switcher
{

    public static function run()
    {

        foreach(\Model_Channel_Switch::query()->where('date', '<', time())->get() as $switcher)
        {


            $channel = \Model_Channel::query()->where('id', '=', $switcher->channel_id)->get_one();

            switch($switcher->switch)
            {

                case 'on':
					\Cli::write('Schalte ein');
                    file_get_contents('http://iptv.watchhd.cc:8000/api.php?action=stream&sub=start&server_id='.$channel->server_id.'&stream_ids[]='.$channel->url);
                break;

                case 'off':
					\Cli::write('Schalte aus');
                    file_get_contents('http://iptv.watchhd.cc:8000/api.php?action=stream&sub=stop&server_id='.$channel->server_id.'&stream_ids[]='.$channel->url);
                break;

            }

            $switcher->delete();

        }

		}

}