<?php
class Controller_Base_Public extends Controller_Base
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
	public function after($response)
	{
        parent::after($response);
        return $response;
	}
    
}