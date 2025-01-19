<?php
class Controller_Ghru4pxsa3_Dashboard extends Controller_Base_Admin
{
 
    /**
     *  
     */    
    public function action_index()
    {  
       
        $data['stats']['signups']['today'] = Model_User::query()->where(DB::expr('DAY(FROM_UNIXTIME(created_at))'), '=', date('d'))->where(DB::expr('MONTH(FROM_UNIXTIME(created_at))'), '=', date('m'))->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y'))->count();
        $data['stats']['signups']['week'] = Model_User::query()->where(DB::expr('WEEK(FROM_UNIXTIME(created_at))'), '=', date('W'))->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y'))->count();
        $data['stats']['signups']['month'] = Model_User::query()->where(DB::expr('MONTH(FROM_UNIXTIME(created_at))'), '=', date('m'))->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y'))->count();
       
        $data['stats']['sales']['today'] = DB::select(array(DB::expr('SUM(user_payments.amount)'), 'sales'))->from('user_payments')->where('status', '=', 'paid')->where(DB::expr('DAY(FROM_UNIXTIME(created_at))'), '=', date('d'))->where(DB::expr('MONTH(FROM_UNIXTIME(created_at))'), '=', date('m'))->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y'))->execute()[0]['sales'];
        $data['stats']['sales']['week'] = DB::select(array(DB::expr('SUM(user_payments.amount)'), 'sales'))->from('user_payments')->where('status', '=', 'paid')->where(DB::expr('WEEK(FROM_UNIXTIME(created_at))'), '=', date('W'))->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y'))->execute()[0]['sales'];
        $data['stats']['sales']['month'] = DB::select(array(DB::expr('SUM(user_payments.amount)'), 'sales'))->from('user_payments')->where('status', '=', 'paid')->where(DB::expr('MONTH(FROM_UNIXTIME(created_at))'), '=', date('m'))->where(DB::expr('YEAR(FROM_UNIXTIME(created_at))'), '=', date('Y'))->execute()[0]['sales'];
       
        $data['stats']['users']['total'] = Model_User::query()->count();
        $data['stats']['users']['active'] = Model_User::query()->where('last_login', '>', (time()-604800))->count();
       
        if(Input::method() == 'POST'){
           
            if(Input::post('maintenance', '') == '1'){
                file_put_contents(DOCROOT.'maintenance.dat', '1');
            }
            else{
                file_put_contents(DOCROOT.'maintenance.dat', '0');
            }
           
            if(Input::post('paysafecard_allowed', null) == '1'){
                file_put_contents(DOCROOT.'paysafecard_allowed.dat', '1');
            }
            
			if(Input::post('paysafecard_allowed', null) == '0'){
                file_put_contents(DOCROOT.'paysafecard_allowed.dat', '0');
            }
           
            if(Input::post('amazon_allowed', null) == '1'){
                file_put_contents(DOCROOT.'amazon_allowed.dat', '1');
            }
			
            if(Input::post('amazon_allowed', null) == '0'){
                file_put_contents(DOCROOT.'amazon_allowed.dat', '0');
            }
           
        }
       
        if(file_get_contents(DOCROOT.'paysafecard_allowed.dat') == '1'){
            $data['paysafecard_allowed'] = 1;
        }
        else{
            $data['paysafecard_allowed'] = 0;
        }
       
        if(file_get_contents(DOCROOT.'amazon_allowed.dat') == '1'){
            $data['amazon_allowed'] = 1;
        }
        else{
            $data['amazon_allowed'] = 0;
        }
       
        if(file_get_contents(DOCROOT.'maintenance.dat') == '1'){
            $data['maintenance'] = 1;
        }
        else{
            $data['maintenance'] = 0;
        }
           
        return Response::forge(View::forge('ghru4pxsa3/dashboard.html.twig', isset($data) ? $data : array(), false));
   
    }
   
}