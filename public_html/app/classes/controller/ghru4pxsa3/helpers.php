<?php
class Controller_Ghru4pxsa3_Helpers extends Controller_Base_Admin
{

    /**
     *  
     */     
    public function action_index()
    {   
        
        $data['helpers'] = Model_Helper::query()->order_by('id', 'DESC')->get();    
            
        return Response::forge(View::forge('ghru4pxsa3/helpers/list.html.twig', isset($data) ? $data : array(), false)); 
    
    }
    
    /**
     * 
     */
    public function action_create()
    {
        
        if(Input::method() == 'POST')
        {
            
            $title = trim(Input::post('title', ''));
            $text = trim(Input::post('text', ''));
            $category = trim(Input::post('category', ''));
        
            $errors = array();
        
            # title
            if(empty($title))
            {
                $errors[] = 'Bitte Titel angeben.';    
            }
            
            # text
            if(empty($text))
            {
                $errors[] = 'Bitte Text angeben.';
            }
            
            # category
            if(empty($category))
            {
                $errors[] = 'Bitte Kategorie angeben.';
            }
            
            if(count($errors) == 0)
            {
                
                $helper = new Model_Helper;
                $helper->title = $title;
                $helper->text = $text;
                $helper->category = $category;
                $helper->save();
                
                \Messages::success('Hilfe Beitrag wurde erstellt.');
                \Messages::redirect('ghru4pxsa3/helpers');
                
            }
            else
            {
                
                Session::set_flash('input.old', Input::post());
                
                \Messages::error(implode('<br />', $errors));
                \Messages::redirect(Uri::current());
                
            }
            
        }
        
        return Response::forge(View::forge('ghru4pxsa3/helpers/create.html.twig', isset($data) ? $data : array(), false)); 
        
    }
    
    /**
     * 
     */
    public function action_edit($helper_id)
    {
        
        $helper = Model_Helper::query()->where('id', '=', $helper_id)->get_one();
        if(!$helper) throw new HttpNotFoundException;
        
        if(Input::method() == 'POST')
        {
            
            $title = trim(Input::post('title', ''));
            $text = trim(Input::post('text', ''));
            $category = trim(Input::post('category', ''));
        
            $errors = array();
        
            # title
            if(empty($title))
            {
                $errors[] = 'Bitte Titel angeben.';    
            }
            
            if(!empty($title) && Model_Helper::query()->where('title', '=', $title)->where('id', '!=', $helper_id)->count())
            {
                $errors[] = 'Es gibt schon einen Hilfe Beitrag mit diesem Titel';
            }
            
            # text
            if(empty($text))
            {
                $errors[] = 'Bitte Text angeben.';
            }
            
            # category
            if(empty($category))
            {
                $errors[] = 'Bitte Kategorie angeben.';
            }
            
            if(count($errors) == 0)
            {
                
                $helper = new Model_Helper;
                $helper->title = $title;
                $helper->text = $text;
                $helper->category = $category;
                $helper->save();
                
                \Messages::success('Änderungen wurden übernommen.');
                \Messages::redirect('ghru4pxsa3/helpers');
                
            }
            else
            {
                
                Session::set_flash('input.old', Input::post());
                
                \Messages::error(implode('<br />', $errors));
                \Messages::redirect(Uri::current());
                
            }
            
        }
        
        $data['helper'] = $helper;
        
        return Response::forge(View::forge('ghru4pxsa3/helpers/edit.html.twig', isset($data) ? $data : array(), false)); 
        
    }

    /**
     * 
     */
    public function post_delete($helper_id)
    {
        
        
 
        
        $helper = Model_Helper::query()->where('id', '=', $helper_id)->get_one();
        if(!$helper) throw new HttpNotFoundException;
         
        $helper->delete();
        
        \Messages::success('Hilfe Beitrag wurde gelöscht!');
        \Messages::redirect('ghru4pxsa3/helpers');
        
    }
    
    
}