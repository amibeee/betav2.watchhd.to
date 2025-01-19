<?php
class Controller_Cash_Dashboard extends Controller_Base_Affiliate
{

    /**
     *  
     */     
    public function action_index()
    {   

        $data['stats']['leads']['today'] = Model_Affiliate_Log::query()->where('type', '=', 'lead')->where('affiliate_id', '=', $this->user['uid'])->where(DB::expr('DAY(FROM_UNIXTIME(created_at))'), '=', date('d'))->where(DB::expr('MONTH(FROM_UNIXTIME(created_at))'), '=', date('m'))->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y'))->count();
        $data['stats']['leads']['week'] = Model_Affiliate_Log::query()->where('type', '=', 'lead')->where('affiliate_id', '=', $this->user['uid'])->where(DB::expr('WEEK(FROM_UNIXTIME(created_at))'), '=', date('W'))->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y'))->count();
        $data['stats']['leads']['month'] = Model_Affiliate_Log::query()->where('type', '=', 'lead')->where('affiliate_id', '=', $this->user['uid'])->where(DB::expr('MONTH(FROM_UNIXTIME(created_at))'), '=', date('m'))->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y'))->count();
        $data['stats']['leads']['total'] = Model_Affiliate_Log::query()->where('type', '=', 'lead')->where('affiliate_id', '=', $this->user['uid'])->count();
        
        $data['stats']['sales']['today'] = Model_Affiliate_Log::query()->where('type', '=', 'sale')->where('affiliate_id', '=', $this->user['uid'])->where(DB::expr('DAY(FROM_UNIXTIME(created_at))'), '=', date('d'))->where(DB::expr('MONTH(FROM_UNIXTIME(created_at))'), '=', date('m'))->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y'))->count();
        $data['stats']['sales']['week'] = Model_Affiliate_Log::query()->where('type', '=', 'sale')->where('affiliate_id', '=', $this->user['uid'])->where(DB::expr('WEEK(FROM_UNIXTIME(created_at))'), '=', date('W'))->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y'))->count();
        $data['stats']['sales']['month'] = Model_Affiliate_Log::query()->where('type', '=', 'sale')->where('affiliate_id', '=', $this->user['uid'])->where(DB::expr('MONTH(FROM_UNIXTIME(created_at))'), '=', date('m'))->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y'))->count();
        $data['stats']['sales']['total'] = Model_Affiliate_Log::query()->where('type', '=', 'sale')->where('affiliate_id', '=', $this->user['uid'])->count();
        
        $data['stats']['earnings']['today'] = DB::select(array(DB::expr('SUM(affiliate_log.worth)'), 'sales'))->from('affiliate_log')->where(DB::expr('DAY(FROM_UNIXTIME(created_at))'), '=', date('d'))->where('affiliate_id', '=', $this->user['uid'])->where(DB::expr('MONTH(FROM_UNIXTIME(created_at))'), '=', date('m'))->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y'))->execute()[0]['sales'];
        $data['stats']['earnings']['week'] = DB::select(array(DB::expr('SUM(affiliate_log.worth)'), 'sales'))->from('affiliate_log')->where(DB::expr('WEEK(FROM_UNIXTIME(created_at))'), '=', date('W'))->where('affiliate_id', '=', $this->user['uid'])->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y'))->execute()[0]['sales'];
        $data['stats']['earnings']['month'] = DB::select(array(DB::expr('SUM(affiliate_log.worth)'), 'sales'))->from('affiliate_log')->where(DB::expr('MONTH(FROM_UNIXTIME(created_at))'), '=', date('m'))->where('affiliate_id', '=', $this->user['uid'])->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y'))->execute()[0]['sales'];
        $data['stats']['earnings']['total'] = DB::select(array(DB::expr('SUM(affiliate_log.worth)'), 'sales'))->from('affiliate_log')->where('affiliate_id', '=', $this->user['uid'])->execute()[0]['sales'];
        
        $user = Model_User::query()->where('id', '=', $this->user['uid'])->get_one();
        
        $data['years'] = array();
        for($i = date("Y", $user->created_at); $i <= date("Y"); $i++){
            $data['years'][] = $i;
        }
        $data['years'] = array_reverse($data['years']);

        $month = Input::get('month', '');
        $year = Input::get('year', '');
        if(($month > 0 & $month <= 12) && (ctype_digit($year) && mb_strlen($year) == 4)){
            $filter['month'] = $month;
            $filter['year'] = $year;
        }else
        {
            $filter['month'] = date('m');
            $filter['year'] = date('Y');
        }
        $data['filter'] = $filter;
        $data['days'] = array();
        for($i = 1; $i <= date("t", mktime(0, 0, 0, $filter['month'], 1, $filter['year'])); $i++)
        {
            
            $timestamp = mktime(0, 0, 0, $filter['month'], $i, $filter['year']);
            $data['days'][date('d.m.Y', $timestamp)] = array(
                'leads' => Model_Affiliate_Log::query()->where('type', '=', 'lead')->where('affiliate_id', '=', $this->user['uid'])->where(DB::expr('DAY(FROM_UNIXTIME(created_at))'), '=', date('d', $timestamp))->where(DB::expr('MONTH(FROM_UNIXTIME(created_at))'), '=', date('m', $timestamp))->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y', $timestamp))->count(),
                'earnings' => DB::select(array(DB::expr('SUM(affiliate_log.worth)'), 'sales'))->from('affiliate_log')->where(DB::expr('DAY(FROM_UNIXTIME(created_at))'), '=', date('d', $timestamp))->where('affiliate_id', '=', $this->user['uid'])->where(DB::expr('MONTH(FROM_UNIXTIME(created_at))'), '=', date('m', $timestamp))->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y', $timestamp))->execute()[0]['sales']
            );
            
        }
        
        if(Input::method() == 'POST')
        {
            
            $wallet_id = trim(Input::post('wallet_id', ''));
            if(empty($wallet_id))
            {
                \Messages::error('Bitte gebe deine Bitcoin Wallet Adresse an.');
                \Messages::redirect(Uri::current());
            }
            
            $ctx = stream_context_create(array('http' => array('timeout' => 10, 'ignore_errors' => true)));
            
            $response = file_get_contents('https://blockchain.info/it/q/addressbalance/'.$wallet_id, false, $ctx);

            if(!is_numeric($response))
            {
                \Messages::error('Du hast eine ungültige Bitcoin Wallet Adresse angegeben.');
                \Messages::redirect(Uri::current());
            }
            
            if($this->user['account_balance'] < 25.00)
            {
                \Messages::error('Du musst min. 25 Euro Guthaben haben um eine Auszahlung anfordern zu können.');
                \Messages::redirect(Uri::current());
            }
            
            $user = Model_User::query()->where('id', '=', $this->user['uid'])->get_one();
            
            $payout = new Model_Payout_Request;
            $payout->user_id = $this->user['uid'];
            $payout->amount = $user->account_balance;
            $payout->status = 'pending';
            $payout->wallet_address = $wallet_id;
            $payout->save();
            
            $user->account_balance = 0;
            $user->save();
            
            \Messages::success('Wir haben deinen Auszahlungsantrag erhalten und werden ihn bald bearbeiten.');
            \Messages::redirect(Uri::current());
            
        }
        
        return Response::forge(View::forge('cash/dashboard.html.twig', isset($data) ? $data : array(), false)); 
    
    }
    
}
