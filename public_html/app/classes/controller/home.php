<?php
class Controller_Home extends Controller_Base_Public
{
    public function before()
    {
        parent::before();
        
        // Debug log
        \Log::debug('User info in Controller_Home: ' . print_r($this->user, true));
    }

    public function action_index()
    {
        // Safety check
        if ($this->user === null) {
            \Log::error('User info is null in Controller_Home');
            $this->user = array('is_authenticated' => false); // Set a default value
        }

        // Redirect authenticated users to the landing page
        if (!empty($this->user['is_authenticated'])) {
            Response::redirect('landing');
        }

        $data['channels'] = Model_Channel::query()->where('active', '=', 1)->order_by('position', 'ASC')->get();

        $data['buli_alert'] = '';
        if (time() <= strtotime('20.01.2017')) {
            if (date('d.m.y') == '20.01.2017') {
                $data['buli_alert'] = '<i class="fa fa-futbol-o" aria-hidden="true"></i> Die Bundesliga startet heute. Wir wünschen euch viel Spaß. <i class="fa fa-futbol-o" aria-hidden="true"></i>';
            } else {
                $diff = round(((strtotime('20.01.2017') - time()) / 86400));
                $data['buli_alert'] = '<i class="fa fa-futbol-o" aria-hidden="true"></i> Die Bundesliga startet wieder. Nur noch ' . $diff . ' Tage bis es wieder heißt `Der Ball ist rund und das Spiel dauert 90 Minuten`. <i class="fa fa-futbol-o" aria-hidden="true"></i>';
            }
        }

        return Response::forge(View::forge('home.html.twig', isset($data) ? $data : array(), false));
    }
}