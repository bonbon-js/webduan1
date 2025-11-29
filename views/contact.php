<!-- Trang Liên Hệ -->
<section class="contact-page py-5">
    <div class="container">
        <!-- Header Section -->
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold mb-3">Liên Hệ Với Chúng Tôi</h1>
            <p class="lead text-muted">Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn</p>
        </div>

        <div class="row g-5">
            <!-- Thông tin liên hệ -->
            <div class="col-lg-4">
                <div class="contact-info-card h-100 p-4">
                    <h3 class="fw-bold mb-4">Thông Tin Liên Hệ</h3>
                    
                    <div class="contact-item mb-4">
                        <div class="contact-icon">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div class="contact-details">
                            <h5 class="fw-semibold mb-1">Địa Chỉ</h5>
                            <p class="text-muted mb-0">245 Đường Thanh Chương, Phố Tân Trọng<br>Phường Quảng Phú, Hà Nội</p>
                        </div>
                    </div>

                    <div class="contact-item mb-4">
                        <div class="contact-icon">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <div class="contact-details">
                            <h5 class="fw-semibold mb-1">Điện Thoại</h5>
                            <p class="text-muted mb-0">
                                <a href="tel:0393561314" class="text-decoration-none text-muted">0393 561 314</a>
                            </p>
                        </div>
                    </div>

                    <div class="contact-item mb-4">
                        <div class="contact-icon">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div class="contact-details">
                            <h5 class="fw-semibold mb-1">Email</h5>
                            <p class="text-muted mb-0">
                                <a href="mailto:le3221981@gmail.com" class="text-decoration-none text-muted">le3221981@gmail.com</a>
                            </p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="bi bi-clock-fill"></i>
                        </div>
                        <div class="contact-details">
                            <h5 class="fw-semibold mb-1">Giờ Làm Việc</h5>
                            <p class="text-muted mb-0">
                                Thứ 2 - Thứ 6: 9:00 - 18:00<br>
                                Thứ 7 - Chủ Nhật: 10:00 - 17:00
                            </p>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div class="mt-4 pt-4 border-top">
                        <h5 class="fw-semibold mb-3">Theo Dõi Chúng Tôi</h5>
                        <div class="social-links-contact">
                            <a href="#" class="social-link" title="Facebook"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="social-link" title="Instagram"><i class="bi bi-instagram"></i></a>
                            <a href="#" class="social-link" title="TikTok"><i class="bi bi-tiktok"></i></a>
                            <a href="#" class="social-link" title="YouTube"><i class="bi bi-youtube"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form liên hệ -->
            <div class="col-lg-8">
                <div class="contact-form-card p-4">
                    <h3 class="fw-bold mb-4">Gửi Tin Nhắn</h3>
                    <form id="contactForm" method="POST" action="<?= BASE_URL ?>?action=contact-submit">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">Họ và Tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required placeholder="Nhập họ và tên của bạn">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required placeholder="your.email@example.com">
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">Số Điện Thoại</label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="0123 456 789">
                            </div>
                            <div class="col-md-6">
                                <label for="subject" class="form-label fw-semibold">Chủ Đề</label>
                                <select class="form-select" id="subject" name="subject">
                                    <option value="">Chọn chủ đề</option>
                                    <option value="product">Câu hỏi về sản phẩm</option>
                                    <option value="order">Câu hỏi về đơn hàng</option>
                                    <option value="return">Đổi trả sản phẩm</option>
                                    <option value="feedback">Góp ý</option>
                                    <option value="other">Khác</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="message" class="form-label fw-semibold">Nội Dung Tin Nhắn <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="message" name="message" rows="6" required placeholder="Nhập nội dung tin nhắn của bạn..."></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-dark btn-lg px-5" id="submitBtn">
                                    <i class="bi bi-send me-2"></i>Gửi Tin Nhắn
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Map Section (Optional) -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="map-section">
                    <h3 class="fw-bold mb-4 text-center">Vị Trí Cửa Hàng</h3>
                    <div class="map-placeholder">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.096466609128!2d105.84115931540247!3d21.02816378599847!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab9bd9861ca1%3A0xe7887f7b6ca21b87!2zSMOgIE5vaQ!5e0!3m2!1svi!2s!4v1234567890" 
                            width="100%" 
                            height="400" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    const submitBtn = document.getElementById('submitBtn');
    
    contactForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(contactForm);
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Đang gửi...';
        
        try {
            const response = await fetch('<?= BASE_URL ?>?action=contact-submit', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Hiển thị thông báo thành công
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-dark alert-dismissible fade show';
                alertDiv.innerHTML = `
                    <strong>Thành công!</strong> ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                contactForm.insertBefore(alertDiv, contactForm.firstChild);
                
                // Reset form
                contactForm.reset();
            } else {
                // Hiển thị thông báo lỗi
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                alertDiv.innerHTML = `
                    <strong>Lỗi!</strong> ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                contactForm.insertBefore(alertDiv, contactForm.firstChild);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Có lỗi xảy ra. Vui lòng thử lại sau.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
});
</script>

