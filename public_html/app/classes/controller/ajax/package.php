<?php 
class Controller_Ajax_Package extends Controller_Base_Ajax
{
    
    /**
     * 
     */
    public function post_action($user_package_id)
    {

        if($this->user['is_user'] == false)
        {
            return $this->response(array('success' => false, 'message' => 'Not authorized'));
        }

        $query = Model_User_Packet::query()->where('id', $user_package_id);
		if($this->user['is_admin'] == false){
			$query->where('user_id', $this->user['uid']);
		}
		$user_package = $query->get_one();
        if(!$user_package)
        {
            return $this->response(array('success' => false, 'message' => 'Package not found'));
        }

        $user_package->status = Input::post('active', false) == 'true' ?  'active' : 'inactive';
        $user_package->save();

        return $this->response(array(
            'success' => true, 
            'message' => '', 
            'data' => array(
                'new_status' => $user_package->status
            )
        )
        );

    }

}