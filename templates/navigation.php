<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $category): ?>
            <li class="nav__item">
                <a href="index.php?category_id=<?= $category['id'] ?? '' ?>">
                    <?= htmlspecialchars($category['title']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
