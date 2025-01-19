<?php
class Controller_Help extends Controller_Base_Public
{
    
    /**
     * 
     */
    public function action_index()
    {
        
        $data['helpers'] = array();
        
        foreach(Model_Helper::query()->order_by('title', 'DESC')->get() as $helper)
        {
            $data['helpers'][$helper->category][] = $helper;
        }
            
        return Response::forge(View::forge('help.html.twig', isset($data) ? $data : array(), false));
    
    }
    
}