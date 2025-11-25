
<div class="auth-container">
    <div class="auth-form">
        <h1 class="form-title">Thông tin cá nhân</h1>
        
        <?php if (isset($errors) && !empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <form method="POST" action="<?= BASE_URL ?>?action=profile">
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled>
                <small style="color: #666; font-size: 12px;">Email không thể thay đổi</small>
            </div>
            
            <div class="form-group">
                <label class="form-label">Họ và tên</label>
                <input type="text" name="name" class="form-control" placeholder="Nhập họ và tên" 
                       value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Số điện thoại</label>
                <input type="tel" name="phone" class="form-control" placeholder="Nhập số điện thoại" 
                       value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label">Địa chỉ</label>
                <textarea name="address" class="form-control" rows="3" placeholder="Nhập địa chỉ"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Vai trò</label>
                <div>
                    <span style="font-weight: 500;"><?= strtoupper($user['role'] ?? 'user') ?></span>
                    <span class="role-badge role-<?= $user['role'] ?? 'user' ?>">
                        <?= ($user['role'] ?? 'user') === 'admin' ? 'Quản trị viên' : 'Người dùng' ?>
                    </span>
                </div>
            </div>
            
            <button type="submit" class="btn-submit">CẬP NHẬT THÔNG TIN</button>
            
            <a href="<?= BASE_URL ?>" class="back-link">
                <span>←</span>
                <span>Quay lại trang chủ</span>
            </a>
        </form>
    </div>
</div>

