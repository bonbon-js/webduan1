<?php

class AdminStatsController
{
    private UserModel $userModel;
    private OrderModel $orderModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->orderModel = new OrderModel();
    }

    public function index(): void
    {
        $this->requireAdmin();

        // Lấy thống kê
        $allUsers = $this->userModel->getAll();
        $allOrders = $this->orderModel->getAll();
        
        // Tính toán thống kê
        $totalOrders = count($allOrders);
        $totalRevenue = $this->orderModel->getTotalRevenue();
        $totalUsers = count($allUsers);
        $monthlyRevenue = $this->orderModel->getMonthlyRevenue();
        $bestSellingProduct = $this->orderModel->getBestSellingProduct();
        $revenueByMonth = $this->orderModel->getRevenueByMonth();
        $productStats = $this->orderModel->getProductStats();
        $recentOrders = $this->orderModel->getRecentOrders(10);

        // Tính phần trăm thay đổi (tạm thời dùng số ngẫu nhiên)
        $stats = [
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'total_users' => $totalUsers,
            'best_selling_product' => $bestSellingProduct ? $bestSellingProduct['product_name'] : 'Chưa có',
            'monthly_revenue' => $monthlyRevenue,
            'revenue_by_month' => $revenueByMonth,
            'product_stats' => $productStats,
            'recent_orders' => $recentOrders,
            'order_change' => rand(-5, 5) / 10, // -0.5% đến +0.5%
            'revenue_change' => rand(-5, 5) / 10,
            'user_change' => rand(-5, 5) / 10,
            'product_change' => rand(-5, 5) / 10,
        ];

        $title = 'Thống kê';
        $view = 'admin/stats';
        $statusMap = OrderModel::statuses();

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

