    <?php
    class Controller_Ghru4pxsa3_Blog extends Controller_Base_Admin
    {
     
        /**
         *
         */
        public function action_index()
        {
     
            $data['posts'] = Model_Blog_Post::query()->order_by('created_at', 'DESC')->get();
     
            return Response::forge(View::forge('ghru4pxsa3/blog/list.html.twig', isset($data) ? $data : array(), false));
     
        }
     
        /**
         *
         */
        public function action_create()
        {
     
            if(Input::method() == 'POST')
            {
     
                $title = trim(Input::post('title', ''));
                $pop = trim(Input::post('pop', 'no'));
                $contents = trim(Input::post('contents', ''));
     
                $errors = array();
     
                # title
                if(empty($title))
                {
                    $errors[] = 'Bitte Titel angeben.';
                }
     
                # contents
                if(empty($contents))
                {
                    $errors[] = 'Bitte Beitrags Text angeben.';
                }
     
                if(count($errors) == 0)
                {
     
                    $post = new Model_Blog_Post;
                    $post->title = $title;
                    $post->pop = $pop;
                    $post->contents = $contents;
                    $post->save();
     
                    \Messages::success('Beitrag wurde erstellt.');
                    \Messages::redirect('ghru4pxsa3/blog');
     
                }
                else
                {
     
                    Session::set_flash('input.old', Input::post());
     
                    \Messages::error(implode('<br />', $errors));
                    \Messages::redirect(Uri::current());
     
                }
     
            }
     
            return Response::forge(View::forge('ghru4pxsa3/blog/create.html.twig', isset($data) ? $data : array(), false));
     
        }
     
        /**
         *
         */
        public function action_edit($post_id)
        {
     
            $post = Model_Blog_Post::query()->where('id', '=', $post_id)->get_one();
            if(!$post) throw new HttpNotFoundException;
     
            if(Input::method() == 'POST')
            {
     
                $title = trim(Input::post('title', ''));
                $pop = trim(Input::post('pop', 'no'));
                $contents = trim(Input::post('contents', ''));
     
                $errors = array();
     
                # title
                if(empty($title))
                {
                    $errors[] = 'Bitte Titel angeben.';
                }
     
                # contents
                if(empty($contents))
                {
                    $errors[] = 'Bitte Beitrags Text angeben.';
                }
     
                if(count($errors) == 0)
                {
     
                    $post->title = $title;
                    $post->pop = $pop;
                    $post->contents = $contents;
                    $post->save();
     
                    \Messages::success('Änderungen wurden übernommen.');
                    \Messages::redirect('ghru4pxsa3/blog');
     
                }
                else
                {
     
                    Session::set_flash('input.old', Input::post());
     
                    \Messages::error(implode('<br />', $errors));
                    \Messages::redirect(Uri::current());
     
                }
     
            }
     
            $data['post'] = $post;
     
            return Response::forge(View::forge('ghru4pxsa3/blog/edit.html.twig', isset($data) ? $data : array(), false));
     
        }
     
        /**
         *
         */
        public function post_delete($post_id)
        {
            $post_id = (int)$post_id;
     
  
     
            $post = Model_Blog_Post::query()->where('id', '=', $post_id)->get_one();
            if(!$post) throw new HttpNotFoundException;
     
            $post->delete();
     
            \Messages::success('Beitrag wurde gelöscht!');
            \Messages::redirect('ghru4pxsa3/blog');
     
        }
     
     
    }
     