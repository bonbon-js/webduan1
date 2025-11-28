<!-- Trang chi tiết đơn hàng hiển thị cho user -->
<section class="order-detail-page">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <p class="text-uppercase text-muted mb-1 small">Mã đơn: <?= htmlspecialchars($order['order_code'] ?? '#' . ($order['id'] ?? $order['order_id'] ?? '')) ?></p>
                <h2 class="fw-bold">Chi tiết đơn hàng</h2>
                <p class="text-muted mb-0">Đặt lúc <?= isset($order['created_at']) && $order['created_at'] ? date('d/m/Y H:i', strtotime($order['created_at'])) : '-' ?></p>
            </div>
            <a href="<?= BASE_URL ?>?action=order-history" class="btn btn-outline-dark">Quay lại danh sách</a>
        </div>

        <?php if ($canReview): 
            // Kiểm tra xem còn sản phẩm nào chưa đánh giá không
            $hasUnreviewedItems = false;
            $firstUnreviewedItemId = null;
            foreach ($order['items'] as $item) {
                $orderItemId = 0;
                if (isset($item['id']) && $item['id']) {
                    $orderItemId = (int)$item['id'];
                } elseif (isset($item['order_item_id']) && $item['order_item_id']) {
                    $orderItemId = (int)$item['order_item_id'];
                }
                if ($orderItemId > 0 && !($item['has_reviewed'] ?? false)) {
                    $hasUnreviewedItems = true;
                    if (!$firstUnreviewedItemId) {
                        $firstUnreviewedItemId = $orderItemId;
                    }
                }
            }
        ?>
            <?php if ($hasUnreviewedItems): ?>
                <div class="alert alert-dark alert-dismissible fade show d-flex align-items-center mb-4" role="alert" id="reviewNotification">
                    <i class="bi bi-star-fill me-2 fs-4"></i>
                    <div class="flex-grow-1">
                        <strong>Đơn hàng đã được giao thành công!</strong>
                        <p class="mb-0">Vui lòng đánh giá sản phẩm bạn đã mua để giúp chúng tôi cải thiện dịch vụ. 
                        <a href="#reviewItem_<?= $firstUnreviewedItemId ?>" class="alert-link fw-bold">Cuộn xuống để đánh giá ngay</a></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Cột trái: thông tin giao nhận + trạng thái + hủy -->
            <div class="col-lg-4">
                <div class="order-summary-card mb-4">
                    <h5 class="fw-bold mb-3">Thông tin giao hàng</h5>
                    <p class="mb-1"><?= htmlspecialchars($order['fullname']) ?></p>
                    <p class="mb-1"><?= htmlspecialchars($order['phone']) ?> • <?= htmlspecialchars($order['email']) ?></p>
                    <p class="mb-1"><?= htmlspecialchars($order['address']) ?></p>
                    <p class="mb-0"><?= htmlspecialchars(($order['ward'] ?? '') . ', ' . ($order['district'] ?? '') . ', ' . ($order['city'] ?? '')) ?></p>
                </div>

                <div class="order-summary-card mb-4">
                    <h5 class="fw-bold mb-3">Trạng thái đơn hàng</h5>
                    <span class="badge bg-<?= OrderModel::statusBadge($order['status']) ?> px-3 py-2 mb-3">
                        <?= OrderModel::statusLabel($order['status']) ?>
                    </span>

                    <ul class="status-steps">
                        <?php foreach (OrderModel::statuses() as $key => $label): ?>
                            <li>
                                <span class="status-dot <?= $order['status'] === $key ? 'active' : '' ?>"></span>
                                <span><?= $label ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if (!empty($order['cancel_reason'])): ?>
                        <div class="alert alert-light border mt-3">
                            <strong>Lý do hủy:</strong>
                            <div><?= htmlspecialchars($order['cancel_reason']) ?></div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($canCancel): ?>
                    <div class="order-summary-card">
                        <h5 class="fw-bold mb-3">Hủy đơn hàng</h5>
                        <form method="POST" action="<?= BASE_URL ?>?action=order-cancel">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <div class="mb-3">
                                <label class="form-label small text-uppercase">Lý do (tuỳ chọn)</label>
                                <textarea class="form-control" name="reason" rows="3" placeholder="Ví dụ: Đổi ý, đặt nhầm size..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-outline-danger w-100">Hủy đơn</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Cột phải: danh sách sản phẩm + ghi chú -->
            <div class="col-lg-8">
                <div class="order-items-card mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Sản phẩm</h5>
                        <div class="text-muted">
                            <?php if (!empty($order['discount_amount']) && $order['discount_amount'] > 0): ?>
                                <div class="text-end">
                                    <div class="small text-muted">Tạm tính: <?= number_format($order['total_amount'] + $order['discount_amount'], 0, ',', '.') ?> đ</div>
                                    <?php if (!empty($order['coupon_code'])): ?>
                                        <div class="small text-dark">Mã giảm giá: <?= htmlspecialchars($order['coupon_code']) ?> (<?= htmlspecialchars($order['coupon_name'] ?? '') ?>)</div>
                                        <div class="small text-dark">Giảm: -<?= number_format($order['discount_amount'], 0, ',', '.') ?> đ</div>
                                    <?php endif; ?>
                                    <div class="fw-bold">Tổng cộng: <?= number_format($order['total_amount'], 0, ',', '.') ?> đ</div>
                                </div>
                            <?php else: ?>
                                <div class="text-end">Tổng cộng: <strong><?= number_format($order['total_amount'], 0, ',', '.') ?> đ</strong></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Thuộc tính</th>
                                    <th>Số lượng</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order['items'] as $item): 
                                    // Lấy order_item_id từ nhiều nguồn
                                    $orderItemId = 0;
                                    if (isset($item['id']) && $item['id']) {
                                        $orderItemId = (int)$item['id'];
                                    } elseif (isset($item['order_item_id']) && $item['order_item_id']) {
                                        $orderItemId = (int)$item['order_item_id'];
                                    }
                                    
                                    $productId = (int)($item['product_id'] ?? 0);
                                    $hasReviewed = $item['has_reviewed'] ?? false;
                                    $existingReview = $item['review'] ?? null;
                                ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold"><?= htmlspecialchars($item['product_name']) ?></div>
                                        </td>
                                        <td>
                                            Size: <?= htmlspecialchars($item['variant_size'] ?? '-') ?> <br>
                                            Màu: <?= htmlspecialchars($item['variant_color'] ?? '-') ?>
                                        </td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td class="text-end"><?= number_format($item['quantity'] * $item['unit_price'], 0, ',', '.') ?> đ</td>
                                    </tr>
                                    <?php if ($canReview && $orderItemId): ?>
                                        <tr class="review-row" id="reviewItem_<?= $orderItemId ?>">
                                            <td colspan="4" class="border-top-0 pt-0">
                                                <?php if ($hasReviewed && $existingReview): 
                                                    $reviewImages = [];
                                                    if (!empty($existingReview['images'])) {
                                                        $reviewImages = is_array($existingReview['images']) ? $existingReview['images'] : json_decode($existingReview['images'], true);
                                                        if (!is_array($reviewImages)) $reviewImages = [];
                                                    }
                                                ?>
                                                    <div class="review-submitted p-3 bg-light rounded">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <div class="flex-grow-1">
                                                                <div class="d-flex align-items-center mb-1">
                                                                    <strong>Đánh giá của bạn:</strong>
                                                                    <div class="ms-2">
                                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                            <i class="bi bi-star<?= $i <= $existingReview['rating'] ? '-fill text-warning' : '' ?>"></i>
                                                                        <?php endfor; ?>
                                                                    </div>
                                                                </div>
                                                                <?php if (!empty($existingReview['comment'])): ?>
                                                                    <p class="mb-2 text-muted"><?= nl2br(htmlspecialchars($existingReview['comment'])) ?></p>
                                                                <?php endif; ?>
                                                                <?php if (!empty($reviewImages)): ?>
                                                                    <div class="review-images mt-2 mb-2">
                                                                        <div class="d-flex flex-wrap gap-2">
                                                                            <?php foreach ($reviewImages as $img): ?>
                                                                                <a href="<?= htmlspecialchars($img) ?>" target="_blank" class="review-image-thumbnail">
                                                                                    <img src="<?= htmlspecialchars($img) ?>" alt="Review image" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                                                                </a>
                                                                            <?php endforeach; ?>
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <?php if (!empty($existingReview['reply'])): ?>
                                                                    <div class="mt-2 p-2 bg-white rounded border-start border-3 border-dark">
                                                                        <small class="text-muted d-block mb-1"><strong>Phản hồi từ cửa hàng:</strong></small>
                                                                        <p class="mb-0 small"><?= nl2br(htmlspecialchars($existingReview['reply'])) ?></p>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <small class="text-muted"><?= date('d/m/Y', strtotime($existingReview['created_at'])) ?></small>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="review-form-container p-3 bg-light rounded">
                                                        <h6 class="mb-3">Đánh giá sản phẩm</h6>
                                                        <form class="review-form" data-order-item-id="<?= $orderItemId ?>" data-order-id="<?= $order['id'] ?? $order['order_id'] ?>" data-product-id="<?= $productId ?>" enctype="multipart/form-data">
                                                            <div class="mb-3">
                                                                <label class="form-label small">Đánh giá sao <span class="text-danger">*</span></label>
                                                                <div class="rating-input">
                                                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                                                        <input type="radio" name="rating" id="rating_<?= $orderItemId ?>_<?= $i ?>" value="<?= $i ?>" required>
                                                                        <label for="rating_<?= $orderItemId ?>_<?= $i ?>" class="star-label">
                                                                            <i class="bi bi-star-fill"></i>
                                                                        </label>
                                                                    <?php endfor; ?>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small">Bình luận (tùy chọn)</label>
                                                                <textarea name="comment" class="form-control" rows="3" placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..."></textarea>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small">Upload ảnh (tùy chọn, tối đa 5 ảnh)</label>
                                                                <input type="file" class="form-control review-image-input" accept="image/*" multiple data-order-item-id="<?= $orderItemId ?>">
                                                                <small class="text-muted">Chấp nhận: JPG, PNG, GIF, WEBP (tối đa 5MB/ảnh)</small>
                                                                <div class="review-images-preview mt-2 d-flex flex-wrap gap-2" id="reviewImagesPreview_<?= $orderItemId ?>"></div>
                                                            </div>
                                                            <button type="submit" class="btn btn-dark btn-sm">Gửi đánh giá</button>
                                                        </form>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if (!empty($order['note'])): ?>
                    <div class="order-items-card">
                        <h6 class="fw-bold mb-2">Ghi chú của bạn</h6>
                        <p class="mb-0"><?= nl2br(htmlspecialchars($order['note'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php if ($canReview): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tự động cuộn đến phần đánh giá nếu có thông báo và chưa đánh giá
    <?php if ($hasUnreviewedItems && $firstUnreviewedItemId): ?>
    // Kiểm tra xem có phải lần đầu vào trang sau khi admin set status delivered không
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('review') === 'true' || document.getElementById('reviewNotification')) {
        setTimeout(() => {
            const reviewElement = document.getElementById('reviewItem_<?= $firstUnreviewedItemId ?>');
            if (reviewElement) {
                reviewElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                // Highlight form đánh giá
                reviewElement.style.transition = 'background-color 0.3s';
                reviewElement.style.backgroundColor = '#fff3cd';
                setTimeout(() => {
                    reviewElement.style.backgroundColor = '';
                }, 2000);
            }
        }, 500);
    }
    <?php endif; ?>
    
    // Xử lý upload ảnh
    const imageInputs = document.querySelectorAll('.review-image-input');
    imageInputs.forEach(input => {
        input.addEventListener('change', async function(e) {
            const orderItemId = this.dataset.orderItemId;
            const previewContainer = document.getElementById('reviewImagesPreview_' + orderItemId);
            const files = Array.from(this.files);
            
            if (files.length > 5) {
                alert('Chỉ có thể upload tối đa 5 ảnh');
                this.value = '';
                return;
            }
            
            previewContainer.innerHTML = '';
            const uploadedImages = [];
            
            for (let file of files) {
                // Kiểm tra kích thước
                if (file.size > 5 * 1024 * 1024) {
                    alert(`Ảnh ${file.name} vượt quá 5MB. Vui lòng chọn ảnh khác.`);
                    continue;
                }
                
                // Hiển thị preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-thumbnail';
                    img.style.cssText = 'width: 80px; height: 80px; object-fit: cover; margin-right: 5px;';
                    previewContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
                
                // Upload ảnh
                const formData = new FormData();
                formData.append('image', file);
                
                try {
                    const uploadResponse = await fetch('<?= BASE_URL ?>?action=review-upload-image', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const uploadData = await uploadResponse.json();
                    if (uploadData.success) {
                        uploadedImages.push(uploadData.url);
                        // Lưu vào data attribute
                        const form = input.closest('.review-form');
                        form.dataset.uploadedImages = JSON.stringify(uploadedImages);
                    } else {
                        alert('Lỗi upload ảnh: ' + (uploadData.message || 'Vui lòng thử lại'));
                    }
                } catch (error) {
                    console.error('Upload error:', error);
                    alert('Lỗi upload ảnh. Vui lòng thử lại.');
                }
            }
        });
    });
    
    // Xử lý submit form đánh giá
    const reviewForms = document.querySelectorAll('.review-form');
    
    reviewForms.forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const orderItemId = parseInt(this.dataset.orderItemId);
            const orderId = parseInt(this.dataset.orderId);
            const productId = parseInt(this.dataset.productId);
            const rating = parseInt(this.querySelector('input[name="rating"]:checked')?.value || 0);
            const comment = this.querySelector('textarea[name="comment"]').value.trim();
            const images = this.dataset.uploadedImages ? JSON.parse(this.dataset.uploadedImages) : [];
            
            if (!rating) {
                alert('Vui lòng chọn số sao đánh giá');
                return;
            }
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Đang gửi...';
            
            try {
                const response = await fetch('<?= BASE_URL ?>?action=review-submit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        order_item_id: orderItemId,
                        order_id: orderId,
                        product_id: productId,
                        rating: rating,
                        comment: comment,
                        images: images
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Reload trang để hiển thị đánh giá đã gửi
                    location.reload();
                } else {
                    alert(data.message || 'Có lỗi xảy ra khi gửi đánh giá');
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    });
});
</script>
<?php endif; ?>

