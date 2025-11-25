<style>
    .reset-page {
        min-height: calc(100vh - 120px);
        padding: 60px 20px 80px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .reset-card {
        width: 100%;
        max-width: 480px;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: 48px;
        box-shadow: 0 30px 80px rgba(15, 23, 42, 0.08);
        background: #fff;
    }

    .reset-card h1 {
        font-size: 2.2rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 24px;
        position: relative;
        display: inline-block;
    }

    .reset-card h1::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: -8px;
        width: 100%;
        height: 4px;
        border-radius: 999px;
        background: linear-gradient(90deg, #0f172a, #f97316);
    }

    .reset-card label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .reset-card input {
        width: 100%;
        border: none;
        border-radius: 12px;
        background: #f1f5f9;
        height: 50px;
        padding: 0 16px;
        margin-bottom: 18px;
        font-size: 0.95rem;
    }

    .reset-card input:focus {
        outline: 2px solid #f97316;
        background: #fff;
    }

    .reset-button {
        width: 100%;
        border: 2px solid #0f172a;
        border-radius: 999px;
        background: #fff;
        color: #0f172a;
        font-weight: 700;
        letter-spacing: 1px;
        padding: 14px 20px;
        margin-top: 8px;
        text-transform: uppercase;
        transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
    }

    .reset-button:hover {
        background: #0f172a;
        color: #fff;
        transform: translateY(-1px);
    }
</style>

<section class="reset-page">
    <div class="reset-card">
        <h1>Đặt lại mật khẩu</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error'] ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>?action=reset-password-submit" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

            <label for="password">Mật khẩu mới</label>
            <input type="password" id="password" name="password" placeholder="Nhập mật khẩu mới" required>

            <label for="password_confirm">Nhập lại mật khẩu</label>
            <input type="password" id="password_confirm" name="password_confirm" placeholder="Xác nhận mật khẩu" required>

            <button type="submit" class="reset-button">Cập nhật mật khẩu</button>
        </form>
    </div>
</section>

