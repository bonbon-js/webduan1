
<div class="auth-container">
    <div class="auth-form">
        <h1 class="auth-title">Đặt lại mật khẩu</h1>
        
        <?php if (isset($errors) && !empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <form method="POST" action="<?= BASE_URL ?>?action=reset-password">
            <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
            
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Mật khẩu mới" required>
            </div>
            
            <div class="form-group">
                <input type="password" name="confirm_password" class="form-control" placeholder="Xác nhận mật khẩu mới" required>
            </div>
            
            <button type="submit" class="btn-submit btn-reset-white">ĐẶT LẠI MẬT KHẨU</button>
            
            <a href="<?= BASE_URL ?>" class="back-link">
                <span>←</span>
                <span>Quay lại trang chủ</span>
            </a>
        </form>
    </div>
</div>

