<div class="auth-page-container">
    <div class="auth-content-wrapper">
        <div class="auth-left-panel">
            <h1 class="auth-title-large">Tạo tài khoản</h1>
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
                
                <?php if (isset($_SESSION['warning'])): ?>
                    <div class="alert alert-warning"><?= htmlspecialchars($_SESSION['warning']) ?></div>
                    <?php unset($_SESSION['warning']); ?>
                <?php endif; ?>
                
                <form method="POST" action="<?= BASE_URL ?>?action=register">
                    <!-- Họ -->
                    <div class="form-group">
                        <input type="text" name="last_name" class="form-control" placeholder="Họ" 
                               value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
                    </div>
                    
                    <!-- Tên -->
                    <div class="form-group">
                        <input type="text" name="first_name" class="form-control" placeholder="Tên" 
                               value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
                    </div>
                    <!-- Giới tính -->
                    <div class="gender-group">
                        <div class="gender-option">
                            <input type="radio" id="gender_female" name="gender" value="female" 
                                   <?= (isset($_POST['gender']) && $_POST['gender'] === 'female') ? 'checked' : (empty($_POST['gender']) ? 'checked' : '') ?>>
                            <label for="gender_female">Nữ</label>
                        </div>
                        <div class="gender-option">
                            <input type="radio" id="gender_male" name="gender" value="male"
                                   <?= (isset($_POST['gender']) && $_POST['gender'] === 'male') ? 'checked' : '' ?>>
                            <label for="gender_male">Nam</label>
                        </div>
                    </div>
                    
                    <!-- Ngày sinh -->
                    <div class="form-group">
                        <input type="date" name="birthday" class="form-control" 
                               value="<?= htmlspecialchars($_POST['birthday'] ?? '') ?>"
                               max="<?= date('Y-m-d') ?>">
                    </div>
                    
                    <!-- Email -->
                    <div class="form-group">
                        <input type="email" name="email" class="form-control" placeholder="Email" 
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                    
                    <!-- Mật khẩu -->
                    <div class="form-group">
                        <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
                    </div>
                    
                    <div class="recaptcha-text">
                        This site is protected by reCAPTCHA and the Google Privacy Policy and Terms of Service apply.
                    </div>
                    
                    <button type="submit" class="btn-submit">ĐĂNG KÝ</button>
                    
                    <a href="<?= BASE_URL ?>" class="back-link">
                        <span>←</span>
                        <span>Quay lại trang chủ</span>
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
