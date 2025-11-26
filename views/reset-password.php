<style>
    .reset-page {
        min-height: calc(100vh - 120px);
        padding: 60px 20px 80px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .reset-grid {
        width: 100%;
        max-width: 1000px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 70px;
        align-items: center;
    }

    .reset-hero h1 {
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

    .reset-hero h1::after {
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

    .reset-card {
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        padding: 48px 42px;
        box-shadow: 0 30px 80px rgba(15, 23, 42, 0.08);
        background: #fff;
    }

    .reset-card h2 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 24px;
    }

    .form-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 6px;
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

    .reset-button {
        width: 100%;
        border: 2px solid #0f172a;
        border-radius: 999px;
        background: #fff;
        color: #0f172a;
        font-weight: 700;
        letter-spacing: 1px;
        padding: 14px 18px;
        margin-top: 20px;
        text-transform: uppercase;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease, color 0.2s ease;
    }

    .reset-button:hover {
        transform: translateY(-1px);
        background: #0f172a;
        color: #fff;
        box-shadow: 0 15px 35px rgba(15, 23, 42, 0.25);
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 20px;
        font-size: 0.9rem;
        color: #0f172a;
        text-decoration: none;
    }

    .back-link:hover {
        color: #f97316;
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

    @media (max-width: 768px) {
        .reset-card {
            padding: 32px;
        }
    }
</style>

<section class="reset-page">
    <div class="reset-grid">
        <div class="reset-hero">
            <h1>Đặt lại mật khẩu</h1>
        </div>

        <div class="reset-card">
            <h2>Nhập OTP & mật khẩu mới</h2>

            <form action="<?= BASE_URL ?>?action=reset-password" method="POST">
                <input type="hidden" name="token" value="<?= htmlspecialchars($resetToken ?? '') ?>">

                <div class="mb-3">
                    <label class="form-label" for="otp">Mã OTP</label>
                    <input type="text" class="form-control" id="otp" name="otp" placeholder="Nhập mã 6 chữ số" required>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password">Mật khẩu mới</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Tạo mật khẩu mới" required>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password_confirmation">Nhập lại mật khẩu</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Nhập lại mật khẩu" required>
                </div>

                <button type="submit" class="reset-button">Cập nhật mật khẩu</button>
            </form>

            <a href="<?= BASE_URL ?>?action=show-login" class="back-link">← Quay lại đăng nhập</a>
        </div>
    </div>
</section>

