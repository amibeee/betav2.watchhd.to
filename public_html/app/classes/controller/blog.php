<?php
class Controller_Blog extends Controller_Base_Public
{
    
    /**
     * 
     */
    public function action_index()
    {
        
        $data['posts'] = Model_Blog_Post::query()->order_by('created_at', 'DESC')->get();    
        
        
        return Response::forge(View::forge('blog.html.twig', isset($data) ? $data : array(), false));      
    
    }
    
    /**
     * 
     */
    public function action_post($slug)
    {
        
        $post = Model_Blog_Post::query()->where('slug', '=', $slug)->get_one();
        if(!$post)
        {
            throw new HttpNotFoundException;
        }
        
        if(Input::method() == 'POST' && $this->user['is_user'])
        {
            
            $input['comment'] = Input::post('comment', '');
            
            if(empty($input['comment']))
            {
                
                Session::set_flash('input.old', Input::post());
                
                \Messages::error('Empty Comments are not possible.');
                \Messages::redirect(Uri::current());
            
            }
            
            if(Model_Blog_Comment::query()->where('user_id', '=', $this->user['uid'])->where('created_at', '>', (time()-600))->count())
            {
                
                Session::set_flash('input.old', Input::post());
                
                \Messages::error('Please try sending your comment again in 10 Minutes.');
                \Messages::redirect(Uri::current());
            
            }
            
            $comment = new Model_Blog_Comment;
            $comment->blog_post_id = $post->id;
            $comment->user_id = $this->user['uid'];
            $comment->comment = $input['comment'];
            $comment->deleted = 0;
            $comment->edited = 0;
            $comment->save();
            
            \Messages::success('Comment saved.');
            \Messages::redirect(Uri::current());
        
        }
        
        $data['post'] = $post;
        $data['comments'] = DB::select('blog_comments.comment', 'blog_comments.created_at', 'users.username', 'blog_comments.deleted')->from('blog_comments')->join('users', 'LEFT')->on('blog_comments.user_id', '=', 'users.id')->where('blog_comments.blog_post_id', '=', $post->id)->order_by('blog_comments.created_at', 'DESC')->as_object()->execute();
        
        return Response::forge(View::forge('post.html.twig', isset($data) ? $data : array(), false));  
        
    }
    
}