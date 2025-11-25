<?php
// $user is provided by controller
?>

<div class="container py-4">
    <div class="card">
        <div class="card-body">
            <h4><?= htmlspecialchars($title) ?></h4>
            <form method="post" action="<?= BASE_URL ?>?action=accounts-edit">
                <input type="hidden" name="id" value="<?= htmlspecialchars($user['user_id']) ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                <div class="mb-3">
                    <label class="form-label">Họ và tên</label>
                    <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Điện thoại</label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Địa chỉ</label>
                    <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($user['address']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Vai trò</label>
                    <select name="role" class="form-select">
                        <option value="user" <?= ($user['role'] === 'user') ? 'selected' : '' ?>>User</option>
                        <option value="admin" <?= ($user['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>

                <button class="btn btn-primary" type="submit">Lưu</button>
                <a class="btn btn-secondary" href="<?= BASE_URL ?>?action=accounts">Hủy</a>
            </form>
        </div>
    </div>
</div>
