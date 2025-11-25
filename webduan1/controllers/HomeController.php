<?php

class HomeController
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
    }

    public function index() 
    {
        $title = 'BonBonwear';
        $view  = 'home';
        $logoUrl = BASE_URL . 'assets/images/logo.png';
        $isLoggedIn = isset($_SESSION['user']) ? 'true' : 'false';

        // Lấy 8 sản phẩm mới nhất từ database
        $products = $this->productModel->getAllProducts(1, 8, ['order_by' => 'p.product_id', 'order_dir' => 'DESC']);
        
        // Nếu không có sản phẩm trong DB, dùng dữ liệu mẫu
        if (empty($products)) {
            $products = [
                [
                    'product_id'       => 1,
                    'product_name'     => 'Áo Polo Essential',
                    'category_name' => 'Áo Polo',
                    'price'    => 399000,
                    'primary_image'    => '',
                ],
                [
                    'product_id'       => 2,
                    'product_name'     => 'Áo Khoác Dệt Kim',
                    'category_name' => 'Áo Khoác',
                    'price'    => 659000,
                    'primary_image'    => '',
                ],
                [
                    'product_id'       => 3,
                    'product_name'     => 'Áo Hoodie Urban',
                    'category_name' => 'Hoodie',
                    'price'    => 549000,
                    'primary_image'    => '',
                ],
                [
                    'product_id'       => 4,
                    'product_name'     => 'Quần Kaki Slimfit',
                    'category_name' => 'Quần',
                    'price'    => 489000,
                    'primary_image'    => '',
                ],
                [
                    'product_id'       => 5,
                    'product_name'     => 'Áo Sơ Mi Cotton',
                    'category_name' => 'Sơ mi',
                    'price'    => 429000,
                    'primary_image'    => '',
                ],
                [
                    'product_id'       => 6,
                    'product_name'     => 'Áo Polo Stripe',
                    'category_name' => 'Áo Polo',
                    'price'    => 419000,
                    'primary_image'    => '',
                ],
                [
                    'product_id'       => 7,
                    'product_name'     => 'Áo Khoác Utility',
                    'category_name' => 'Áo Khoác',
                    'price'    => 699000,
                    'primary_image'    => '',
                ],
                [
                    'product_id'       => 8,
                    'product_name'     => 'Quần Jean Darkwash',
                    'category_name' => 'Quần',
                    'price'    => 559000,
                    'primary_image'    => '',
                ],
            ];
        }

        $news = [
            [
                'title'   => 'BST Polo Thu Đông 2025',
                'date'    => '15/11/2025',
                'excerpt' => 'Phong cách tối giản, chất liệu mềm mại cùng gam màu trung tính.',
                'image'   => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'title'   => 'Cách phối áo Polo đa dạng',
                'date'    => '12/11/2025',
                'excerpt' => 'Từ công sở đến dạo phố, Polo luôn phù hợp mọi hoàn cảnh.',
                'image'   => 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'title'   => 'Chất liệu tái chế bền vững',
                'date'    => '08/11/2025',
                'excerpt' => 'BonBonwear tiên phong đưa cotton tái chế vào dòng sản phẩm chủ lực.',
                'image'   => 'https://images.unsplash.com/photo-1445205170230-053b83016050?auto=format&fit=crop&w=600&q=80',
            ],
        ];

        require_once PATH_VIEW . 'main.php';
    }
}