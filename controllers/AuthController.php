<?php

require_once PATH_MODEL . 'CartModel.php';

class AuthController
{
    private UserModel $userModel;
    private PasswordResetModel $passwordResetModel;
    private CartModel $cartModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->passwordResetModel = new PasswordResetModel();
        $this->cartModel = new CartModel();
    }

    /**
     * Hiển thị trang đăng nhập
     */
    public function showLogin(): void
    {
        $title = 'Đăng nhập';
        $view  = 'login';

        require_once PATH_VIEW . 'main.php';
    }

    /**
     * Trang đăng ký tài khoản
     */
    public function showRegister(): void
    {
        $title = 'Đăng ký';
        $view  = 'register';

        require_once PATH_VIEW . 'main.php';
    }

    /**
     * Trang quên mật khẩu
     */
    public function showForgotPassword(): void
    {
        $title = 'Quên mật khẩu';
        $view  = 'forgot-password';

        require_once PATH_VIEW . 'main.php';
    }

    /**
     * Xử lý đăng ký tài khoản
     */
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
        } else {
            // Kiểm tra tuổi trên 18
            $birthdayDate = new DateTime($birthday);
            $today = new DateTime();
            $age = $today->diff($birthdayDate)->y;
            
            if ($age < 18) {
                $errors[] = 'Bạn phải trên 18 tuổi để đăng ký tài khoản.';
            }
        }

        if ($phone === '') {
            $errors[] = 'Vui lòng nhập số điện thoại.';
        } elseif (!preg_match('/^[0-9]{10,11}$/', $phone)) {
            $errors[] = 'Số điện thoại phải có 10 hoặc 11 chữ số.';
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
            $userId = $this->userModel->create([
                'first_name' => $firstname,
                'last_name'  => $lastname,
                'gender'     => $gender,
                'birthday'   => $birthday,
                'phone'      => $phone,
                'address'    => $address,
                'email'      => $email,
                'password'   => password_hash($password, PASSWORD_BCRYPT),
            ]);

            $token     = $this->generateToken();
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 day'));
            $this->userModel->setVerificationToken($userId, $token, $expiresAt);

            $sent = $this->sendVerificationEmail($email, trim("$firstname $lastname"), $token);

            if (!$sent) {
                $_SESSION['error'] = 'Tạo tài khoản thành công nhưng chưa gửi được email xác thực. Vui lòng thử lại sau.';
                $this->redirect('show-login');
            }

            $_SESSION['success'] = 'Đăng ký thành công. Vui lòng kiểm tra Gmail để xác thực tài khoản.';
            $this->redirect('show-login');
        } catch (Exception $e) {
            $_SESSION['error'] = 'Đăng ký thất bại, vui lòng thử lại.';
            $this->redirect('show-register');
        }
    }

    /**
     * Xử lý đăng nhập
     */
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
            'id'       => $user['user_id'] ?? ($user['id'] ?? null),
            'fullname' => $user['full_name'] ?? trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')),
            'email'    => $user['email'],
            'role'     => $user['role'] ?? 'customer',
        ];

        $userId = (int)$_SESSION['user']['id'];
        
        // Lưu giỏ hàng session vào database nếu có
        $sessionCart = $_SESSION['cart'] ?? [];
        if (!empty($sessionCart)) {
            $this->cartModel->syncSessionCartToDatabase($userId, $sessionCart);
        }
        
        // Khôi phục giỏ hàng từ database (đã sync session cart vào DB rồi nên chỉ load từ DB)
        $this->cartModel->loadCartToSession($userId, false);

        $_SESSION['success'] = 'Đăng nhập thành công.';
        $this->redirect('/');
    }

    /**
     * Đăng xuất
     */
    public function logout(): void
    {
        unset($_SESSION['user']);
        // Xóa giỏ hàng trong session khi đăng xuất
        unset($_SESSION['cart']);
        session_regenerate_id(true);
        $_SESSION['success'] = 'Bạn đã đăng xuất.';
        $this->redirect('/');
    }

    public function verifyAccount(): void
    {
        $token = $_GET['token'] ?? '';

        if ($token === '') {
            $_SESSION['error'] = 'Liên kết xác thực không hợp lệ.';
            $this->redirect('show-login');
        }

        $user = $this->userModel->findByVerificationToken($token);

        if (!$user) {
            $_SESSION['error'] = 'Liên kết xác thực đã hết hạn hoặc không tồn tại.';
            $this->redirect('show-login');
        }

        if (!empty($user['session_expires']) && strtotime($user['session_expires']) < time()) {
            $_SESSION['error'] = 'Liên kết xác thực đã hết hạn. Vui lòng đăng nhập để gửi lại.';
            $this->redirect('show-login');
        }

        $this->userModel->markVerified((int)$user['user_id']);
        $_SESSION['success'] = 'Tài khoản đã được kích hoạt. Vui lòng đăng nhập.';
        $this->redirect('show-login');
    }

    /**
     * Gửi email quên mật khẩu + tạo token
     */
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

        if (!empty($user['session_token'])) {
            $_SESSION['error'] = 'Tài khoản chưa được xác thực. Không thể đặt lại mật khẩu.';
            $this->redirect('show-login');
        }

        $token     = $this->generateToken();
        $otp       = $this->generateOtp();
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));

        $this->passwordResetModel->create((int)$user['user_id'], $token, $otp, $expiresAt);

        $sent = $this->sendForgotEmail($email, $user['full_name'] ?? $user['email'], $token, $otp);

        if (!$sent) {
            $_SESSION['error'] = 'Không thể gửi email đặt lại mật khẩu. Vui lòng thử lại sau.';
            $this->redirect('show-forgot');
        }

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

        $record = $this->passwordResetModel->findValidToken($token);

        if (!$record || (int)($record['is_used'] ?? 0) === 1 || strtotime($record['expires_at']) < time()) {
            $_SESSION['error'] = 'Liên kết đặt lại mật khẩu đã hết hạn hoặc không tồn tại.';
            $this->redirect('show-forgot');
        }

        $title = 'Đặt lại mật khẩu';
        $view  = 'reset-password';
        $resetToken = $token;

        require_once PATH_VIEW . 'main.php';
    }

    public function resetPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('show-login');
        }

        $token       = $_POST['token'] ?? '';
        $otp         = trim($_POST['otp'] ?? '');
        $password    = $_POST['password'] ?? '';
        $confirmation = $_POST['password_confirmation'] ?? '';

        if ($token === '' || $otp === '') {
            $_SESSION['error'] = 'Thông tin không hợp lệ.';
            $this->redirect('show-forgot');
        }

        $record = $this->passwordResetModel->findValidToken($token);

        if (
            !$record ||
            (int)($record['is_used'] ?? 0) === 1 ||
            strtotime($record['expires_at']) < time()
        ) {
            $_SESSION['error'] = 'Liên kết đặt lại mật khẩu đã hết hạn.';
            $this->redirect('show-forgot');
        }

        if ($otp !== ($record['otp_code'] ?? '')) {
            $_SESSION['error'] = 'Mã OTP không chính xác.';
            $this->redirect('show-reset-password&token=' . urlencode($token));
        }

        if ($password !== $confirmation || strlen($password) < 6) {
            $_SESSION['error'] = 'Mật khẩu phải trùng khớp và có ít nhất 6 ký tự.';
            $this->redirect('show-reset-password&token=' . urlencode($token));
        }

        $this->userModel->updatePassword((int)$record['user_id'], password_hash($password, PASSWORD_BCRYPT));
        $this->passwordResetModel->markUsed((int)$record['reset_id']);

        $_SESSION['success'] = 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập.';
        $this->redirect('show-login');
    }

    private function redirect(string $action): void
    {
        $url = $action === '/' ? BASE_URL : BASE_URL . '?action=' . $action;
        header("Location: {$url}");
        exit;
    }

    private function generateToken(int $length = 64): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    private function generateOtp(): string
    {
        return str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function sendVerificationEmail(string $email, string $name, string $token): bool
    {
        $link = BASE_URL . '?action=verify-account&token=' . urlencode($token);

        $html = "
            <p>Xin chào {$name},</p>
            <p>Cảm ơn bạn đã đăng ký BonBonWear. Nhấn vào nút bên dưới để xác thực tài khoản:</p>
            <p><a href=\"{$link}\" style=\"display:inline-block;padding:12px 20px;background:#000;color:#fff;text-decoration:none;border-radius:4px\">Xác thực tài khoản</a></p>
            <p>Nếu bạn không thực hiện đăng ký này, vui lòng bỏ qua email.</p>
        ";

        return send_mail($email, '[BonBonWear] Xác thực tài khoản', $html, $name);
    }

    private function sendForgotEmail(string $email, string $name, string $token, string $otp): bool
    {
        $link = BASE_URL . '?action=show-reset-password&token=' . urlencode($token);

        $html = "
            <p>Xin chào {$name},</p>
            <p>Chúng tôi đã nhận yêu cầu đặt lại mật khẩu cho tài khoản BonBonWear của bạn.</p>
            <p><strong>Mã OTP:</strong> {$otp}</p>
            <p>Nhấn vào liên kết bên dưới để đặt lại mật khẩu trong vòng 30 phút:</p>
            <p><a href=\"{$link}\" style=\"display:inline-block;padding:12px 20px;background:#000;color:#fff;text-decoration:none;border-radius:4px\">Đặt lại mật khẩu</a></p>
            <p>Nếu bạn không yêu cầu, hãy bỏ qua email này.</p>
        ";

        return send_mail($email, '[BonBonWear] Đặt lại mật khẩu', $html, $name);
    }
}

