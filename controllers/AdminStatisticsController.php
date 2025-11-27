<?php

class AdminStatisticsController
{
    private OrderModel $orderModel;
    private ProductModel $productModel;
    private UserModel $userModel;

    public function __construct()
    {
        require_once PATH_MODEL . 'OrderModel.php';
        require_once PATH_MODEL . 'ProductModel.php';
        require_once PATH_MODEL . 'UserModel.php';
        
        $this->orderModel = new OrderModel();
        $this->productModel = new ProductModel();
        $this->userModel = new UserModel();
    }

    public function index(): void
    {
        $this->requireAdmin();

        // Lấy thống kê tổng quan
        $stats = [
            'total_orders' => $this->orderModel->getTotalCount(),
            'total_revenue' => $this->orderModel->getTotalRevenue(),
            'total_users' => $this->userModel->getTotalCount(),
            'best_selling' => 'Chưa có', // Sẽ tính sau
        ];

        // Lấy doanh thu theo tháng (12 tháng gần nhất)
        $monthlyRevenue = $this->orderModel->getMonthlyRevenue(12);

        // Lấy số lượng sản phẩm theo tháng
        $monthlyProducts = $this->productModel->getMonthlyProducts(12);

        // Lấy tổng sản phẩm
        $stats['total_products'] = $this->productModel->countAllProducts();

        // Lấy đơn hàng gần nhất
        $orders = $this->orderModel->getAll();

        $title = 'Thống kê';
        $view = 'admin/statistics/index';

        require_once PATH_VIEW . 'admin/layout.php';
    }

    private function requireAdmin(): void
    {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? null) !== 'admin') {
            set_flash('danger', 'Bạn cần quyền quản trị để truy cập trang này.');
            header('Location: ' . BASE_URL);
            exit;
        }
    }
}

