<?php

namespace App\Controllers;
use System\Controller;

/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/12/2017
 * Time: 3:38 PM
 */
class PostController extends Controller
{

    public function index()
    {
        $view = $this->view->render('admin/post');
            echo $view;
     //  return view('admin/post', compact('data'));
    }

}