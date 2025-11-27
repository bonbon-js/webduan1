<style>
    .coupons-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 2rem;
        color: #fff;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }
    .coupons-header h2 {
        margin: 0;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .coupons-header .icon-wrapper {
        width: 56px;
        height: 56px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .admin-table {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    .table {
        margin: 0;
    }
    .table thead {
        background: #f8f9fa;
    }
    .table thead th {
        border: none;
        padding: 1rem;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        color: #495057;
    }
    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-top: 1px solid #f0f0f0;
    }
    .badge {
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.75rem;
    }
    .btn-sm {
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
    }
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #999;
    }
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        display: block;
    }
    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        border: none;
    }
    .modal-header .btn-close {
        filter: brightness(0) invert(1);
    }
</style>

<div class="coupons-header">
    <h2>
        <div class="icon-wrapper">
            <i class="bi bi-ticket-perforated"></i>
        </div>
        Quản lý mã giảm giá
    </h2>
</div>

<!-- Form tìm kiếm và lọc -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>" id="searchForm">
            <input type="hidden" name="action" value="admin-coupons">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small text-uppercase fw-bold">Tìm kiếm</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" 
                               name="keyword" 
                               class="form-control" 
                               placeholder="Mã, tên hoặc mô tả..." 
                               value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>"
                               id="searchKeyword">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-uppercase fw-bold">Trạng thái</label>
                    <select name="status" class="form-select" id="statusFilter">
                        <option value="">Tất cả</option>
                        <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="expired" <?= ($_GET['status'] ?? '') === 'expired' ? 'selected' : '' ?>>Hết hạn</option>
                        <option value="out_of_stock" <?= ($_GET['status'] ?? '') === 'out_of_stock' ? 'selected' : '' ?>>Hết mã</option>
                        <option value="inactive" <?= ($_GET['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Tạm dừng</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-uppercase fw-bold">Loại giảm giá</label>
                    <select name="discount_type" class="form-select" id="discountTypeFilter">
                        <option value="">Tất cả</option>
                        <option value="percentage" <?= ($_GET['discount_type'] ?? '') === 'percentage' ? 'selected' : '' ?>>Phần trăm</option>
                        <option value="fixed" <?= ($_GET['discount_type'] ?? '') === 'fixed' ? 'selected' : '' ?>>Cố định</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-search"></i> Tìm
                        </button>
                        <?php if (!empty($_GET['keyword']) || !empty($_GET['status']) || !empty($_GET['discount_type'])): ?>
                            <a href="<?= BASE_URL ?>?action=admin-coupons" class="btn btn-outline-secondary" title="Xóa bộ lọc">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0">Danh sách mã giảm giá</h5>
        <small class="text-muted">
            <?php if (!empty($coupons)): ?>
                Tìm thấy <strong><?= count($coupons) ?></strong> mã giảm giá
                <?php if (!empty($_GET['keyword']) || !empty($_GET['status']) || !empty($_GET['discount_type'])): ?>
                    (đã lọc)
                <?php endif; ?>
            <?php else: ?>
                Không tìm thấy mã giảm giá nào
            <?php endif; ?>
        </small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>?action=admin-coupons-trash" class="btn btn-outline-secondary">
            <i class="bi bi-trash"></i> Thùng rác
            <?php
            require_once PATH_MODEL . 'CouponModel.php';
            $couponModel = new CouponModel();
            $deletedCount = count($couponModel->getDeleted());
            if ($deletedCount > 0):
            ?>
                <span class="badge bg-danger ms-1"><?= $deletedCount ?></span>
            <?php endif; ?>
        </a>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#couponModal" onclick="openCouponModal()">
            <i class="bi bi-plus-circle"></i> Thêm mã giảm giá
        </button>
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
                <th>Giảm tối đa</th>
                <th>Thời gian</th>
                <th>Số lần dùng</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($coupons)): ?>
                <tr>
                    <td colspan="10" class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <div>Chưa có mã giảm giá nào</div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($coupons as $coupon): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($coupon['code']) ?></strong></td>
                        <td><?= htmlspecialchars($coupon['name']) ?></td>
                        <td>
                            <span class="badge bg-info">
                                <?= $coupon['discount_type'] === 'percentage' ? 'Phần trăm' : 'Cố định' ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($coupon['discount_type'] === 'percentage'): ?>
                                <?= number_format($coupon['discount_value'], 0) ?>%
                            <?php else: ?>
                                <?= number_format($coupon['discount_value'], 0, ',', '.') ?> đ
                            <?php endif; ?>
                        </td>
                        <td><?= number_format($coupon['min_order_amount'], 0, ',', '.') ?> đ</td>
                        <td>
                            <?= $coupon['max_discount_amount'] ? number_format($coupon['max_discount_amount'], 0, ',', '.') . ' đ' : 'Không giới hạn' ?>
                        </td>
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
                            <?php
                            $calculatedStatus = $coupon['calculated_status'] ?? 'active';
                            $statusBadge = 'success';
                            $statusText = 'Hoạt động';
                            
                            if ($calculatedStatus === 'expired') {
                                $statusBadge = 'danger';
                                $statusText = 'Hết hạn';
                            } elseif ($calculatedStatus === 'out_of_stock') {
                                $statusBadge = 'warning';
                                $statusText = 'Hết mã';
                            } elseif ($calculatedStatus === 'inactive') {
                                $statusBadge = 'secondary';
                                $statusText = 'Tạm dừng';
                            }
                            ?>
                            <span class="badge bg-<?= $statusBadge ?>">
                                <?= $statusText ?>
                            </span>
                        </td>
                        <td>
                            <button type="button" 
                                    class="btn btn-sm btn-primary edit-coupon-btn" 
                                    data-coupon-id="<?= htmlspecialchars($coupon['coupon_id']) ?>"
                                    data-coupon-code="<?= htmlspecialchars($coupon['code']) ?>"
                                    data-coupon-name="<?= htmlspecialchars($coupon['name']) ?>"
                                    data-coupon-description="<?= htmlspecialchars($coupon['description'] ?? '') ?>"
                                    data-coupon-discount-type="<?= htmlspecialchars($coupon['discount_type']) ?>"
                                    data-coupon-discount-value="<?= htmlspecialchars($coupon['discount_value']) ?>"
                                    data-coupon-min-order="<?= htmlspecialchars($coupon['min_order_amount'] ?? 0) ?>"
                                    data-coupon-max-discount="<?= htmlspecialchars($coupon['max_discount_amount'] ?? '') ?>"
                                    data-coupon-start-date="<?= htmlspecialchars($coupon['start_date']) ?>"
                                    data-coupon-end-date="<?= htmlspecialchars($coupon['end_date']) ?>"
                                    data-coupon-usage-limit="<?= htmlspecialchars($coupon['usage_limit'] ?? '') ?>"
                                    data-coupon-status="<?= htmlspecialchars($coupon['status']) ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" 
                                  action="<?= BASE_URL ?>?action=admin-coupon-delete" 
                                  style="display: inline;"
                                  onsubmit="return confirm('Bạn có chắc muốn xóa mã giảm giá này?')">
                                <input type="hidden" name="coupon_id" value="<?= $coupon['coupon_id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal thêm/sửa mã giảm giá -->
<div class="modal fade" id="couponModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="couponModalTitle">Thêm mã giảm giá</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>?action=admin-coupon-create" id="couponForm">
                <input type="hidden" name="coupon_id" id="couponId">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Mã giảm giá <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   name="code" 
                                   id="couponCode" 
                                   required 
                                   placeholder="WELCOME10"
                                   style="text-transform: uppercase;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tên mã giảm giá <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   name="name" 
                                   id="couponName" 
                                   required 
                                   placeholder="Mã chào mừng 10%">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" 
                                      name="description" 
                                      id="couponDescription" 
                                      rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Loại giảm giá <span class="text-danger">*</span></label>
                            <select class="form-select" name="discount_type" id="discountType" required>
                                <option value="percentage">Phần trăm (%)</option>
                                <option value="fixed">Cố định (VNĐ)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Giá trị giảm <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control" 
                                   name="discount_value" 
                                   id="discountValue" 
                                   required 
                                   min="0" 
                                   step="0.01"
                                   placeholder="10 hoặc 50000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Đơn hàng tối thiểu (VNĐ)</label>
                            <input type="number" 
                                   class="form-control" 
                                   name="min_order_amount" 
                                   id="minOrderAmount" 
                                   min="0" 
                                   step="1000"
                                   value="0"
                                   placeholder="500000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Giảm tối đa (VNĐ) - Chỉ áp dụng với phần trăm</label>
                            <input type="number" 
                                   class="form-control" 
                                   name="max_discount_amount" 
                                   id="maxDiscountAmount" 
                                   min="0" 
                                   step="1000"
                                   placeholder="Để trống nếu không giới hạn">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                            <input type="datetime-local" 
                                   class="form-control" 
                                   name="start_date" 
                                   id="startDate" 
                                   required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                            <input type="datetime-local" 
                                   class="form-control" 
                                   name="end_date" 
                                   id="endDate" 
                                   required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Giới hạn số lần sử dụng</label>
                            <input type="number" 
                                   class="form-control" 
                                   name="usage_limit" 
                                   id="usageLimit" 
                                   min="1"
                                   placeholder="Để trống nếu không giới hạn">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select class="form-select" name="status" id="couponStatus" required>
                                <option value="active">Hoạt động</option>
                                <option value="inactive">Tạm dừng</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Đảm bảo hàm openCouponModal được định nghĩa trước khi sử dụng
window.openCouponModal = function(coupon = null) {
    try {
        const form = document.getElementById('couponForm');
        const modalTitle = document.getElementById('couponModalTitle');
        const couponId = document.getElementById('couponId');
        const modalElement = document.getElementById('couponModal');
        
        if (!form || !modalTitle || !couponId || !modalElement) {
            alert('Có lỗi xảy ra khi mở form. Vui lòng tải lại trang.');
            return;
        }
        
        if (coupon) {
            // Sửa mã giảm giá
            modalTitle.textContent = 'Sửa mã giảm giá';
            form.action = '<?= BASE_URL ?>?action=admin-coupon-update';
            couponId.value = coupon.coupon_id;
            document.getElementById('couponCode').value = coupon.code || '';
            document.getElementById('couponName').value = coupon.name || '';
            document.getElementById('couponDescription').value = coupon.description || '';
            document.getElementById('discountType').value = coupon.discount_type || 'percentage';
            document.getElementById('discountValue').value = coupon.discount_value || '';
            document.getElementById('minOrderAmount').value = coupon.min_order_amount || 0;
            document.getElementById('maxDiscountAmount').value = coupon.max_discount_amount || '';
            document.getElementById('startDate').value = coupon.start_date ? coupon.start_date.replace(' ', 'T').substring(0, 16) : '';
            document.getElementById('endDate').value = coupon.end_date ? coupon.end_date.replace(' ', 'T').substring(0, 16) : '';
            document.getElementById('usageLimit').value = coupon.usage_limit || '';
            document.getElementById('couponStatus').value = coupon.status || 'active';
            
            // Xử lý max_discount_amount khi sửa
            handleDiscountTypeChange();
        } else {
            // Thêm mã giảm giá mới
            modalTitle.textContent = 'Thêm mã giảm giá';
            form.action = '<?= BASE_URL ?>?action=admin-coupon-create';
            form.reset();
            couponId.value = '';
            
            // Reset tất cả các trường
            document.getElementById('couponCode').value = '';
            document.getElementById('couponName').value = '';
            document.getElementById('couponDescription').value = '';
            document.getElementById('discountType').value = 'percentage';
            document.getElementById('discountValue').value = '';
            document.getElementById('minOrderAmount').value = '0';
            document.getElementById('maxDiscountAmount').value = '';
            document.getElementById('usageLimit').value = '';
            document.getElementById('couponStatus').value = 'active';
            
            // Set ngày mặc định
            document.getElementById('startDate').value = new Date().toISOString().slice(0, 16);
            document.getElementById('endDate').value = new Date(Date.now() + 30*24*60*60*1000).toISOString().slice(0, 16);
            
            // Xử lý max_discount_amount khi thêm mới
            handleDiscountTypeChange();
            
            // Clear validation
            document.getElementById('endDate').setCustomValidity('');
        }
        
        // Validate ngày khi mở modal với dữ liệu
        if (coupon) {
            setTimeout(() => {
                validateDates();
            }, 100);
        }
        
        // Mở modal bằng Bootstrap
        if (typeof bootstrap === 'undefined') {
            alert('Bootstrap chưa được load. Vui lòng tải lại trang.');
            return;
        }
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
    } catch (error) {
        console.error('Lỗi khi mở modal:', error);
        alert('Có lỗi xảy ra khi mở form sửa mã giảm giá: ' + error.message);
    }
};

// Các hàm helper khác

// Xử lý khi thay đổi loại giảm giá
function handleDiscountTypeChange() {
    const discountType = document.getElementById('discountType').value;
    const maxDiscountInput = document.getElementById('maxDiscountAmount');
    
    if (discountType === 'fixed') {
        // Nếu chọn cố định, xóa giá trị và disable
        maxDiscountInput.value = '';
        maxDiscountInput.disabled = true;
        maxDiscountInput.placeholder = 'Không áp dụng cho giảm giá cố định';
    } else {
        // Nếu chọn phần trăm, enable lại
        maxDiscountInput.disabled = false;
        maxDiscountInput.placeholder = 'Để trống nếu không giới hạn';
    }
}

// Validate ngày kết thúc > ngày bắt đầu
function validateDates() {
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    
    if (!startDateInput.value || !endDateInput.value) {
        return true; // Để HTML5 validation xử lý
    }
    
    const startDate = new Date(startDateInput.value);
    const endDate = new Date(endDateInput.value);
    
    if (endDate <= startDate) {
        endDateInput.setCustomValidity('Ngày kết thúc phải sau ngày bắt đầu. Vui lòng nhập lại.');
        endDateInput.reportValidity();
        endDateInput.focus();
        return false;
    } else {
        endDateInput.setCustomValidity('');
    }
    
    return true;
}

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        const discountType = document.getElementById('discountType');
        const couponForm = document.getElementById('couponForm');
        
        // Xử lý khi thay đổi loại giảm giá
        if (discountType) {
            discountType.addEventListener('change', handleDiscountTypeChange);
        }
        
        // Validate form trước khi submit
        if (couponForm) {
            couponForm.addEventListener('submit', function(e) {
                if (!validateDates()) {
                    e.preventDefault();
                    return false;
                }
            });
        }
        
        // Validate ngày khi thay đổi
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');
        
        if (startDateInput && endDateInput) {
            startDateInput.addEventListener('change', function() {
                validateDates();
            });
            
            endDateInput.addEventListener('change', function() {
                validateDates();
            });
            
            // Validate khi mở modal với dữ liệu có sẵn
            if (startDateInput.value && endDateInput.value) {
                validateDates();
            }
        }
        
        // Auto-submit khi thay đổi filter (trạng thái, loại giảm giá)
        const statusFilter = document.getElementById('statusFilter');
        const discountTypeFilter = document.getElementById('discountTypeFilter');
        
        if (statusFilter) {
            statusFilter.addEventListener('change', function() {
                document.getElementById('searchForm').submit();
            });
        }
        
        if (discountTypeFilter) {
            discountTypeFilter.addEventListener('change', function() {
                document.getElementById('searchForm').submit();
            });
        }
        
        // Xử lý nút sửa mã giảm giá
        const editButtons = document.querySelectorAll('.edit-coupon-btn');
        editButtons.forEach((btn) => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                try {
                    const coupon = {
                        coupon_id: this.getAttribute('data-coupon-id'),
                        code: this.getAttribute('data-coupon-code'),
                        name: this.getAttribute('data-coupon-name'),
                        description: this.getAttribute('data-coupon-description'),
                        discount_type: this.getAttribute('data-coupon-discount-type'),
                        discount_value: this.getAttribute('data-coupon-discount-value'),
                        min_order_amount: this.getAttribute('data-coupon-min-order'),
                        max_discount_amount: this.getAttribute('data-coupon-max-discount'),
                        start_date: this.getAttribute('data-coupon-start-date'),
                        end_date: this.getAttribute('data-coupon-end-date'),
                        usage_limit: this.getAttribute('data-coupon-usage-limit'),
                        status: this.getAttribute('data-coupon-status')
                    };
                    if (window.openCouponModal) {
                        window.openCouponModal(coupon);
                    } else {
                        alert('Hàm openCouponModal chưa được định nghĩa. Vui lòng tải lại trang.');
                    }
                } catch (error) {
                    console.error('Lỗi khi xử lý nút sửa:', error);
                    alert('Có lỗi xảy ra: ' + error.message);
                }
            });
        });
        
        // Cho phép nhấn Enter để tìm kiếm
        const searchKeyword = document.getElementById('searchKeyword');
        if (searchKeyword) {
            searchKeyword.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('searchForm').submit();
                }
            });
        }
        
        // Nếu tìm thấy đúng 1 mã giảm giá, tự động mở modal
        <?php if (count($coupons) === 1 && !empty($_GET['keyword'])): ?>
            const singleCoupon = <?= json_encode($coupons[0]) ?>;
            // Chờ modal Bootstrap sẵn sàng
            setTimeout(function() {
                openCouponModal(singleCoupon);
                const modal = new bootstrap.Modal(document.getElementById('couponModal'));
                modal.show();
            }, 100);
        <?php endif; ?>
    });
</script>

