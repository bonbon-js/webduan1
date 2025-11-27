<style>
    .post-detail-page {
        padding: 60px 0;
        min-height: 60vh;
    }
    
    .post-header {
        margin-bottom: 40px;
        text-align: center;
    }
    
    .post-title {
        font-family: 'Playfair Display', serif;
        font-size: 2.5rem;
        margin-bottom: 20px;
        color: #000;
        line-height: 1.3;
    }
    
    .post-meta {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 30px;
    }
    
    .post-image {
        width: 100%;
        max-width: 900px;
        height: 500px;
        object-fit: cover;
        border-radius: 12px;
        margin: 0 auto 40px;
        display: block;
    }
    
    .post-content {
        max-width: 800px;
        margin: 0 auto;
        font-size: 1.1rem;
        line-height: 1.8;
        color: #333;
    }
    
    .post-content p {
        margin-bottom: 20px;
    }
    
    .post-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 20px 0;
    }
    
    .post-content h2,
    .post-content h3 {
        font-family: 'Playfair Display', serif;
        margin-top: 40px;
        margin-bottom: 20px;
        color: #000;
    }
    
    .post-content h2 {
        font-size: 2rem;
    }
    
    .post-content h3 {
        font-size: 1.5rem;
    }
    
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #000;
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 30px;
        transition: all 0.3s;
    }
    
    .back-link:hover {
        color: #333;
        gap: 12px;
    }
</style>

<div class="container post-detail-page">
    <a href="<?= BASE_URL ?>?action=posts" class="back-link">
        <i class="bi bi-arrow-left"></i>
        Quay lại danh sách tin tức
    </a>

    <div class="post-header">
        <h1 class="post-title"><?= htmlspecialchars($post['title']) ?></h1>
        <div class="post-meta">
            <span>
                <i class="bi bi-calendar3 me-2"></i>
                <?= htmlspecialchars($post['date']) ?>
            </span>
        </div>
    </div>

    <?php if ($post['image']): ?>
        <img src="<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="post-image">
    <?php endif; ?>

    <div class="post-content">
        <?php if (!empty($post['excerpt'])): ?>
            <p class="lead" style="font-size: 1.2rem; color: #666; margin-bottom: 30px;">
                <?= htmlspecialchars($post['excerpt']) ?>
            </p>
        <?php endif; ?>
        
        <div>
            <?= nl2br(htmlspecialchars($post['content'] ?? '')) ?>
        </div>
    </div>
</div>

