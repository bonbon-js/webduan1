<?php

class HomeController
{
    public function index() 
    {
        $title = 'Trang chủ';
        $view = 'home';
        require_once PATH_VIEW . 'main.php';
    }
}