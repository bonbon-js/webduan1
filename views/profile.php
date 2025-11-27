<style>
    .profile-page {
        min-height: calc(100vh - 120px);
        padding: 60px 40px 90px;
        background: #fff;
    }

    .profile-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .profile-header {
        margin-bottom: 2rem;
    }

    .profile-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.5rem;
    }

    .profile-header p {
        color: #64748b;
        font-size: 1rem;
    }

    .profile-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
    }

    .profile-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid #f1f5f9;
    }

    .profile-card-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .profile-form {
        display: grid;
        gap: 1.5rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .form-input {
        padding: 0.75rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.2s;
    }

    .form-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .gender-group {
        display: flex;
        gap: 2rem;
        margin-top: 0.5rem;
    }

    .gender-option {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .gender-option input[type="radio"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .gender-option label {
        cursor: pointer;
        color: #64748b;
        font-weight: 500;
    }

    .btn-submit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        width: 100%;
        max-width: 200px;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-submit:active {
        transform: translateY(0);
    }

    @media (max-width: 768px) {
        .profile-page {
            padding: 40px 20px;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .profile-card {
            padding: 1.5rem;
        }
    }
</style>

<div class="profile-page">
    <div class="profile-container">
        <div class="profile-header">
            <h1>Thông tin cá nhân</h1>
            <p>Cập nhật thông tin tài khoản của bạn</p>
        </div>

        <div class="profile-card">
            <div class="profile-card-header">
                <h2 class="profile-card-title">Thông tin cơ bản</h2>
            </div>

            <form action="<?= BASE_URL ?>?action=update-profile" method="POST" class="profile-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstname" class="form-label">Họ</label>
                        <input 
                            type="text" 
                            id="firstname" 
                            name="firstname" 
                            class="form-input" 
                            value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" 
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="lastname" class="form-label">Tên</label>
                        <input 
                            type="text" 
                            id="lastname" 
                            name="lastname" 
                            class="form-input" 
                            value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" 
                            required
                        >
                    </div>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">Giới tính</label>
                    <div class="gender-group">
                        <div class="gender-option">
                            <input 
                                type="radio" 
                                id="gender-female" 
                                name="gender" 
                                value="female" 
                                <?= ($user['gender'] ?? 'female') === 'female' ? 'checked' : '' ?>
                            >
                            <label for="gender-female">Nữ</label>
                        </div>
                        <div class="gender-option">
                            <input 
                                type="radio" 
                                id="gender-male" 
                                name="gender" 
                                value="male" 
                                <?= ($user['gender'] ?? '') === 'male' ? 'checked' : '' ?>
                            >
                            <label for="gender-male">Nam</label>
                        </div>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="birthday" class="form-label">Ngày sinh</label>
                    <input 
                        type="date" 
                        id="birthday" 
                        name="birthday" 
                        class="form-input" 
                        value="<?= htmlspecialchars($user['birthday'] ?? '') ?>"
                        max="<?= date('Y-m-d', strtotime('-18 years')) ?>"
                    >
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            class="form-input" 
                            value="<?= htmlspecialchars($user['phone'] ?? '') ?>" 
                            pattern="[0-9]{10,11}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-input" 
                            value="<?= htmlspecialchars($user['email'] ?? '') ?>" 
                            disabled
                            style="background: #f8fafc; cursor: not-allowed;"
                        >
                        <small style="color: #64748b; font-size: 0.875rem; margin-top: 0.25rem;">Email không thể thay đổi</small>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="address" class="form-label">Địa chỉ</label>
                    <input 
                        type="text" 
                        id="address" 
                        name="address" 
                        class="form-input" 
                        value="<?= htmlspecialchars($user['address'] ?? '') ?>" 
                        required
                    >
                </div>

                <div class="form-group full-width" style="margin-top: 1rem;">
                    <button type="submit" class="btn-submit">Cập nhật thông tin</button>
                </div>
            </form>
            
            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e2e8f0;">
                <a href="<?= BASE_URL ?>" class="btn btn-outline-secondary" style="text-decoration: none;">
                    <i class="bi bi-arrow-left me-2"></i>
                    Quay lại trang chủ
                </a>
            </div>
        </div>
    </div>
</div>

