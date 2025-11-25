<?php

class AuthController
{
    private $userModel;
    private $passwordResetModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->passwordResetModel = new PasswordResetModel();
    }

    // Hiển thị form đăng ký
    public function showRegister()
    {
        $title = 'Đăng ký';
        $view = 'auth/register';
        $errors = [];
        require_once PATH_VIEW . 'main.php';
    }

    // Xử lý đăng ký
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=register');
            exit;
        }

        $errors = [];

        // Validate dữ liệu
        $lastName = trim($_POST['last_name'] ?? '');
        $firstName = trim($_POST['first_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $birthday = trim($_POST['birthday'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        
        // Gộp họ và tên
        $name = trim($lastName . ' ' . $firstName);

        if (empty($lastName)) {
            $errors[] = 'Vui lòng nhập họ';
        }
        
        if (empty($firstName)) {
            $errors[] = 'Vui lòng nhập tên';
        }

        if (empty($email)) {
            $errors[] = 'Vui lòng nhập email';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        } elseif ($this->userModel->checkEmailExists($email)) {
            $errors[] = 'Email đã được sử dụng';
        }

        if (empty($password)) {
            $errors[] = 'Vui lòng nhập mật khẩu';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
        }

        if (empty($errors)) {
            try {
                // Determine role: default 'user', allow 'admin' only when correct admin key is provided
                $role = 'user';
                if (!empty($_POST['admin_key']) && defined('ADMIN_CREATION_KEY') && ADMIN_CREATION_KEY) {
                    // compare trimmed values
                    if (trim($_POST['admin_key']) === ADMIN_CREATION_KEY) {
                        $role = 'admin';
                    }
                }

                $this->userModel->register([
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                    'phone' => $phone,
                    'address' => $address,
                    'role' => $role
                ]);

                // Gửi email chào mừng
                require_once PATH_ROOT . 'utils/MailHelper.php';
                $emailResult = MailHelper::sendRegistrationMail($email, $name);
                if (!$emailResult['success']) {
                    $_SESSION['warning'] = 'Tài khoản đã được tạo nhưng chưa thể gửi email thông báo: ' . $emailResult['message'];
                    // Ghi log chi tiết để debug (có thể chứa debug output PHPMailer nếu SMTP_DEBUG=true)
                    error_log('MailHelper registration error: ' . $emailResult['message']);
                }

                // Tự động đăng nhập sau khi đăng ký
                $user = $this->userModel->login($email, $password);
                if ($user) {
                    $this->createSession($user);
                    $_SESSION['success'] = 'Đăng ký thành công!';
                    $redirectUrl = BASE_URL;
                    if (($user['role'] ?? 'user') === 'admin') {
                        $redirectUrl = BASE_URL . '?action=accounts';
                    }
                    header('Location: ' . $redirectUrl);
                    exit;
                }

                $_SESSION['success'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
                header('Location: ' . BASE_URL . '?action=login');
                exit;
            } catch (PDOException $e) {
                $errors[] = 'Đăng ký thất bại: ' . $e->getMessage();
                // Log lỗi để debug
                error_log("Register Error: " . $e->getMessage());
            } catch (Exception $e) {
                $errors[] = 'Đăng ký thất bại: ' . $e->getMessage();
                error_log("Register Error: " . $e->getMessage());
            }
        }

        $title = 'Đăng ký';
        $view = 'auth/register';
        require_once PATH_VIEW . 'main.php';
    }

    // Hiển thị form đăng nhập
    public function showLogin()
    {
        $title = 'Đăng nhập';
        $view = 'auth/login';
        $errors = [];
        require_once PATH_VIEW . 'main.php';
    }

    // Xử lý đăng nhập
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        $errors = [];

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email)) {
            $errors[] = 'Vui lòng nhập email';
        }

        if (empty($password)) {
            $errors[] = 'Vui lòng nhập mật khẩu';
        }

        if (empty($errors)) {
            try {
                $user = $this->userModel->login($email, $password);
                
                if ($user) {
                    $this->createSession($user);
                    $redirectUrl = BASE_URL;
                    if (($user['role'] ?? 'user') === 'admin') {
                        $redirectUrl = BASE_URL . '?action=accounts';
                    }
                    header('Location: ' . $redirectUrl);
                    exit;
                }

                $errors[] = 'Email hoặc mật khẩu không đúng';
            } catch (PDOException $e) {
                $errors[] = 'Lỗi đăng nhập: ' . $e->getMessage();
                error_log("Login Error: " . $e->getMessage());
            } catch (Exception $e) {
                $errors[] = 'Lỗi đăng nhập: ' . $e->getMessage();
                error_log("Login Error: " . $e->getMessage());
            }
        }

        $title = 'Đăng nhập';
        $view = 'auth/login';
        require_once PATH_VIEW . 'main.php';
    }

    // Tạo session và lưu token vào database
    private function createSession($user)
    {
        // Tạo session token
        $token = bin2hex(random_bytes(32));
        
        // Lưu token vào database
        $this->userModel->saveSessionToken($user['user_id'], $token);
        
        // Lưu vào session PHP
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'] ?? 'user';
        $_SESSION['session_token'] = $token;
    }

    // Đăng xuất
    public function logout()
    {
        // Xóa session token khỏi database
        if (isset($_SESSION['user_id'])) {
            $this->userModel->clearSessionToken($_SESSION['user_id']);
        }
        
        session_destroy();
        header('Location: ' . BASE_URL);
        exit;
    }

    // Hiển thị form quên mật khẩu
    public function showForgotPassword()
    {
        $title = 'Quên mật khẩu';
        $view = 'auth/forgot-password';
        $errors = [];
        $success = false;
        require_once PATH_VIEW . 'main.php';
    }

    // Xử lý quên mật khẩu
    public function forgotPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=forgot-password');
            exit;
        }

        $errors = [];
        $success = false;

        $email = trim($_POST['email'] ?? '');

        if (empty($email)) {
            $errors[] = 'Vui lòng nhập email';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        } else {
            $user = $this->userModel->getUserByEmail($email);
            
            if ($user) {
                // Tạo token reset password và lưu vào bảng password_resets
                $token = bin2hex(random_bytes(32));
                $this->passwordResetModel->createResetToken($user['user_id'], $token);

                // Gửi email reset password
                $resetLink = BASE_URL . "?action=reset-password&token=" . $token;
                
                require_once PATH_ROOT . 'utils/MailHelper.php';
                $fullName = $user['full_name'] ?? $user['email'];
                $result = MailHelper::sendResetPasswordMail($email, $fullName, $resetLink);
                
                if ($result['success']) {
                    $success = true;
                    $_SESSION['success'] = 'Email đặt lại mật khẩu đã được gửi! Vui lòng kiểm tra hộp thư của bạn.';
                } else {
                    // Nếu gửi email thất bại, vẫn hiển thị link để test
                    $_SESSION['reset_token'] = $token;
                    $errors[] = 'Không thể gửi email. Link đặt lại mật khẩu: ' . $resetLink;
                    error_log("Email send failed: " . $result['message']);
                }
            } else {
                $errors[] = 'Email không tồn tại trong hệ thống';
            }
        }

        $title = 'Quên mật khẩu';
        $view = 'auth/forgot-password';
        require_once PATH_VIEW . 'main.php';
    }

    // Hiển thị form reset password
    public function showResetPassword()
    {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            $_SESSION['error'] = 'Token không hợp lệ';
            header('Location: ' . BASE_URL . '?action=forgot-password');
            exit;
        }

        $resetData = $this->passwordResetModel->verifyResetToken($token);
        
        if (!$resetData) {
            $_SESSION['error'] = 'Token không hợp lệ hoặc đã hết hạn';
            header('Location: ' . BASE_URL . '?action=forgot-password');
            exit;
        }

        $title = 'Đặt lại mật khẩu';
        $view = 'auth/reset-password';
        $errors = [];
        require_once PATH_VIEW . 'main.php';
    }

    // Xử lý reset password
    public function resetPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL);
            exit;
        }

        $errors = [];

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($token)) {
            $errors[] = 'Token không hợp lệ';
        }

        if (empty($password)) {
            $errors[] = 'Vui lòng nhập mật khẩu mới';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Mật khẩu xác nhận không khớp';
        }

        if (empty($errors)) {
            // Xác thực token
            $resetData = $this->passwordResetModel->verifyResetToken($token);
            
            if ($resetData) {
                // Đặt lại mật khẩu
                $result = $this->userModel->resetPassword($resetData['user_id'], $password);
                
                if ($result) {
                    // Đánh dấu token đã sử dụng
                    $this->passwordResetModel->markTokenAsUsed($token);
                    
                    $_SESSION['success'] = 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập.';
                    header('Location: ' . BASE_URL . '?action=login');
                    exit;
                } else {
                    $errors[] = 'Đặt lại mật khẩu thất bại. Vui lòng thử lại.';
                }
            } else {
                $errors[] = 'Token không hợp lệ hoặc đã hết hạn';
            }
        }

        $title = 'Đặt lại mật khẩu';
        $view = 'auth/reset-password';
        // Đảm bảo token có sẵn trong GET để view có thể sử dụng
        if (empty($_GET['token']) && !empty($token)) {
            $_GET['token'] = $token;
        }
        require_once PATH_VIEW . 'main.php';
    }

    // Hiển thị trang thông tin cá nhân
    public function showProfile()
    {
        // Require authenticated session and verify session token from DB
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['session_token'])) {
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        $sessionUser = $this->userModel->getUserBySessionToken($_SESSION['session_token']);
        if (!$sessionUser || $sessionUser['user_id'] != $_SESSION['user_id']) {
            // Token invalid or does not match - force logout
            if (isset($_SESSION['user_id'])) {
                $this->userModel->clearSessionToken($_SESSION['user_id']);
            }
            session_unset();
            session_destroy();
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        $user = $this->userModel->getUserById($_SESSION['user_id']);

        $title = 'Thông tin cá nhân';
        $view = 'auth/profile';
        $errors = [];
        require_once PATH_VIEW . 'main.php';
    }

    // Cập nhật thông tin cá nhân
    public function updateProfile()
    {
        // Require authenticated session and verify session token from DB
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['session_token'])) {
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        $sessionUser = $this->userModel->getUserBySessionToken($_SESSION['session_token']);
        if (!$sessionUser || $sessionUser['user_id'] != $_SESSION['user_id']) {
            if (isset($_SESSION['user_id'])) {
                $this->userModel->clearSessionToken($_SESSION['user_id']);
            }
            session_unset();
            session_destroy();
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=profile');
            exit;
        }

        $errors = [];

        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if (empty($name)) {
            $errors[] = 'Vui lòng nhập họ tên';
        }

        if (empty($errors)) {
            try {
                $this->userModel->updateProfile($_SESSION['user_id'], [
                    'name' => $name,
                    'phone' => $phone,
                    'address' => $address
                ]);

                $_SESSION['user_name'] = $name;
                $_SESSION['success'] = 'Cập nhật thông tin thành công!';
                header('Location: ' . BASE_URL . '?action=profile');
                exit;
            } catch (Exception $e) {
                $errors[] = 'Cập nhật thông tin thất bại. Vui lòng thử lại.';
            }
        }

        $user = $this->userModel->getUserById($_SESSION['user_id']);
        $title = 'Thông tin cá nhân';
        $view = 'auth/profile';
        require_once PATH_VIEW . 'main.php';
    }

    // Quản lý tài khoản người dùng (chỉ admin)
    public function manageUsers()
    {
        $this->ensureAdminAccess();

        $keyword = trim($_GET['keyword'] ?? '');
        $users = $this->userModel->getAllUsers($keyword);

        $title = 'Quản lý tài khoản';
        $view = 'auth/manage-users';
        require_once PATH_VIEW . 'main.php';
    }

    /**
     * Đảm bảo user đã đăng nhập và có quyền admin
     */
    private function ensureAdminAccess()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để tiếp tục.';
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        if (($_SESSION['user_role'] ?? 'user') !== 'admin') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này.';
            header('Location: ' . BASE_URL);
            exit;
        }
    }
}
