<?php

$router->get('/login',  'Auth\LoginController@login')->name('login');
$router->post('/login',  'Auth\LoginController@login')->name('login.post');
$router->post('/logout',  'Auth\LoginController@login')->name('logout');
$router->get('/',                'UserController@index')->name('home');
$router->get('/posts/:text/:id', 'UserController@index');