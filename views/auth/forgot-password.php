
<div class="auth-container">
    <div class="auth-form">
        <h1 class="auth-title">Quên mật khẩu</h1>
        <p class="auth-subtitle">Nhập email của bạn để nhận link đặt lại mật khẩu</p>
        
        <?php if (isset($errors) && !empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <?php if (isset($success) && $success && isset($_SESSION['reset_token'])): ?>
            <div class="alert alert-success">
                Link đặt lại mật khẩu đã được gửi! Vui lòng kiểm tra email của bạn.
            </div>
            <div class="reset-token-info">
                <strong>Link đặt lại mật khẩu (để test):</strong>
                <a href="<?= BASE_URL ?>?action=reset-password&token=<?= $_SESSION['reset_token'] ?>">
                    <?= BASE_URL ?>?action=reset-password&token=<?= $_SESSION['reset_token'] ?>
                </a>
            </div>
            <?php unset($_SESSION['reset_token']); ?>
        <?php else: ?>
            <form method="POST" action="<?= BASE_URL ?>?action=forgot-password">
                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="Email" 
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
                
                <button type="submit" class="btn-submit">GỬI YÊU CẦU</button>
            </form>
        <?php endif; ?>
        
        <div class="login-link">
            Nhớ mật khẩu? <a href="<?= BASE_URL ?>?action=login">Đăng nhập ngay</a>
        </div>
        
        <a href="<?= BASE_URL ?>" class="back-link">
            <span>←</span>
            <span>Quay lại trang chủ</span>
        </a>
    </div>
</div>

