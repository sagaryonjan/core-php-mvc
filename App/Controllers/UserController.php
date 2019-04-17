<?php

namespace App\Controllers;
use System\Controller;
use System\Database;

/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/12/2017
 * Time: 3:38 PM
 */
class UserController extends Controller
{

    public function index()
    {

        //$user = $this->db->select('*')->from('user')->first();



        $user = $this->db->select('*')->from('user')->get();
        dd($user);



     /*   dd(   $this->db->data([
            'name' => 'sagar',
            'email' => 'hello@gmail.com',
        ])->insert('user'));*/

    /*    dd( $this->db->data([
            'name' => 'sagar',
            'email' => 'hero@gmail.com',
        ])->where('id = ?', 9)->insert('user'));*/



        $data = [];
        $data['title'] = 'Hello';
        $data['description'] = 'Desction';

        $view = $this->view->render('admin/post', $data);

        return $view;
    }

}