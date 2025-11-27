<?php

class AdminPanelController
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
        
        $stats = [
            'total_users' => count($allUsers),
            'total_orders' => count($allOrders),
            'admin_count' => count(array_filter($allUsers, fn($u) => ($u['role'] ?? '') === 'admin')),
            'customer_count' => count(array_filter($allUsers, fn($u) => ($u['role'] ?? '') === 'customer')),
            'pending_orders' => count(array_filter($allOrders, fn($o) => ($o['status'] ?? '') === 'pending')),
            'processing_orders' => count(array_filter($allOrders, fn($o) => ($o['status'] ?? '') === 'processing')),
            'completed_orders' => count(array_filter($allOrders, fn($o) => ($o['status'] ?? '') === 'completed')),
        ];

        $title = 'Admin Panel';
        $view = 'admin/panel';

        require_once PATH_VIEW . 'main.php';
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

