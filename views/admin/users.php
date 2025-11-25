<?php
// Helper để hiển thị "Tên đăng nhập" khi DB chưa có cột riêng
$resolveUsername = function ($user) {
    if (!empty($user['username'])) {
        return $user['username'];
    }

    if (!empty($user['full_name'])) {
        return trim(explode(' ', $user['full_name'])[0]);
    }

    if (!empty($user['email'])) {
        return strstr($user['email'], '@', true) ?: $user['email'];
    }

    return '—';
};
?>
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin-users.css">

<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="brand">BonBon</div>
        <ul class="admin-menu">
            <li>
                <a href="#" class="menu-link">
                    <span class="menu-icon icon-folder"><i class="fa-solid fa-folder-open"></i></span>
                    <span>Quản lý danh mục</span>
                </a>
            </li>
            <li>
                <a href="#" class="menu-link">
                    <span class="menu-icon icon-product"><i class="fa-solid fa-box-open"></i></span>
                    <span>Quản lý sản phẩm</span>
                </a>
            </li>
            <li>
                <a class="menu-link active" href="<?= BASE_URL ?>?action=accounts">
                    <span class="menu-icon icon-users"><i class="fa-solid fa-users"></i></span>
                    <span>Quản lý người dùng</span>
                </a>
            </li>
            <li>
                <a href="#" class="menu-link">
                    <span class="menu-icon icon-comment"><i class="fa-solid fa-comments"></i></span>
                    <span>Quản lý bình luận</span>
                </a>
            </li>
            <li>
                <a href="#" class="menu-link">
                    <span class="menu-icon icon-order"><i class="fa-solid fa-boxes-stacked"></i></span>
                    <span>Quản lý đơn hàng</span>
                </a>
            </li>
            <li>
                <a href="#" class="menu-link">
                    <span class="menu-icon icon-stats"><i class="fa-solid fa-chart-pie"></i></span>
                    <span>Thống kê</span>
                </a>
            </li>
        </ul>
        <hr style="border-color:rgba(255,255,255,0.08); width:90%; margin:10px auto;">
        <ul class="admin-menu">
            <li>
                <a href="<?= BASE_URL ?>" class="menu-link">
                    <span class="menu-icon icon-web"><i class="fa-solid fa-globe"></i></span>
                    <span>Xem website</span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>?action=logout" class="menu-link">
                    <span class="menu-icon icon-logout"><i class="fa-solid fa-right-from-bracket"></i></span>
                    <span>Đăng xuất</span>
                </a>
            </li>
        </ul>
    </aside>

    <section class="admin-content">
        <div class="admin-card">
            <div class="admin-topbar">
                <div>
                    <h2>Quản lý người dùng</h2>
                    <p class="text-muted mb-0">Theo dõi & phân quyền tài khoản trong hệ thống.</p>
                </div>
                <div class="admin-toolbar">
                    <div class="avatar-chip">
                        <span><?= strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)) ?></span>
                        <div>
                            <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Administrator') ?></strong><br>
                            <small><?= htmlspecialchars($_SESSION['user_role'] ?? 'admin') ?></small>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <?php if (!empty($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <div class="text-muted mb-3">Tổng: <strong><?= isset($total) ? $total : (isset($users) ? count($users) : 0) ?></strong> người dùng</div>

            <div class="table-responsive">
                <table class="table table-borderless align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên đăng nhập</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Ngày tạo</th>
                            <th>Vai trò</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><?= htmlspecialchars($u['user_id']) ?></td>
                                    <td><?= htmlspecialchars($resolveUsername($u)) ?></td>
                                    <td><?= htmlspecialchars($u['full_name'] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($u['email']) ?></td>
                                    <td><?= htmlspecialchars($u['phone'] ?? '—') ?></td>
                                    <td><?= !empty($u['created_at']) ? date('d/m/Y H:i', strtotime($u['created_at'])) : '—' ?></td>
                                    <td>
                                        <?php $role = $u['role'] ?? 'user'; ?>
                                        <span class="status-pill <?= $role === 'admin' ? 'admin' : 'user' ?>">
                                            <?= $role === 'admin' ? 'Admin' : 'User' ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="<?= BASE_URL ?>?action=accounts-edit&id=<?= $u['user_id'] ?>">Sửa</a>
                                        <form method="post" action="<?= BASE_URL ?>?action=accounts-delete" onsubmit="return confirm('Bạn có chắc muốn xóa tài khoản này?');" style="display:inline-block;">
                                            <input type="hidden" name="id" value="<?= $u['user_id'] ?>">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                            <button class="btn-icon" type="submit" title="Xóa">🗑</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Chưa có người dùng nào.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php
            $total = $total ?? 0;
            $perPage = 10;
            $current = max(1, (int)($_GET['page'] ?? 1));
            $last = (int)ceil($total / $perPage);
            if ($last > 1):
            ?>
            <nav class="mt-3" aria-label="Page navigation">
                <ul class="pagination">
                    <?php for ($p = 1; $p <= $last; $p++): ?>
                        <li class="page-item <?= $p === $current ? 'active' : '' ?>">
                            <a class="page-link" href="<?= BASE_URL ?>?action=accounts&q=<?= urlencode($_GET['q'] ?? '') ?>&page=<?= $p ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </section>
</div>
