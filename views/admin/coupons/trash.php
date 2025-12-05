<?php
require_once PATH_MODEL . 'CouponModel.php';
$couponModel = new CouponModel();
$deletedCoupons = $couponModel->getDeleted();
?>

<div class="container-fluid py-4">
    <div class="trash-header">
        <h2>
            <div class="icon-wrapper">
                <i class="bi bi-trash"></i>
            </div>
            <span>Thùng rác - Mã giảm giá</span>
        </h2>
        <p class="mb-0 mt-2 opacity-90">Các mã giảm giá đã bị xóa. Bạn có thể khôi phục hoặc xóa vĩnh viễn.</p>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <a href="<?= BASE_URL ?>?action=admin-coupons" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
        <div>
            <small class="text-muted">
                <?php if (!empty($deletedCoupons)): ?>
                    Có <strong><?= count($deletedCoupons) ?></strong> mã giảm giá trong thùng rác
                <?php else: ?>
                    Thùng rác trống
                <?php endif; ?>
            </small>
        </div>
    </div>

    <div class="admin-table">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Mã</th>
                    <th>Tên</th>
                    <th>Loại giảm</th>
                    <th>Giá trị</th>
                    <th>Đơn tối thiểu</th>
                    <th>Thời gian</th>
                    <th>Số lần dùng</th>
                    <th>Ngày xóa</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($deletedCoupons)): ?>
                    <tr>
                        <td colspan="9" class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <div>Thùng rác trống</div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($deletedCoupons as $coupon): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($coupon['code']) ?></strong></td>
                            <td><?= htmlspecialchars($coupon['name']) ?></td>
                            <td>
                                <?php if ($coupon['discount_type'] === 'percentage'): ?>
                                    <span class="badge bg-info">Phần trăm</span>
                                <?php else: ?>
                                    <span class="badge bg-primary">Cố định</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($coupon['discount_type'] === 'percentage'): ?>
                                    <?= number_format($coupon['discount_value'], 2) ?>%
                                <?php else: ?>
                                    <?= number_format($coupon['discount_value'], 0, ',', '.') ?> đ
                                <?php endif; ?>
                            </td>
                            <td><?= number_format($coupon['min_order_amount'], 0, ',', '.') ?> đ</td>
                            <td>
                                <small>
                                    <?= date('d/m/Y', strtotime($coupon['start_date'])) ?><br>
                                    đến <?= date('d/m/Y', strtotime($coupon['end_date'])) ?>
                                </small>
                            </td>
                            <td>
                                <?= $coupon['used_count'] ?> / <?= $coupon['usage_limit'] ?? '∞' ?>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= date('d/m/Y H:i', strtotime($coupon['deleted_at'])) ?>
                                </small>
                            </td>
                            <td>
                                <form method="POST" 
                                      action="<?= BASE_URL ?>?action=admin-coupon-restore" 
                                      class="d-inline"
                                      onsubmit="return confirm('Bạn có chắc muốn khôi phục mã giảm giá này?')">
                                    <input type="hidden" name="coupon_id" value="<?= $coupon['coupon_id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-success" title="Khôi phục">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                </form>
                                <form method="POST" 
                                      action="<?= BASE_URL ?>?action=admin-coupon-force-delete" 
                                      class="d-inline"
                                      onsubmit="return confirm('Bạn có chắc muốn xóa VĨNH VIỄN mã giảm giá này? Hành động này không thể hoàn tác!')">
                                    <input type="hidden" name="coupon_id" value="<?= $coupon['coupon_id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa vĩnh viễn">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

