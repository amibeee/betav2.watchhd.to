<?php

class Controller_Welcome extends Controller
{
    public function action_index()
    {
        return Response::forge('Hello, World!');
    }
}

