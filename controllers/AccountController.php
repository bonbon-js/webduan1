<?php

class AccountController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    protected function ensureAdmin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || (($_SESSION['user_role'] ?? '') !== 'admin')) {
            header('Location: ' . BASE_URL);
            exit;
        }
    }

    public function index()
    {
        $this->ensureAdmin();

        // đảm bảo token CSRF tồn tại cho các form admin
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // xử lý tìm kiếm và phân trang
        $q = trim($_GET['q'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $users = $this->userModel->getUsers($perPage, $offset, $q);
        $total = $this->userModel->countUsers($q);

        $title = 'Quản lý người dùng';
        $view = 'admin/users';
        require_once PATH_VIEW . 'main.php';
    }

    // Hiển thị form sửa user (GET)
    public function showEdit()
    {
        $this->ensureAdmin();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '?action=accounts');
            exit;
        }

        $user = $this->userModel->getUserById($id);
        if (!$user) {
            header('Location: ' . BASE_URL . '?action=accounts');
            exit;
        }

        // đảm bảo token CSRF
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $title = 'Sửa người dùng';
        $view = 'admin/user_edit';
        require_once PATH_VIEW . 'main.php';
    }

    // Hiển thị form tạo user
    public function create()
    {
        $this->ensureAdmin();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $title = 'Tạo người dùng mới';
        $view = 'admin/user_create';
        require_once PATH_VIEW . 'main.php';
    }

    // Xử lý lưu user mới (POST)
    public function store()
    {
        $this->ensureAdmin();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=accounts');
            exit;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $_SESSION['error'] = 'Yêu cầu không hợp lệ (CSRF).';
            header('Location: ' . BASE_URL . '?action=accounts');
            exit;
        }

        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $phone = trim($_POST['phone'] ?? '');
        $role = $_POST['role'] ?? 'user';

        $errors = [];
        if (empty($full_name)) $errors[] = 'Vui lòng nhập họ tên';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ';
        if (empty($password) || strlen($password) < 6) $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';

        // Kiểm tra email tồn tại
        if ($this->userModel->checkEmailExists($email)) {
            $errors[] = 'Email đã tồn tại';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: ' . BASE_URL . '?action=accounts-create');
            exit;
        }

        $this->userModel->createUser([
            'full_name' => $full_name,
            'email' => $email,
            'password' => $password,
            'phone' => $phone,
            'role' => $role
        ]);

        $_SESSION['success'] = 'Tạo user thành công';
        header('Location: ' . BASE_URL . '?action=accounts');
        exit;
    }

    // Xử lý cập nhật (POST)
    public function update()
    {
        $this->ensureAdmin();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=accounts');
            exit;
        }

        // Kiểm tra CSRF
        $token = $_POST['csrf_token'] ?? '';
        if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $_SESSION['error'] = 'Yêu cầu không hợp lệ (CSRF).';
            header('Location: ' . BASE_URL . '?action=accounts');
            exit;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '?action=accounts');
            exit;
        }

        $data = [
            'full_name' => trim($_POST['full_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'role' => ($_POST['role'] ?? 'user')
        ];

        $this->userModel->updateById($id, $data);

        header('Location: ' . BASE_URL . '?action=accounts');
        exit;
    }

    // Xử lý xóa (POST)
    public function delete()
    {
        $this->ensureAdmin();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=accounts');
            exit;
        }

        // Kiểm tra CSRF
        $token = $_POST['csrf_token'] ?? '';
        if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $_SESSION['error'] = 'Yêu cầu không hợp lệ (CSRF).';
            header('Location: ' . BASE_URL . '?action=accounts');
            exit;
        }

        $id = $_POST['id'] ?? null;
        if ($id) {
            $this->userModel->deleteUser($id);
        }

        header('Location: ' . BASE_URL . '?action=accounts');
        exit;
    }
}
