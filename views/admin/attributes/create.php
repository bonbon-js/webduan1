<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-plus-circle"></i> Thêm thuộc tính mới</h2>
    <a href="<?= BASE_URL ?>?action=admin-attributes" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>?action=admin-attribute-store" onsubmit="return validateForm()">
            <div class="mb-3">
                <label class="form-label">Tên thuộc tính <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required placeholder="VD: Size, Màu sắc, Chất liệu...">
                <small class="text-muted">Tên đặc điểm của sản phẩm</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Giá trị (tùy chọn)</label>
                <input type="text" name="values" class="form-control" placeholder="VD: S, M, L, XL (cách nhau bởi dấu phẩy)">
                <small class="text-muted">Nhập các giá trị cách nhau bởi dấu phẩy. Có thể thêm sau.</small>
            </div>

            <!-- Ví dụ -->
            <div class="alert alert-info">
                <h6 class="mb-2"><i class="bi bi-lightbulb"></i> Ví dụ:</h6>
                <ul class="mb-0">
                    <li><strong>Tên thuộc tính:</strong> Size</li>
                    <li><strong>Giá trị:</strong> S, M, L, XL, XXL</li>
                </ul>
                <hr>
                <ul class="mb-0">
                    <li><strong>Tên thuộc tính:</strong> Màu sắc</li>
                    <li><strong>Giá trị:</strong> Đen, Trắng, Xám, Xanh Navy, Đỏ Đô</li>
                </ul>
            </div>

            <hr>

            <div class="d-flex justify-content-end gap-2">
                <a href="<?= BASE_URL ?>?action=admin-attributes" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Hủy
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Thêm thuộc tính
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Hướng dẫn -->
<div class="card mt-3">
    <div class="card-header bg-light">
        <h6 class="mb-0"><i class="bi bi-question-circle"></i> Hướng dẫn sử dụng</h6>
    </div>
    <div class="card-body">
        <ol>
            <li><strong>Tạo thuộc tính:</strong> Nhập tên thuộc tính (VD: Size, Màu sắc)</li>
            <li><strong>Thêm giá trị:</strong> Nhập các giá trị cách nhau bởi dấu phẩy</li>
            <li><strong>Áp dụng cho sản phẩm:</strong> Sau khi tạo, bạn có thể liên kết thuộc tính với sản phẩm</li>
        </ol>
        <p class="mb-0 text-muted">
            <i class="bi bi-info-circle"></i> 
            Bạn có thể thêm/sửa/xóa giá trị sau khi tạo thuộc tính bằng cách click "Sửa".
        </p>
    </div>
</div>

<script>
    function validateForm() {
        const name = document.querySelector('input[name="name"]').value.trim();

        if (!name) {
            alert('Vui lòng nhập tên thuộc tính');
            return false;
        }

        return true;
    }
</script>
