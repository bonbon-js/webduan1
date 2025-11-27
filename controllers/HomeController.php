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
        
        // // Nếu không có tin tức nổi bật, dùng dữ liệu mẫu
        // if (empty($news)) {
        //     $news = [
        //         [
        //             'title'   => 'BST Polo Thu Đông 2025',
        //             'date'    => '15/11/2025',
        //             'excerpt' => 'Phong cách tối giản, chất liệu mềm mại cùng gam màu trung tính.',
        //             'image'   => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=600&q=80',
        //         ],
        //         [
        //             'title'   => 'Cách phối áo Polo đa dạng',
        //             'date'    => '12/11/2025',
        //             'excerpt' => 'Từ công sở đến dạo phố, Polo luôn phù hợp mọi hoàn cảnh.',
        //             'image'   => 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?auto=format&fit=crop&w=600&q=80',
        //         ],
        //         [
        //             'title'   => 'Chất liệu tái chế bền vững',
        //             'date'    => '08/11/2025',
        //             'excerpt' => 'BonBonwear tiên phong đưa cotton tái chế vào dòng sản phẩm chủ lực.',
        //             'image'   => 'https://images.unsplash.com/photo-1445205170230-053b83016050?auto=format&fit=crop&w=600&q=80',
        //         ],
        //     ];
        // }

        // $products = [
        //     [
        //         'id'       => 1,
        //         'name'     => 'Áo Polo Essential',
        //         'category' => 'Áo Polo',
        //         'price'    => 399000,
        //         'image'    => 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?auto=format&fit=crop&w=600&q=80',
        //     ],
        //     [
        //         'id'       => 2,
        //         'name'     => 'Áo Khoác Dệt Kim',
        //         'category' => 'Áo Khoác',
        //         'price'    => 659000,
        //         'image'    => 'https://images.unsplash.com/photo-1503341455253-b2e723bb3dbb?auto=format&fit=crop&w=600&q=80',
        //     ],
        //     [
        //         'id'       => 3,
        //         'name'     => 'Áo Hoodie Urban',
        //         'category' => 'Hoodie',
        //         'price'    => 549000,
        //         'image'    => 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?auto=format&fit=crop&w=600&q=80',
        //     ],
        //     [
        //         'id'       => 4,
        //         'name'     => 'Quần Kaki Slimfit',
        //         'category' => 'Quần',
        //         'price'    => 489000,
        //         'image'    => 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?auto=format&fit=crop&w=600&q=80',
        //     ],
        //     [
        //         'id'       => 5,
        //         'name'     => 'Áo Sơ Mi Cotton',
        //         'category' => 'Sơ mi',
        //         'price'    => 429000,
        //         'image'    => 'https://images.unsplash.com/photo-1475180098004-ca77a66827be?auto=format&fit=crop&w=600&q=80',
        //     ],
        //     [
        //         'id'       => 6,
        //         'name'     => 'Áo Polo Stripe',
        //         'category' => 'Áo Polo',
        //         'price'    => 419000,
        //         'image'    => 'https://images.unsplash.com/photo-1514996937319-344454492b37?auto=format&fit=crop&w=600&q=80',
        //     ],
        //     [
        //         'id'       => 7,
        //         'name'     => 'Áo Khoác Utility',
        //         'category' => 'Áo Khoác',
        //         'price'    => 699000,
        //         'image'    => 'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?auto=format&fit=crop&w=600&q=80',
        //     ],
        //     [
        //         'id'       => 8,
        //         'name'     => 'Quần Jean Darkwash',
        //         'category' => 'Quần',
        //         'price'    => 559000,
        //         'image'    => 'https://images.unsplash.com/photo-1475180098004-ca77a66827be?auto=format&fit=crop&w=600&q=80',
        //     ],
        // ];

        require_once PATH_VIEW . 'main.php';
    }
}