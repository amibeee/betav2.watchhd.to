<?php
class Controller_Ghru4pxsa3_Switches extends Controller_Base_Admin
{
    
    public function action_index()
    {

        if(Input::method() == 'POST')
        {

            $channel_id = Input::post('channel_id');
            $on = Input::post('on');
            $off = Input::post('off');
            
            if(Model_Channel::query()->where('id', '=', $channel_id)->count() == 0)
            {
                \Messages::error('Es wurde ein nicht existierender Kanal ausgewählt.');
                \Messages::redirect('ghru4pxsa3/switches');
            }

            if(!strtotime($on))
            {
                \Messages::error('Bitte wähle ein gültiges Datum und eine gültige Uhrzeit zu der der Sender eingeschaltet werden soll.');
                \Messages::redirect('ghru4pxsa3/switches');
            }

            if(!strtotime($off))
            {
                \Messages::error('Bitte wähle ein gültiges Datum und eine gültige Uhrzeit zu der der Sender abgeschaltet werden soll.');
                \Messages::redirect('ghru4pxsa3/switches');
            }

            if(strtotime($on) > strtotime($off))
            {
                \Messages::error('Der Sender muss vor dem abschalten eingeschaltet werden.');
                \Messages::redirect('ghru4pxsa3/switches');
            }

            if((strtotime($on) || strtotime($off)) < time())
            {
                \Messages::error('Der Sender kann nicht vor dem aktuellen Datum an oder aus geschaltet werden.');
                \Messages::redirect('ghru4pxsa3/switches');
            }

            $switch = new Model_Channel_Switch;
            $switch->channel_id = $channel_id;
            $switch->switch = 'on';
            $switch->date = strtotime($on);
            $switch->save();

            $switch = new Model_Channel_Switch;
            $switch->channel_id = $channel_id;
            $switch->switch = 'off';
            $switch->date = strtotime($off);
            $switch->save();


            \Messages::success('Schalter wurden angelegt.');
            \Messages::redirect('ghru4pxsa3/switches');

        }

        $data['switch'] = array(
            'channel_id' => 0,
            'on' => '',
            'off' => ''
        );

        if(Input::get('do') == 'delete')
        {
            
            $switch = Model_Channel_Switch::query()->where('id', '=', Input::get('id'))->get_one();
            $off = Model_Channel_Switch::query()->where('channel_id', '=', $switch->channel_id)->where('date', '>', $switch->date)->get_one();
            $switch->delete();
            $off->delete();

            \Messages::success("Schalter wurden gelöscht.");
            \Messages::redirect("ghru4pxsa3/switches");
            
        }

        if(Input::get('do') == 'copy')
        {
            
            $switch = Model_Channel_Switch::query()->where('id', '=', Input::get('id'))->get_one();
            $off = Model_Channel_Switch::query()->where('channel_id', '=', $switch->channel_id)->where('date', '>', $switch->date)->get_one();
            $switch->on = $switch->date;
            $switch->off = $off->date;
            $data['switch'] = $switch;
            
        }

        $data['switches'] = array();
        foreach(Model_Channel_Switch::query()->where('switch', '=', 'on')->order_by('date', 'asc')->get() as $switch)
        {
            $switch->channel = Model_Channel::query()->where('id', '=', $switch->channel_id)->get_one();
            
            $off = Model_Channel_Switch::query()->where('channel_id', '=', $switch->channel_id)->where('date', '>', $switch->date)->get_one();
            $switch->off_date = $off->date;
            $data['switches'][] = $switch;

        }

    

        $data['channels'] = Model_Channel::query()->order_by('name', 'asc')->get();
        return Response::forge(View::forge('ghru4pxsa3/switches.html.twig', isset($data) ? $data : array(), false)); 
    }
        

}