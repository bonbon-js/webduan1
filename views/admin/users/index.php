<style>
    .users-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 2rem;
        color: #fff;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }
    .users-header h2 {
        margin: 0;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .users-header .icon-wrapper {
        width: 56px;
        height: 56px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .users-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    }
    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0.5rem 0;
    }
    .stat-card .stat-label {
        color: #64748b;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stat-card .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    .stat-card.total .stat-icon { background: #dbeafe; color: #3b82f6; }
    .stat-card.admin .stat-icon { background: #fee2e2; color: #ef4444; }
    .stat-card.user .stat-icon { background: #dbeafe; color: #3b82f6; }
    .stat-card.verified .stat-icon { background: #d1fae5; color: #10b981; }
    
    .admin-table {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    .admin-table table {
        margin: 0;
    }
    .admin-table thead {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }
    .admin-table th {
        font-weight: 600;
        color: #1e293b;
        border-bottom: 2px solid #e2e8f0;
        padding: 1.25rem 1rem;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .admin-table tbody tr {
        transition: all 0.2s;
        border-bottom: 1px solid #f1f5f9;
    }
    .admin-table tbody tr:hover {
        background: #f8fafc;
        transform: scale(1.01);
    }
    .admin-table tbody tr:last-child {
        border-bottom: none;
    }
    .admin-table td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
    }
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 600;
        font-size: 0.875rem;
        margin-right: 0.75rem;
    }
    .user-info {
        display: flex;
        align-items: center;
    }
    .user-name {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.25rem;
    }
    .user-email {
        font-size: 0.875rem;
        color: #64748b;
    }
    .role-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .role-badge.user {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: #fff;
    }
    .role-badge.admin {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #fff;
    }
    .btn-delete {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #fff;
        border: none;
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
    }
    .btn-delete:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(239, 68, 68, 0.4);
    }
    .current-user-badge {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #fff;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #94a3b8;
    }
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
</style>

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

