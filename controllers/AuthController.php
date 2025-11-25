<?php

require_once PATH_ROOT . 'utils/MailHelper.php';

class AuthController
{
    private UserModel $userModel;
    private PasswordResetModel $passwordResetModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->passwordResetModel = new PasswordResetModel();
    }

    public function showLogin(): void
    {
        $title = 'Đăng nhập';
        $view  = 'login';
        require_once PATH_VIEW . 'main.php';
    }

    public function showRegister(): void
    {
        $title = 'Đăng ký';
        $view  = 'register';
        require_once PATH_VIEW . 'main.php';
    }

    public function showForgotPassword(): void
    {
        $title = 'Quên mật khẩu';
        $view  = 'forgot-password';
        require_once PATH_VIEW . 'main.php';
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('show-register');
        }

        $firstname = trim($_POST['firstname'] ?? '');
        $lastname  = trim($_POST['lastname'] ?? '');
        $gender    = $_POST['gender'] ?? 'female';
        $birthday  = $_POST['birthday'] ?? '';
        $phone     = trim($_POST['phone'] ?? '');
        $address   = trim($_POST['address'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $password  = $_POST['password'] ?? '';

        $errors = [];

        if ($firstname === '') {
            $errors[] = 'Vui lòng nhập họ.';
        }

        if ($lastname === '') {
            $errors[] = 'Vui lòng nhập tên.';
        }

        if (!in_array($gender, ['female', 'male'], true)) {
            $errors[] = 'Giới tính không hợp lệ.';
        }

        if ($birthday === '') {
            $errors[] = 'Vui lòng chọn ngày sinh.';
        }

        if ($phone === '') {
            $errors[] = 'Vui lòng nhập số điện thoại.';
        }

        if ($address === '') {
            $errors[] = 'Vui lòng nhập địa chỉ.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Vui lòng nhập Gmail hợp lệ.';
        }

        if (strlen($password) < 6) {
            $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự.';
        }

        if ($this->userModel->findByEmail($email)) {
            $errors[] = 'Gmail đã được sử dụng.';
        }

        if ($errors) {
            $_SESSION['error'] = implode('<br>', $errors);
            $this->redirect('show-register');
        }

        try {
            $token   = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));

            $this->userModel->create([
                'first_name'      => $firstname,
                'last_name'       => $lastname,
                'gender'          => $gender,
                'birthday'        => $birthday,
                'phone'           => $phone,
                'address'         => $address,
                'email'           => $email,
                'password'        => password_hash($password, PASSWORD_BCRYPT),
                'session_token'   => $token,
                'session_expires' => $expires,
            ]);

            $verifyLink = BASE_URL . '?action=verify-account&token=' . urlencode($token);
            MailHelper::send(
                $email,
                'Xác thực tài khoản BonBonwear',
                "<p>Chào {$firstname},</p>
                 <p>Vui lòng nhấp vào liên kết bên dưới để xác thực tài khoản:</p>
                 <p><a href=\"{$verifyLink}\">Xác thực tài khoản</a></p>
                 <p>Liên kết có hiệu lực trong vòng 24 giờ.</p>"
            );

            $_SESSION['success'] = 'Đăng ký thành công. Vui lòng kiểm tra Gmail để xác thực tài khoản.';
            $this->redirect('show-login');
        } catch (Exception $e) {
            $_SESSION['error'] = 'Đăng ký thất bại, vui lòng thử lại.';
            $this->redirect('show-register');
        }
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('show-login');
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            $_SESSION['error'] = 'Vui lòng nhập Email và mật khẩu hợp lệ.';
            $this->redirect('show-login');
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['error'] = 'Email hoặc mật khẩu không đúng.';
            $this->redirect('show-login');
        }

        if (!empty($user['session_token'])) {
            $_SESSION['error'] = 'Tài khoản chưa được xác thực. Vui lòng kiểm tra Gmail.';
            $this->redirect('show-login');
        }

        $_SESSION['user'] = [
            'id'       => $user['user_id'],
            'fullname' => $user['full_name'] ?? trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')),
            'email'    => $user['email'],
            'role'     => $user['role'] ?? 'customer',
        ];

        $_SESSION['success'] = 'Đăng nhập thành công.';
        $this->redirect('/');
    }

    public function logout(): void
    {
        unset($_SESSION['user']);
        session_regenerate_id(true);
        $_SESSION['success'] = 'Bạn đã đăng xuất.';
        $this->redirect('/');
    }

    public function handleForgotPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('show-forgot');
        }

        $email = trim($_POST['email'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Vui lòng nhập Gmail hợp lệ.';
            $this->redirect('show-forgot');
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            $_SESSION['error'] = 'Không tìm thấy tài khoản với Gmail này.';
            $this->redirect('show-forgot');
        }

        $token   = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        $this->passwordResetModel->createToken((int)$user['user_id'], $token, $expires);

        $resetLink = BASE_URL . '?action=reset-password&token=' . urlencode($token);
        MailHelper::send(
            $email,
            'Đặt lại mật khẩu BonBonwear',
            "<p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu của bạn.</p>
             <p>Nhấp <a href=\"{$resetLink}\">vào đây</a> để tạo mật khẩu mới (hiệu lực trong 30 phút).</p>"
        );

        $_SESSION['success'] = 'Đã gửi hướng dẫn đặt lại mật khẩu. Vui lòng kiểm tra Gmail.';
        $this->redirect('show-login');
    }

    public function showResetPassword(): void
    {
        $token = $_GET['token'] ?? '';
        if ($token === '') {
            $_SESSION['error'] = 'Liên kết không hợp lệ.';
            $this->redirect('show-forgot');
        }

        $reset = $this->passwordResetModel->findValidToken($token);
        if (!$reset) {
            $_SESSION['error'] = 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.';
            $this->redirect('show-forgot');
        }

        $title = 'Đặt lại mật khẩu';
        $view  = 'reset-password';
        $token = $token;

        require_once PATH_VIEW . 'main.php';
    }

    public function resetPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('show-forgot');
        }

        $token    = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        if ($token === '') {
            $_SESSION['error'] = 'Thiếu token đặt lại mật khẩu.';
            $this->redirect('show-forgot');
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Mật khẩu phải có ít nhất 6 ký tự.';
            $this->redirect('reset-password&token=' . urlencode($token));
        }

        if ($password !== $confirm) {
            $_SESSION['error'] = 'Mật khẩu nhập lại không khớp.';
            $this->redirect('reset-password&token=' . urlencode($token));
        }

        $reset = $this->passwordResetModel->findValidToken($token);
        if (!$reset) {
            $_SESSION['error'] = 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.';
            $this->redirect('show-forgot');
        }

        $this->userModel->updatePassword((int)$reset['user_id'], password_hash($password, PASSWORD_BCRYPT));
        $this->passwordResetModel->markUsed((int)$reset['reset_id']);

        $_SESSION['success'] = 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập.';
        $this->redirect('show-login');
    }

    public function verifyAccount(): void
    {
        $token = $_GET['token'] ?? '';
        if ($token === '') {
            $_SESSION['error'] = 'Liên kết xác thực không hợp lệ.';
            $this->redirect('show-login');
        }

        $user = $this->userModel->findByToken($token);
        if (!$user) {
            $_SESSION['error'] = 'Liên kết xác thực không hợp lệ.';
            $this->redirect('show-login');
        }

        if (!empty($user['session_expires']) && strtotime($user['session_expires']) < time()) {
            $_SESSION['error'] = 'Liên kết xác thực đã hết hạn.';
            $this->redirect('show-login');
        }

        $this->userModel->updateVerificationStatus((int)$user['user_id']);
        $_SESSION['success'] = 'Xác thực tài khoản thành công. Bạn có thể đăng nhập.';
        $this->redirect('show-login');
    }

    private function redirect(string $action): void
    {
        $url = $action === '/' ? BASE_URL : BASE_URL . '?action=' . $action;
        header("Location: {$url}");
        exit;
    }
}

