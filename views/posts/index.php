<style>
    .news-page {
        padding: 60px 0;
        min-height: 60vh;
    }
    
    .news-header {
        margin-bottom: 40px;
        text-align: center;
    }
    
    .news-title {
        font-family: 'Playfair Display', serif;
        font-size: 2.5rem;
        margin-bottom: 10px;
        color: #000;
    }
    
    .news-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 0;
        text-align: left;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        box-shadow: none;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .news-card:hover {
        border-color: #000;
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .news-card img {
        width: 100%;
        height: 280px;
        object-fit: cover;
        transition: all 0.4s;
    }
    
    .news-card:hover img {
        transform: scale(1.05);
    }
    
    .news-card-body {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .news-card-date {
        font-size: 0.85rem;
        color: #666;
        margin-bottom: 10px;
    }
    
    .news-card-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 10px;
        color: #000;
        line-height: 1.4;
    }
    
    .news-card-excerpt {
        font-size: 0.9rem;
        color: #666;
        line-height: 1.6;
        flex: 1;
        margin-bottom: 15px;
    }
    
    .news-card-link {
        color: #000;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.3s;
    }
    
    .news-card-link:hover {
        color: #333;
        gap: 10px;
    }
    
    .pagination {
        margin-top: 40px;
        justify-content: center;
    }
    
    .pagination .page-link {
        color: #000;
        border-color: #000;
        padding: 10px 15px;
    }
    
    .pagination .page-item.active .page-link {
        background: #000;
        border-color: #000;
        color: #fff;
    }
    
    .pagination .page-link:hover {
        background: #f5f5f5;
        border-color: #000;
    }
</style>

<div class="container news-page">
    <div class="news-header">
        <h1 class="news-title">Tin Tức</h1>
        <p class="text-muted">Cập nhật những xu hướng thời trang mới nhất</p>
    </div>

    <?php if (empty($posts)): ?>
        <div class="text-center py-5">
            <i class="bi bi-newspaper" style="font-size: 4rem; color: #999;"></i>
            <h3 class="mt-3">Chưa có tin tức nào</h3>
            <p class="text-muted">Vui lòng quay lại sau.</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($posts as $post): ?>
                <div class="col-12 col-sm-6 col-lg-4">
                    <article class="news-card">
                        <a href="<?= BASE_URL ?>?action=post-detail&id=<?= $post['id'] ?>">
                            <img src="<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>">
                        </a>
                        <div class="news-card-body">
                            <p class="news-card-date">
                                <i class="bi bi-calendar3 me-2"></i><?= htmlspecialchars($post['date']) ?>
                            </p>
                            <h3 class="news-card-title">
                                <a href="<?= BASE_URL ?>?action=post-detail&id=<?= $post['id'] ?>" class="text-decoration-none text-dark">
                                    <?= htmlspecialchars($post['title']) ?>
                                </a>
                            </h3>
                            <p class="news-card-excerpt"><?= htmlspecialchars($post['excerpt']) ?></p>
                            <a href="<?= BASE_URL ?>?action=post-detail&id=<?= $post['id'] ?>" class="news-card-link">
                                Đọc thêm <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <nav aria-label="Phân trang tin tức">
                <ul class="pagination">
                    <?php if ($currentPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= BASE_URL ?>?action=posts&page=<?= $currentPage - 1 ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == 1 || $i == $totalPages || ($i >= $currentPage - 2 && $i <= $currentPage + 2)): ?>
                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="<?= BASE_URL ?>?action=posts&page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php elseif ($i == $currentPage - 3 || $i == $currentPage + 3): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($currentPage < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= BASE_URL ?>?action=posts&page=<?= $currentPage + 1 ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

