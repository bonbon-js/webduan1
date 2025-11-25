<style>
    .auth-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        padding: 40px 20px;
    }

    .auth-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        max-width: 450px;
        width: 100%;
        padding: 50px 40px;
    }

    .auth-logo {
        text-align: center;
        margin-bottom: 40px;
    }

    .auth-logo h1 {
        font-size: 2rem;
        font-weight: 700;
        color: #000;
        letter-spacing: 2px;
        margin: 0;
    }

    .auth-logo p {
        color: #666;
        font-size: 14px;
        margin-top: 8px;
    }

    .auth-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #000;
        margin-bottom: 30px;
        text-align: center;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        font-size: 14px;
        margin-bottom: 8px;
    }

    .form-control {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 14px;
        transition: all 0.3s;
    }

    .form-control:focus {
        border-color: #000;
        box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.05);
    }

    .btn-auth {
        background: #000;
        color: #fff;
        border: 2px solid #000;
        border-radius: 30px;
        padding: 14px;
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
        width: 100%;
        transition: all 0.3s;
        margin-top: 20px;
    }

    .btn-auth:hover {
        background: #fff;
        color: #000;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .auth-divider {
        text-align: center;
        margin: 30px 0;
        position: relative;
    }

    .auth-divider::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background: #e0e0e0;
    }

    .auth-divider span {
        background: #fff;
        padding: 0 20px;
        position: relative;
        color: #999;
        font-size: 13px;
    }

    .auth-link {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
        color: #666;
    }

    .auth-link a {
        color: #000;
        font-weight: 600;
        text-decoration: none;
        border-bottom: 2px solid #000;
        transition: all 0.3s;
    }

    .auth-link a:hover {
        opacity: 0.7;
    }

    .alert {
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 14px;
        margin-bottom: 20px;
    }

    .back-home {
        text-align: center;
        margin-top: 30px;
    }

    .back-home a {
        color: #666;
        text-decoration: none;
        font-size: 14px;
        transition: color 0.3s;
    }

    .back-home a:hover {
        color: #000;
    }
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-logo">
            <h1>BONBONWEAR</h1>
            <p>Fashion for Everyone</p>
        </div>

        <h2 class="auth-title">Đăng Nhập</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error'] ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success'] ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>?action=login" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="your@email.com" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember" style="font-size: 14px; color: #666;">
                    Ghi nhớ đăng nhập
                </label>
            </div>

            <button type="submit" class="btn btn-auth">Đăng Nhập</button>
        </form>

        <div class="auth-link">
            Chưa có tài khoản? <a href="<?= BASE_URL ?>?action=register">Đăng ký ngay</a>
        </div>

        <div class="back-home">
            <a href="<?= BASE_URL ?>">← Quay lại trang chủ</a>
        </div>
    </div>
</div>
