<?php

require_once PATH_MODEL . 'PostModel.php';

class HomeController
{
    public function index() 
    {
        $title = 'BonBonwear';
        $view  = 'home';
        $logoUrl = BASE_URL . 'assets/images/logo.png';
        $isLoggedIn = isset($_SESSION['user']) ? 'true' : 'false';

        // Lấy 3 tin tức nổi bật từ database
        $postModel = new PostModel();
        $news = $postModel->getFeaturedPosts(3);

        require_once PATH_VIEW . 'main.php';
    }
}