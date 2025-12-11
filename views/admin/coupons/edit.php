<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<?php if (!empty($warnings)): ?>
    <div class="alert alert-warning">
        <ul class="mb-0">
            <?php foreach ($warnings as $warn): ?>
                <li><?= htmlspecialchars($warn) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="admin-page-header">
    <div class="title-wrap">
        <p class="text-uppercase mb-1 small">Bảng điều khiển</p>
        <h2 class="d-flex align-items-center gap-2 mb-0">
            <i class="bi bi-ticket-perforated"></i>
            <span>Sửa mã giảm giá</span>
        </h2>
    </div>
    <div class="admin-page-actions">
        <a href="<?= BASE_URL ?>?action=admin-coupons" class="btn btn-light-soft">
            <i class="bi bi-arrow-left"></i> Danh sách
        </a>
    </div>
</div>

<?php if (isset($coupon) && $coupon): ?>
    <form class="card p-3" method="POST" action="<?= BASE_URL ?>?action=admin-coupon-update">
        <input type="hidden" name="coupon_id" value="<?= htmlspecialchars($coupon['coupon_id']) ?>">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Mã giảm giá <span class="text-danger">*</span></label>
                <input type="text"
                       name="code"
                       class="form-control"
                       value="<?= htmlspecialchars(($formData['code'] ?? $coupon['code']) ?? '') ?>"
                       readonly>
                <small class="text-muted">Áp dụng cho toàn bộ sản phẩm.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tên mã giảm giá <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars(($formData['name'] ?? $coupon['name']) ?? '') ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label">Mô tả</label>
                <textarea name="description" class="form-control" rows="2"><?= htmlspecialchars(($formData['description'] ?? $coupon['description'] ?? '')) ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Loại giảm giá <span class="text-danger">*</span></label>
                <?php $type = $formData['discount_type'] ?? ($coupon['discount_type'] ?? 'percentage'); ?>
                <select name="discount_type" class="form-select" required>
                    <option value="percentage" <?= $type === 'percentage' ? 'selected' : '' ?>>Phần trăm (%)</option>
                    <option value="fixed" <?= $type === 'fixed' ? 'selected' : '' ?>>Cố định (VNĐ)</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Giá trị giảm <span class="text-danger">*</span></label>
                <input type="number" name="discount_value" class="form-control" min="0" step="0.01" required value="<?= htmlspecialchars(($formData['discount_value'] ?? $coupon['discount_value']) ?? 0) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Đơn hàng tối thiểu (VNĐ)</label>
                <input type="number" name="min_order_amount" class="form-control" min="0" step="1000" value="<?= htmlspecialchars(($formData['min_order_amount'] ?? $coupon['min_order_amount']) ?? 0) ?>">
                <small class="text-muted">Nếu để 0: áp dụng mọi đơn.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Giảm tối đa (VNĐ) - chỉ áp dụng %</label>
                <input type="number" name="max_discount_amount" id="maxDiscountAmount" class="form-control" min="0" step="1000" value="<?= htmlspecialchars(($formData['max_discount_amount'] ?? $coupon['max_discount_amount']) ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                <?php $sd = $formData['start_date'] ?? $coupon['start_date']; ?>
                <input type="datetime-local" name="start_date" class="form-control" required value="<?= htmlspecialchars(str_replace(' ', 'T', substr($sd, 0, 16))) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                <?php $ed = $formData['end_date'] ?? $coupon['end_date']; ?>
                <input type="datetime-local" name="end_date" class="form-control" required value="<?= htmlspecialchars(str_replace(' ', 'T', substr($ed, 0, 16))) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Giới hạn số lần sử dụng</label>
                <input type="number" name="usage_limit" class="form-control" min="1" value="<?= htmlspecialchars(($formData['usage_limit'] ?? $coupon['usage_limit'] ?? '')) ?>" placeholder="Để trống nếu không giới hạn">
                <small class="text-muted">Ví dụ: 5 lượt tổng toàn hệ thống.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Giới hạn mỗi khách hàng</label>
                <input type="number" name="per_user_limit" id="perUserLimit" class="form-control" min="1" value="<?= htmlspecialchars(($formData['per_user_limit'] ?? $coupon['per_user_limit'] ?? '')) ?>" placeholder="Để trống nếu không giới hạn">
                <small class="text-muted">Ví dụ: 1 = mỗi khách dùng 1 lần. Không được lớn hơn tổng lượt.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nhóm khách hàng</label>
                <?php $group = $formData['customer_group'] ?? ($coupon['customer_group'] ?? ''); ?>
                <select class="form-select" name="customer_group" id="customerGroup">
                    <option value="" <?= $group === '' ? 'selected' : '' ?>>Tất cả</option>
                    <option value="vip_today" <?= $group === 'vip_today' ? 'selected' : '' ?>>VIP (đơn giao thành công ≥ 2.000.000đ)</option>
                </select>
                <small class="text-muted" id="vipHelp">VIP tự động gán khi có đơn giao thành công đạt ngưỡng.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Trạng thái</label>
                <select class="form-select" name="status" required>
                    <option value="active" <?= ($coupon['status'] ?? '') === 'active' ? 'selected' : '' ?>>Hoạt động</option>
                    <option value="inactive" <?= ($coupon['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Ngừng hoạt động</option>
                </select>
            </div>
            <div class="col-12">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="newCustomerOnly" name="new_customer_only" <?= !empty($formData['new_customer_only'] ?? $coupon['new_customer_only'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="newCustomerOnly">Chỉ khách mới</label>
                            <small class="text-muted d-block">Khách mới = chưa có đơn giao thành công.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="returnOnRefund" name="return_on_refund" <?= !empty($formData['return_on_refund'] ?? $coupon['return_on_refund'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="returnOnRefund">Hoàn lượt khi hoàn tiền</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end mt-4 gap-2">
            <a href="<?= BASE_URL ?>?action=admin-coupons" class="btn btn-outline-secondary">Hủy</a>
            <button type="submit" class="btn btn-primary">Lưu</button>
        </div>
    </form>
<?php else: ?>
    <div class="alert alert-danger">Không tìm thấy mã giảm giá.</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const discountType = document.querySelector('select[name="discount_type"]');
    const maxDiscountInput = document.getElementById('maxDiscountAmount');
    const customerGroupSelect = document.getElementById('customerGroup');
    const vipHelp = document.getElementById('vipHelp');
    const newCustomerOnly = document.getElementById('newCustomerOnly');
    const perUserLimit = document.getElementById('perUserLimit');
    const returnOnRefund = document.getElementById('returnOnRefund');

    function toggleMaxDiscount() {
        if (!discountType || !maxDiscountInput) return;
        if (discountType.value === 'fixed') {
            maxDiscountInput.value = '';
            maxDiscountInput.disabled = true;
            maxDiscountInput.placeholder = 'Không áp dụng cho giảm cố định';
        } else {
            maxDiscountInput.disabled = false;
            maxDiscountInput.placeholder = 'Để trống nếu không giới hạn';
        }
    }

    function toggleVipHelp() {
        if (!customerGroupSelect || !vipHelp) return;
        vipHelp.style.display = customerGroupSelect.value === 'vip_today' ? 'block' : 'none';
    }

    function syncNewCustomerLock() {
        if (!newCustomerOnly || !perUserLimit || !returnOnRefund) return;
        if (newCustomerOnly.checked) {
            perUserLimit.value = 1;
            perUserLimit.readOnly = true;
            returnOnRefund.checked = true;
        } else {
            perUserLimit.readOnly = false;
        }
    }

    if (discountType) {
        discountType.addEventListener('change', toggleMaxDiscount);
        toggleMaxDiscount();
    }

    if (customerGroupSelect) {
        customerGroupSelect.addEventListener('change', toggleVipHelp);
        toggleVipHelp();
    }

    if (newCustomerOnly) {
        newCustomerOnly.addEventListener('change', syncNewCustomerLock);
        syncNewCustomerLock();
    }
});
</script>

