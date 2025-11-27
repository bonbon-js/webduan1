<style>
    .register-page {
        min-height: calc(100vh - 120px);
        padding: 60px 40px 90px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .register-grid {
        width: 100%;
        max-width: 1100px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 80px;
        align-items: center;
    }

    .register-hero h1 {
        font-size: clamp(3rem, 6vw, 4.5rem);
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 12px;
        letter-spacing: -1px;
        position: relative;
        display: inline-block;
        opacity: 0;
        transform: translateY(25px);
        animation: heroFloat 0.7s ease-out forwards;
    }

    .register-hero h1::after {
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

    .register-form {
        width: 100%;
    }

    .register-form label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .register-form input[type="text"],
    .register-form input[type="email"],
    .register-form input[type="password"],
    .register-form input[type="date"] {
        width: 100%;
        border: none;
        border-radius: 6px;
        background: #f1f5f9;
        height: 52px;
        padding: 0 18px;
        margin-bottom: 18px;
        font-size: 0.95rem;
    }

    .register-form input:focus {
        outline: 2px solid #f97316;
        background: #fff;
    }

    .gender-group {
        display: flex;
        gap: 30px;
        align-items: center;
        margin-bottom: 24px;
        color: #0f172a;
        font-size: 0.9rem;
    }

    .gender-group label {
        margin-bottom: 0;
        font-weight: 500;
    }

    .register-button {
        border: 2px solid #0f172a;
        border-radius: 999px;
        background: #fff;
        color: #0f172a;
        font-weight: 700;
        letter-spacing: 1px;
        padding: 14px 28px;
        margin-top: 20px;
        text-transform: uppercase;
        transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
    }

    .register-button:hover {
        background: #0f172a;
        color: #fff;
        transform: translateY(-1px);
    }

    .recaptcha-hint {
        font-size: 0.75rem;
        color: #94a3b8;
        margin-top: -10px;
        margin-bottom: 16px;
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

    @media (max-width: 768px) {
        .register-page {
            padding: 40px 20px 70px;
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
<?
require_once 'utils/MailHelper.php';

// Sau khi đăng ký thành công
MailHelper::sendRegistrationMail($email, $full_name);
?>
<section class="register-page">
    <div class="register-grid">
        <div class="register-hero">
            <h1>Tạo tài khoản</h1>
        </div>

        <div class="register-form">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger" style="background: #fee; border: 1px solid #fcc; color: #c33; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem;">
                    <?= $_SESSION['error'] ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success" style="background: #efe; border: 1px solid #cfc; color: #3c3; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem;">
                    <?= $_SESSION['success'] ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>?action=register" method="POST">
                <label for="firstname">Họ</label>
                <input type="text" id="firstname" name="firstname" placeholder="Nhập họ" required>

                <label for="lastname">Tên</label>
                <input type="text" id="lastname" name="lastname" placeholder="Nhập tên" required>

                <div class="gender-group">
                    <label class="d-flex align-items-center gap-1 mb-0">
                        <input type="radio" name="gender" value="female" checked> Nữ
                    </label>
                    <label class="d-flex align-items-center gap-1 mb-0">
                        <input type="radio" name="gender" value="male"> Nam
                    </label>
                </div>

                <label for="birthday">Ngày sinh</label>
<<<<<<< Updated upstream
                <input type="date" id="birthday" name="birthday" required>
=======
                <input type="date" id="birthday" name="birthday" max="<?= date('Y-m-d', strtotime('-18 years')) ?>" required>
                <small id="age-error" style="color: #dc3545; display: none; font-size: 0.8rem; margin-top: -15px; margin-bottom: 10px;">Bạn phải trên 18 tuổi để đăng ký tài khoản.</small>

                <label for="phone">Số điện thoại</label>
                <input type="tel" id="phone" name="phone" placeholder="0123456789" pattern="[0-9]{10,11}">

                <label for="address">Địa chỉ</label>
                <input type="text" id="address" name="address" placeholder="Nhập địa chỉ của bạn">
>>>>>>> Stashed changes

                <label for="email">Gmail</label>
                <input type="email" id="email" name="email" placeholder="tennguoidung@gmail.com" required>

                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" placeholder="Tạo mật khẩu" required>

                <p class="recaptcha-hint">This site is protected by reCAPTCHA.</p>

                <button class="register-button" type="submit">Đăng ký</button>
            </form>

            <a href="<?= BASE_URL ?>" class="back-link">← Quay lại trang chủ</a>
        </div>
    </div>
</section>

