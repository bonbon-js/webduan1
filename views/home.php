
<div class="home-container">
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="welcome-title">Chào mừng, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>!</div>
        <div class="welcome-message">Bạn đã đăng nhập thành công.</div>
        
        <div class="user-info">
            <h3>Thông tin tài khoản</h3>
            <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></p>
            <p><strong>Vai trò:</strong> <?= strtoupper($_SESSION['user_role'] ?? 'user') ?></p>
        </div>
        
        <div class="auth-buttons" style="margin-top: 30px;">
            <a href="<?= BASE_URL ?>?action=profile" class="auth-btn btn-primary">Thông tin cá nhân</a>
            <a href="<?= BASE_URL ?>?action=logout" class="auth-btn btn-outline">Đăng xuất</a>
        </div>
    <?php else: ?>
        <div class="welcome-title">Chào mừng đến với BonBon</div>
        <div class="welcome-message">Đăng ký hoặc đăng nhập để tiếp tục</div>
        
        <div class="auth-buttons">
            <a href="<?= BASE_URL ?>?action=register" class="auth-btn btn-primary">Đăng ký</a>
            <a href="<?= BASE_URL ?>?action=login" class="auth-btn btn-outline">Đăng nhập</a>
        </div>
    <?php endif; ?>
</div>

