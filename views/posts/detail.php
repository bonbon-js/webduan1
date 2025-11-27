<?php
require_once PATH_MODEL . 'PostModel.php';
?>
<style>
    .post-detail-page {
        padding: 40px 0 80px;
        min-height: 60vh;
        background: #fff;
    }
    
    .breadcrumb-nav {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .breadcrumb-nav ol {
        margin: 0;
        padding: 0;
        list-style: none;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
    }
    
    .breadcrumb-nav li {
        display: flex;
        align-items: center;
    }
    
    .breadcrumb-nav a {
        color: #666;
        text-decoration: none;
        transition: color 0.3s;
    }
    
    .breadcrumb-nav a:hover {
        color: #000;
    }
    
    .breadcrumb-nav .separator {
        color: #ccc;
        margin: 0 4px;
    }
    
    .breadcrumb-nav .current {
        color: #000;
        font-weight: 600;
    }
    
    .post-hero {
        position: relative;
        margin-bottom: 50px;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    
    .post-hero-image {
        width: 100%;
        height: 500px;
        object-fit: cover;
        display: block;
    }
    
    .post-hero-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
        padding: 60px 40px 40px;
        color: #fff;
    }
    
    .post-title {
        font-family: 'Playfair Display', serif;
        font-size: clamp(2rem, 5vw, 3.5rem);
        margin-bottom: 20px;
        color: #fff;
        line-height: 1.2;
        font-weight: 700;
        text-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }
    
    .post-meta {
        display: flex;
        align-items: center;
        gap: 20px;
        color: rgba(255,255,255,0.9);
        font-size: 0.95rem;
    }
    
    .post-meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .post-meta-item i {
        font-size: 1rem;
    }
    
    .post-wrapper {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .post-excerpt {
        font-size: 1.3rem;
        line-height: 1.7;
        color: #555;
        margin-bottom: 40px;
        padding: 30px;
        background: #f8f9fa;
        border-left: 4px solid #000;
        border-radius: 8px;
        font-style: italic;
    }
    
    .post-content {
        font-size: 1.1rem;
        line-height: 1.9;
        color: #333;
        margin-bottom: 50px;
    }
    
    .post-content p {
        margin-bottom: 24px;
    }
    
    .post-content img {
        max-width: 100%;
        height: auto;
        border-radius: 12px;
        margin: 30px 0;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    
    .post-content h2,
    .post-content h3,
    .post-content h4 {
        font-family: 'Playfair Display', serif;
        margin-top: 50px;
        margin-bottom: 20px;
        color: #000;
        font-weight: 700;
    }
    
    .post-content h2 {
        font-size: 2.2rem;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .post-content h3 {
        font-size: 1.8rem;
    }
    
    .post-content h4 {
        font-size: 1.4rem;
    }
    
    .post-content ul,
    .post-content ol {
        margin: 20px 0;
        padding-left: 30px;
    }
    
    .post-content li {
        margin-bottom: 12px;
    }
    
    .post-content blockquote {
        border-left: 4px solid #000;
        padding: 20px 30px;
        margin: 30px 0;
        background: #f8f9fa;
        border-radius: 8px;
        font-style: italic;
        color: #555;
    }
    
    .post-content a {
        color: #000;
        text-decoration: underline;
        transition: color 0.3s;
    }
    
    .post-content a:hover {
        color: #666;
    }
    
    .post-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 30px 0;
        margin: 50px 0;
        border-top: 1px solid #f0f0f0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: #000;
        text-decoration: none;
        font-weight: 600;
        padding: 12px 24px;
        border: 2px solid #000;
        border-radius: 8px;
        transition: all 0.3s;
    }
    
    .back-button:hover {
        background: #000;
        color: #fff;
        transform: translateX(-5px);
    }
    
    .social-share {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .social-share-label {
        font-weight: 600;
        color: #666;
        font-size: 0.9rem;
    }
    
    .social-share-buttons {
        display: flex;
        gap: 10px;
    }
    
    .social-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        text-decoration: none;
        transition: transform 0.3s, box-shadow 0.3s;
        font-size: 1.1rem;
    }
    
    .social-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        color: #fff;
    }
    
    .social-btn.facebook {
        background: #1877f2;
    }
    
    .social-btn.twitter {
        background: #1da1f2;
    }
    
    .social-btn.linkedin {
        background: #0077b5;
    }
    
    .related-posts {
        margin-top: 80px;
        padding-top: 50px;
        border-top: 2px solid #f0f0f0;
    }
    
    .related-posts-title {
        font-family: 'Playfair Display', serif;
        font-size: 2rem;
        margin-bottom: 30px;
        color: #000;
        text-align: center;
    }
    
    .related-post-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.4s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .related-post-card:hover {
        border-color: #000;
        transform: translateY(-5px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    }
    
    .related-post-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        transition: transform 0.4s;
    }
    
    .related-post-card:hover img {
        transform: scale(1.05);
    }
    
    .related-post-card-body {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .related-post-card-date {
        font-size: 0.85rem;
        color: #666;
        margin-bottom: 10px;
    }
    
    .related-post-card-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 10px;
        color: #000;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .related-post-card-link {
        color: #000;
        text-decoration: none;
        margin-top: auto;
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: gap 0.3s;
    }
    
    .related-post-card-link:hover {
        gap: 10px;
        color: #000;
    }
    
    @media (max-width: 768px) {
        .post-hero-image {
            height: 300px;
        }
        
        .post-hero-overlay {
            padding: 40px 20px 30px;
        }
        
        .post-actions {
            flex-direction: column;
            gap: 20px;
            align-items: stretch;
        }
        
        .back-button {
            text-align: center;
            justify-content: center;
        }
        
        .social-share {
            justify-content: center;
        }
    }
</style>

<div class="post-detail-page">
    <div class="container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb-nav" aria-label="breadcrumb">
            <ol>
                <li><a href="<?= BASE_URL ?>">Trang chủ</a></li>
                <li class="separator">/</li>
                <li><a href="<?= BASE_URL ?>?action=posts">Tin tức</a></li>
                <li class="separator">/</li>
                <li class="current"><?= htmlspecialchars(mb_substr($post['title'], 0, 50)) ?><?= mb_strlen($post['title']) > 50 ? '...' : '' ?></li>
            </ol>
        </nav>

        <!-- Hero Image with Title -->
        <?php if ($post['image']): ?>
            <div class="post-hero">
                <img src="<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="post-hero-image">
                <div class="post-hero-overlay">
                    <h1 class="post-title"><?= htmlspecialchars($post['title']) ?></h1>
                    <div class="post-meta">
                        <div class="post-meta-item">
                            <i class="bi bi-calendar3"></i>
                            <span><?= htmlspecialchars($post['date']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="post-wrapper">
                <h1 class="post-title" style="color: #000; margin-bottom: 20px;"><?= htmlspecialchars($post['title']) ?></h1>
                <div class="post-meta" style="color: #666; margin-bottom: 30px;">
                    <div class="post-meta-item">
                        <i class="bi bi-calendar3"></i>
                        <span><?= htmlspecialchars($post['date']) ?></span>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="post-wrapper">
            <!-- Excerpt -->
            <?php if (!empty($post['excerpt'])): ?>
                <div class="post-excerpt">
                    <?= htmlspecialchars($post['excerpt']) ?>
                </div>
            <?php endif; ?>
            
            <!-- Content -->
            <div class="post-content">
                <?php 
                $content = $post['content'] ?? '';
                // Kiểm tra nếu content có HTML tags
                if (strip_tags($content) !== $content) {
                    // Nếu có HTML, hiển thị trực tiếp (đã được sanitize từ database)
                    echo $content;
                } else {
                    // Nếu là plain text, format với nl2br
                    echo nl2br(htmlspecialchars($content));
                }
                ?>
            </div>

            <!-- Actions -->
            <div class="post-actions">
                <a href="<?= BASE_URL ?>?action=posts" class="back-button">
                    <i class="bi bi-arrow-left"></i>
                    Quay lại danh sách
                </a>
                
                <div class="social-share">
                    <span class="social-share-label">Chia sẻ:</span>
                    <div class="social-share-buttons">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(BASE_URL . '?action=post-detail&id=' . $post['id']) ?>" 
                           target="_blank" 
                           class="social-btn facebook"
                           title="Chia sẻ lên Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?= urlencode(BASE_URL . '?action=post-detail&id=' . $post['id']) ?>&text=<?= urlencode($post['title']) ?>" 
                           target="_blank" 
                           class="social-btn twitter"
                           title="Chia sẻ lên Twitter">
                            <i class="bi bi-twitter"></i>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode(BASE_URL . '?action=post-detail&id=' . $post['id']) ?>" 
                           target="_blank" 
                           class="social-btn linkedin"
                           title="Chia sẻ lên LinkedIn">
                            <i class="bi bi-linkedin"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Posts -->
        <?php
        // Lấy 3 bài viết mới nhất (trừ bài hiện tại)
        $postModel = new PostModel();
        $allPosts = $postModel->getAllPosts(1, 4);
        $relatedPosts = array_filter($allPosts['posts'], function($p) use ($post) {
            return $p['id'] != $post['id'];
        });
        $relatedPosts = array_slice($relatedPosts, 0, 3);
        ?>
        
        <?php if (!empty($relatedPosts)): ?>
            <div class="related-posts">
                <h2 class="related-posts-title">Bài viết liên quan</h2>
                <div class="row g-4">
                    <?php foreach ($relatedPosts as $relatedPost): ?>
                        <div class="col-12 col-md-6 col-lg-4">
                            <article class="related-post-card">
                                <a href="<?= BASE_URL ?>?action=post-detail&id=<?= $relatedPost['id'] ?>">
                                    <img src="<?= htmlspecialchars($relatedPost['image']) ?>" alt="<?= htmlspecialchars($relatedPost['title']) ?>">
                                </a>
                                <div class="related-post-card-body">
                                    <p class="related-post-card-date">
                                        <i class="bi bi-calendar3 me-2"></i><?= htmlspecialchars($relatedPost['date']) ?>
                                    </p>
                                    <h3 class="related-post-card-title">
                                        <a href="<?= BASE_URL ?>?action=post-detail&id=<?= $relatedPost['id'] ?>" class="text-decoration-none text-dark">
                                            <?= htmlspecialchars($relatedPost['title']) ?>
                                        </a>
                                    </h3>
                                    <a href="<?= BASE_URL ?>?action=post-detail&id=<?= $relatedPost['id'] ?>" class="related-post-card-link">
                                        Đọc thêm <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

