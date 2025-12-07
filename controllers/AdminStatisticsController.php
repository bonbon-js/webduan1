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

        $fromDate = $_GET['from_date'] ?? null;
        $toDate   = $_GET['to_date'] ?? null;
        $preset   = $_GET['preset'] ?? 'today';

        // Preset time ranges
        $now = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
        switch ($preset) {
            case 'yesterday':
                $fromDate = $toDate = $now->modify('-1 day')->format('Y-m-d');
                break;
            case '7d':
                $toDate = $toDate ?: $now->format('Y-m-d');
                $fromDate = $fromDate ?: (new DateTime($toDate))->modify('-6 day')->format('Y-m-d');
                break;
            case 'this_month':
                $fromDate = $fromDate ?: $now->format('Y-m-01');
                $toDate = $toDate ?: $now->format('Y-m-t');
                break;
            case 'last_month':
                $firstDayLastMonth = (new DateTime('first day of last month'))->format('Y-m-d');
                $lastDayLastMonth = (new DateTime('last day of last month'))->format('Y-m-d');
                $fromDate = $fromDate ?: $firstDayLastMonth;
                $toDate = $toDate ?: $lastDayLastMonth;
                break;
            case 'quarter':
                $month = (int)$now->format('n');
                $quarter = (int)ceil($month / 3);
                $startMonth = ($quarter - 1) * 3 + 1;
                $fromDate = $fromDate ?: sprintf("%s-%02d-01", $now->format('Y'), $startMonth);
                $toDate = $toDate ?: (new DateTime($fromDate))->modify('+2 month')->format('Y-m-t');
                break;
            case 'today':
            default:
                $fromDate = $fromDate ?: $now->format('Y-m-d');
                $toDate = $toDate ?: $now->format('Y-m-d');
        }

        // Lấy thống kê tổng quan
        $stats = [
            'total_orders' => $this->orderModel->getTotalCount(),
            'total_revenue' => $this->orderModel->getTotalRevenue(),
            'total_users' => $this->userModel->getTotalCount(),
            'best_selling' => 'Chưa có', // Sẽ tính sau
        ];

        // Mặc định khung thời gian: hôm nay
        if (!$fromDate || !$toDate) {
            $fromDate = date('Y-m-d');
            $toDate   = date('Y-m-d');
        }

        // Thống kê theo range
        $rangeStats = $this->orderModel->getStatsByRange($fromDate, $toDate);
        $dailyRevenue = $this->orderModel->getDailyRevenue($fromDate, $toDate);
        $dailyOrders = $this->orderModel->getDailyOrders($fromDate, $toDate);
        $paymentBreakdown = $this->orderModel->getPaymentBreakdown($fromDate, $toDate);
        $statusCounts = $this->orderModel->getStatusCounts($fromDate, $toDate);
        $topCustomers = $this->orderModel->getTopCustomers($fromDate, $toDate, 5);
        $orderMetrics = $this->orderModel->getOrderMetrics($fromDate, $toDate);
        $topProducts = $this->productModel->getTopSelling($fromDate, $toDate, 5);

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
        $filterFrom = $fromDate;
        $filterTo = $toDate;
        $rangeStatsOrders = $rangeStats['orders'];
        $rangeStatsRevenue = $rangeStats['revenue'];
        $chartDailyRevenue = $dailyRevenue;
        $chartDailyOrders = $dailyOrders;
        $chartPayment = $paymentBreakdown;
        $chartStatusCounts = $statusCounts;
        $kpiOrderMetrics = $orderMetrics;
        $listTopProducts = $topProducts;
        $listTopCustomers = $topCustomers;

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

