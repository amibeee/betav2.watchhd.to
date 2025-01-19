<?php
class Controller_Base_Api extends Controller_Base
{    
    
    /**
     * 
     */
	public function before()
	{
        parent::before();
	}

    /**
     *  
     */    
    public function response($data = array(), $http_status = 200)
    {
        return new Response(Format::forge($data)->to_json(), $http_status, array('Content-Type' => 'application/json'));    
    }

    /**
     *  
     */
	public function after($response)
	{
        parent::after($response);
        return $response;
	}
    
}