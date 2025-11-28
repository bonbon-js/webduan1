<div class="users-header">
    <h2>
        <div class="icon-wrapper">
            <i class="bi bi-people"></i>
        </div>
        <span>Quản lý người dùng</span>
    </h2>
</div>

<?php
$totalUsers = count($users ?? []);
$adminCount = 0;
$userCount = 0;
$verifiedCount = 0;

foreach ($users ?? [] as $user) {
    if (($user['role'] ?? 'customer') === 'admin') {
        $adminCount++;
    } else {
        $userCount++;
    }
    if (empty($user['session_token'])) {
        $verifiedCount++;
    }
}
?>

<div class="users-stats">
    <div class="stat-card total">
        <div class="stat-icon">
            <i class="bi bi-people-fill"></i>
        </div>
        <div class="stat-value"><?= $totalUsers ?></div>
        <div class="stat-label">Tổng người dùng</div>
    </div>
    <div class="stat-card admin">
        <div class="stat-icon">
            <i class="bi bi-shield-check"></i>
        </div>
        <div class="stat-value"><?= $adminCount ?></div>
        <div class="stat-label">Quản trị viên</div>
    </div>
    <div class="stat-card user">
        <div class="stat-icon">
            <i class="bi bi-person-fill"></i>
        </div>
        <div class="stat-value"><?= $userCount ?></div>
        <div class="stat-label">Khách hàng</div>
    </div>
    <div class="stat-card verified">
        <div class="stat-icon">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <div class="stat-value"><?= $verifiedCount ?></div>
        <div class="stat-label">Đã xác thực</div>
    </div>
</div>

<div class="admin-table">
    <table class="table mb-0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên đăng nhập</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Ngày tạo</th>
                <th>Vai trò</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="8" class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <div>Chưa có tài khoản nào</div>
                    </td>
                </tr>
            <?php else: ?>
                <?php 
                $currentUserId = $_SESSION['user']['id'] ?? null;
                foreach ($users as $user): 
                    $userId = $user['user_id'] ?? $user['id'] ?? 0;
                    $isCurrentUser = $currentUserId && (int)$userId === (int)$currentUserId;
                    $fullName = $user['full_name'] ?? trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                    $initials = strtoupper(substr($fullName, 0, 1) . substr($fullName, strrpos($fullName, ' ') + 1, 1));
                ?>
                    <tr>
                        <td><strong class="text-muted">#<?= htmlspecialchars($userId) ?></strong></td>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar"><?= htmlspecialchars($initials) ?></div>
                                <div>
                                    <div class="user-name"><?= htmlspecialchars($user['email']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="user-name"><?= htmlspecialchars($fullName) ?></div>
                        </td>
                        <td>
                            <div class="user-email"><?= htmlspecialchars($user['email']) ?></div>
                        </td>
                        <td>
                            <span class="text-muted"><?= htmlspecialchars($user['phone'] ?? '-') ?></span>
                        </td>
                        <td>
                            <span class="text-muted"><?= date('d/m/Y H:i', strtotime($user['created_at'] ?? 'now')) ?></span>
                        </td>
                        <td>
                            <span class="role-badge <?= ($user['role'] ?? 'customer') === 'admin' ? 'admin' : 'user' ?>">
                                <i class="bi bi-<?= ($user['role'] ?? 'customer') === 'admin' ? 'shield-check' : 'person' ?>"></i>
                                <?= ($user['role'] ?? 'customer') === 'admin' ? 'Admin' : 'User' ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($isCurrentUser): ?>
                                <span class="current-user-badge">
                                    <i class="bi bi-check-circle"></i>
                                    Tài khoản hiện tại
                                </span>
                            <?php else: ?>
                                <form method="POST" action="<?= BASE_URL ?>?action=admin-user-delete" style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa tài khoản này?');">
                                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($userId) ?>">
                                    <button type="submit" class="btn-delete" title="Xóa tài khoản">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

