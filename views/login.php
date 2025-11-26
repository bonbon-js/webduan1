<style>
    .login-page {
        min-height: calc(100vh - 120px);
        padding: 60px 20px 80px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 60px;
        width: 100%;
        max-width: 1100px;
        align-items: center;
    }

    .login-hero h1 {
        font-size: clamp(3rem, 6vw, 4.5rem);
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 16px;
        letter-spacing: -1px;
        position: relative;
        display: inline-block;
        opacity: 0;
        transform: translateY(25px);
        animation: heroFloat 0.7s ease-out forwards;
    }

    .login-hero h1::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: -12px;
        width: 100%;
        height: 6px;
        border-radius: 999px;
        background: linear-gradient(90deg, #0f172a, #f97316);
        transform: scaleY(0);
        transform-origin: bottom;
        animation: underlineRise 0.6s ease-out forwards 0.2s;
    }

    .login-hero p {
        color: #64748b;
        font-size: 1rem;
        max-width: 380px;
    }

    .login-card {
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        padding: 48px 42px;
        box-shadow: 0 30px 80px rgba(15, 23, 42, 0.08);
        background: #fff;
    }

    .login-card h2 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 32px;
    }

    .form-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .form-control {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 16px;
        font-size: 0.95rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .form-control:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.2);
    }

    .login-button {
        width: 100%;
        border: 2px solid #0f172a;
        border-radius: 999px;
        background: #fff;
        color: #0f172a;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 14px 18px;
        margin-top: 24px;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease, color 0.2s ease;
    }

    .login-button:hover {
        transform: translateY(-1px);
        background: #0f172a;
        color: #fff;
        box-shadow: 0 15px 35px rgba(15, 23, 42, 0.25);
    }

    .recaptcha-hint {
        font-size: 0.75rem;
        color: #94a3b8;
        margin-top: 16px;
    }

    .register-cta {
        text-align: center;
        margin-top: 18px;
        color: #475569;
        font-size: 0.85rem;
    }

    .register-cta span {
        display: block;
        text-transform: uppercase;
        letter-spacing: 3px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .register-cta a {
        font-size: 0.9rem;
        font-weight: 600;
        color: #f97316;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 3px;
    }

    .register-cta a:hover {
        color: #0f172a;
    }

    .forgot-link {
        display: inline-block;
        font-size: 0.85rem;
        font-weight: 600;
        color: #0f172a;
        text-decoration: none;
        margin-top: 8px;
    }

    .forgot-link:hover {
        color: #f97316;
    }
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 24px;
        font-size: 0.9rem;
        color: #0f172a;
        text-decoration: none;
    }

    .back-link:hover {
        color: #f97316;
    }

    .alert {
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 0.9rem;
        margin-bottom: 16px;
    }

    @media (max-width: 768px) {
        .login-card {
            padding: 32px;
        }
        .login-page {
            padding-top: 40px;
        }
    }

    @keyframes underlineRise {
        0% {
            transform: scaleY(0);
        }
        100% {
            transform: scaleY(1);
        }
    }

    @keyframes heroFloat {
        0% {
            opacity: 0;
            transform: translateY(25px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<section class="login-page">
    <div class="login-grid">
        <div class="login-hero">
            <h1>Đăng nhập</h1>
        </div>

        <div class="login-card">
            <h2>Thông tin tài khoản</h2>

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
                    <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required>
                </div>

                <div class="form-check mb-2">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label small text-muted" for="remember">Ghi nhớ lần đăng nhập này</label>
                </div>

                <p class="recaptcha-hint">This site is protected by reCAPTCHA.</p>

                <a href="<?= BASE_URL ?>?action=show-forgot" class="forgot-link">Quên mật khẩu?</a>

                <button type="submit" class="login-button">Đăng nhập</button>

                <div class="register-cta">
                    <span>Chưa có tài khoản?</span>
                    <a href="<?= BASE_URL ?>?action=show-register">Đăng ký ngay</a>
                </div>
            </form>

            <a href="<?= BASE_URL ?>" class="back-link">
                <span>←</span> Quay lại trang chủ
            </a>
        </div>
    </div>
</section>
