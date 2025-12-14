<div class="container mx-auto px-4 py-8 max-w-7xl">
    <div class="text-center mb-12">
        <h1 class="text-3xl md:text-4xl font-serif font-bold mb-4">Tin Tức</h1>
        <p class="text-gray-500">Cập nhật những xu hướng thời trang mới nhất</p>
    </div>

    <?php if (empty($posts)): ?>
        <div class="text-center py-20 bg-gray-50 rounded-lg">
            <i class="bi bi-newspaper text-6xl text-gray-300 mb-4 block"></i>
            <h3 class="text-xl font-bold text-gray-800">Chưa có tin tức nào</h3>
            <p class="text-gray-500 mt-2">Vui lòng quay lại sau.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($posts as $post): ?>
                <div class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 flex flex-col h-full">
                    <a href="<?= BASE_URL ?>?action=post-detail&id=<?= $post['post_id'] ?>" class="block overflow-hidden aspect-[4/3] relative">
                        <?php 
                            $imgSrc = !empty($post['thumbnail']) ? getProductImageUrl($post['thumbnail']) : BASE_URL . 'assets/images/default.jpg';
                        ?>
                        <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </a>
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex items-center text-sm text-gray-500 mb-3">
                            <i class="bi bi-calendar3 mr-2 text-primary"></i>
                            <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                        </div>
                        <h3 class="text-xl font-bold mb-3 line-clamp-2 group-hover:text-primary transition-colors">
                            <a href="<?= BASE_URL ?>?action=post-detail&id=<?= $post['post_id'] ?>">
                                <?= htmlspecialchars($post['title']) ?>
                            </a>
                        </h3>
                        <p class="text-gray-600 mb-4 line-clamp-3 flex-1 text-sm leading-relaxed">
                            <?= htmlspecialchars($post['excerpt'] ?? '') ?>
                        </p>
                        <a href="<?= BASE_URL ?>?action=post-detail&id=<?= $post['post_id'] ?>" class="inline-flex items-center text-primary font-semibold hover:text-black mt-auto group/link">
                            Đọc thêm <i class="bi bi-arrow-right ml-2 transform group-hover/link:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="mt-12 flex justify-center">
                <nav aria-label="Phân trang tin tức">
                    <ul class="flex gap-2">
                        <?php if ($currentPage > 1): ?>
                            <li>
                                <a class="w-10 h-10 flex items-center justify-center rounded-full border border-gray-200 hover:bg-black hover:text-white transition-colors" href="<?= BASE_URL ?>?action=posts&page=<?= $currentPage - 1 ?>">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i == 1 || $i == $totalPages || ($i >= $currentPage - 2 && $i <= $currentPage + 2)): ?>
                                <li>
                                    <a class="w-10 h-10 flex items-center justify-center rounded-full border border-gray-200 <?= $i == $currentPage ? 'bg-black text-white border-black' : 'hover:bg-black hover:text-white transition-colors' ?>" href="<?= BASE_URL ?>?action=posts&page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php elseif ($i == $currentPage - 3 || $i == $currentPage + 3): ?>
                                <li>
                                    <span class="w-10 h-10 flex items-center justify-center text-gray-400">...</span>
                                </li>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($currentPage < $totalPages): ?>
                            <li>
                                <a class="w-10 h-10 flex items-center justify-center rounded-full border border-gray-200 hover:bg-black hover:text-white transition-colors" href="<?= BASE_URL ?>?action=posts&page=<?= $currentPage + 1 ?>">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

