<?php
if (!isset($user)) {
    header('Location: ' . BASE_URL);
    exit;
}
?>

<style>
    .profile-page {
        min-height: calc(100vh - 120px);
        padding: 60px 40px 90px;
        background: #f8f9fa;
    }

    .profile-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .profile-header {
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }

    .profile-header h1 {
        font-size: 2rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 10px;
    }

    .profile-header p {
        color: #64748b;
        margin: 0;
    }

    .profile-form {
        background: #fff;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .profile-form .form-group {
        margin-bottom: 24px;
    }

    .profile-form label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .profile-form input[type="text"],
    .profile-form input[type="email"],
    .profile-form input[type="tel"],
    .profile-form input[type="date"],
    .profile-form textarea {
        width: 100%;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        background: #fff;
        height: 48px;
        padding: 0 16px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .profile-form textarea {
        height: 100px;
        padding: 12px 16px;
        resize: vertical;
    }

    .profile-form input:focus,
    .profile-form textarea:focus {
        outline: none;
        border-color: #f97316;
        background: #fff;
    }

    .gender-group {
        display: flex;
        gap: 30px;
        align-items: center;
    }

    .gender-group label {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 0;
        font-weight: 500;
        cursor: pointer;
    }

    .gender-group input[type="radio"] {
        width: auto;
        height: auto;
        margin: 0;
        cursor: pointer;
    }

    .profile-actions {
        display: flex;
        gap: 16px;
        margin-top: 32px;
        padding-top: 32px;
        border-top: 2px solid #e2e8f0;
    }

    .btn-save {
        background: #0f172a;
        color: #fff;
        border: 2px solid #0f172a;
        border-radius: 8px;
        padding: 12px 32px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .btn-save:hover {
        background: #1e293b;
        border-color: #1e293b;
        transform: translateY(-1px);
    }

    .btn-cancel {
        background: #fff;
        color: #0f172a;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 32px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-block;
    }

    .btn-cancel:hover {
        border-color: #0f172a;
        background: #f8f9fa;
    }

    .info-badge {
        display: inline-block;
        background: #f1f5f9;
        color: #475569;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        margin-top: 4px;
    }

    @media (max-width: 768px) {
        .profile-page {
            padding: 30px 20px 60px;
        }

        .profile-form {
            padding: 24px;
        }

        .profile-actions {
            flex-direction: column;
        }

        .btn-save,
        .btn-cancel {
            width: 100%;
            text-align: center;
        }
    }
</style>

<section class="profile-page">
    <div class="profile-container">
        <div class="profile-header">
            <h1>Thông tin cá nhân</h1>
            <p>Quản lý thông tin tài khoản của bạn</p>
        </div>

        <form action="<?= BASE_URL ?>?action=update-profile" method="POST" class="profile-form">
            <div class="form-group">
                <label for="firstname">Họ <span style="color: #dc3545;">*</span></label>
                <input type="text" id="firstname" name="firstname" 
                       value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="lastname">Tên <span style="color: #dc3545;">*</span></label>
                <input type="text" id="lastname" name="lastname" 
                       value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>Giới tính <span style="color: #dc3545;">*</span></label>
                <div class="gender-group">
                    <label>
                        <input type="radio" name="gender" value="female" 
                               <?= ($user['gender'] ?? 'female') === 'female' ? 'checked' : '' ?>>
                        Nữ
                    </label>
                    <label>
                        <input type="radio" name="gender" value="male" 
                               <?= ($user['gender'] ?? '') === 'male' ? 'checked' : '' ?>>
                        Nam
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="birthday">Ngày sinh <span style="color: #dc3545;">*</span></label>
                <input type="date" id="birthday" name="birthday" 
                       value="<?= htmlspecialchars($user['birthday'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email <span style="color: #dc3545;">*</span></label>
                <input type="email" id="email" name="email" 
                       value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input type="tel" id="phone" name="phone" 
                       value="<?= htmlspecialchars($user['phone'] ?? '') ?>" 
                       pattern="[0-9]{10,11}" 
                       placeholder="0123456789">
                <span class="info-badge">10 hoặc 11 chữ số</span>
            </div>

            <div class="form-group">
                <label for="address">Địa chỉ</label>
                <textarea id="address" name="address" 
                          placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
            </div>

            <div class="profile-actions">
                <button type="submit" class="btn-save">Lưu thay đổi</button>
                <a href="<?= BASE_URL ?>" class="btn-cancel">Hủy</a>
            </div>
        </form>
    </div>
</section>

