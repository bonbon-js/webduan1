<style>
    .forgot-page {
        min-height: calc(100vh - 120px);
        padding: 60px 40px 90px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .forgot-grid {
        width: 100%;
        max-width: 1000px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 80px;
        align-items: center;
    }

    .forgot-hero h1 {
        font-size: clamp(3rem, 6vw, 4.5rem);
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 12px;
        letter-spacing: -1px;
        position: relative;
        display: block;
        width: 100%;
        opacity: 0;
        transform: translateY(25px);
        animation: heroFloat 0.7s ease-out forwards;
    }

    .forgot-hero h1::after {
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

    .forgot-card {
        width: 100%;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        padding: 52px 44px;
        box-shadow: 0 30px 80px rgba(15, 23, 42, 0.08);
        background: #fff;
    }

    .forgot-card h2 {
        font-size: 1.15rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 18px;
    }

    .forgot-card label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .forgot-card input[type="email"] {
        width: 100%;
        border: none;
        border-radius: 12px;
        background: #f1f5f9;
        height: 54px;
        padding: 0 18px;
        font-size: 0.95rem;
    }

    .forgot-card input:focus {
        outline: 2px solid #f97316;
        background: #fff;
    }

    .forgot-button {
        width: 100%;
        border: 2px solid #0f172a;
        border-radius: 999px;
        background: #fff;
        color: #0f172a;
        font-weight: 700;
        letter-spacing: 1px;
        padding: 14px 28px;
        margin-top: 22px;
        text-transform: uppercase;
        transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
    }

    .forgot-button:hover {
        background: #0f172a;
        color: #fff;
        transform: translateY(-1px);
    }

    .recaptcha-hint {
        font-size: 0.75rem;
        color: #94a3b8;
        margin-top: 16px;
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
        .forgot-page {
            padding: 40px 20px 70px;
        }
        .forgot-card {
            padding: 36px;
        }
    }
</style>

<section class="forgot-page">
    <div class="forgot-grid">
        <div class="forgot-hero">
            <h1>Quên mật khẩu</h1>
        </div>

        <div class="forgot-card">
            <h2>Gửi yêu cầu đặt lại mật khẩu</h2>
            <form action="<?= BASE_URL ?>?action=forgot-password" method="POST">
                <label for="forgotEmail">Gmail</label>
                <input type="email" id="forgotEmail" name="email" placeholder="tennguoidung@gmail.com" required>

                <p class="recaptcha-hint">This site is protected by reCAPTCHA.</p>

                <button class="forgot-button" type="submit">Gửi yêu cầu</button>
            </form>

            <a href="<?= BASE_URL ?>?action=show-login" class="back-link">← Quay lại đăng nhập</a>
        </div>
    </div>
</section>

