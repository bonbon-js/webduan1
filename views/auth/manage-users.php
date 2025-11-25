<?php
$keyword = htmlspecialchars($_GET['keyword'] ?? '', ENT_QUOTES, 'UTF-8');
$totalUsers = isset($users) ? count($users) : 0;
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Quản lý tài khoản</h1>
            <p class="text-muted mb-0">Theo dõi và tìm kiếm người dùng trong hệ thống.</p>
        </div>
        <span class="badge bg-dark fs-6">Tổng cộng: <?= $totalUsers ?></span>
    </div>

    <form class="row g-2 mb-4" method="GET" action="<?= BASE_URL ?>">
        <input type="hidden" name="action" value="manage-users">
        <div class="col-md-4">
            <input type="text" name="keyword" class="form-control" placeholder="Tìm theo tên hoặc email"
                   value="<?= $keyword ?>">
        </div>
        <div class="col-auto">
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-search me-2"></i>Tìm kiếm
            </button>
        </div>
        <?php if (!empty($keyword)): ?>
            <div class="col-auto">
                <a href="<?= BASE_URL ?>?action=manage-users" class="btn btn-outline-secondary">
                    Xóa lọc
                </a>
            </div>
        <?php endif; ?>
    </form>

    <div class="table-responsive shadow-sm rounded">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Điện thoại</th>
                    <th>Địa chỉ</th>
                    <th>Vai trò</th>
                    <th>Ngày tạo</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $index => $user): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($user['full_name'] ?? 'Chưa cập nhật') ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['phone'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($user['address'] ?? '—') ?></td>
                            <td>
                                <span class="badge <?= ($user['role'] ?? 'user') === 'admin' ? 'bg-warning text-dark' : 'bg-secondary' ?>">
                                    <?= strtoupper($user['role'] ?? 'user') ?>
                                </span>
                            </td>
                            <td><?= !empty($user['created_at']) ? date('d/m/Y H:i', strtotime($user['created_at'])) : '—' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Không tìm thấy tài khoản nào phù hợp.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>



