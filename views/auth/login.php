
<div class="auth-page-container">
    <div class="auth-content-wrapper">
        <div class="auth-left-panel">
            <h1 class="auth-title-large">Đăng nhập</h1>
            <div class="title-underline"></div>
        </div>
        
        <div class="auth-right-panel">
            <div class="auth-form">
                <?php if (isset($errors) && !empty($errors)): ?>
                    <?php foreach ($errors as $error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                
                <form method="POST" action="<?= BASE_URL ?>?action=login">
                    <div class="form-group">
                        <input type="email" name="email" class="form-control" placeholder="Email" 
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
                    </div>
                    
                    <a href="<?= BASE_URL ?>?action=forgot-password" class="forgot-password-link">
                        Quên mật khẩu?
                    </a>
                    
                    <div class="recaptcha-text">
                        This site protected by reCAPTCHA
                    </div>
                    
                    <button type="submit" class="btn-submit">ĐĂNG NHẬP</button>
                    
                    <a href="<?= BASE_URL ?>" class="back-link">
                        <span>←</span>
                        <span>Quay lại trang chủ</span>
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
