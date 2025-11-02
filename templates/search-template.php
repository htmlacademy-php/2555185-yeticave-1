<div class="container">
    <section class="lots">
        <h2>Результаты поиска по запросу «<span><?= htmlspecialchars($search) ?></span>»</h2>

        <?php if (empty($lots)): ?>
            <p>Ничего не найдено по вашему запросу</p>
        <?php else: ?>
            <ul class="lots__list">
                <?php foreach ($lots as $ads): ?>
                    <li class="lots__item lot">
                        <div class="lot__image">
                            <img src="<?= htmlspecialchars($ads['image']) ?>" width="350" height="260"
                                alt="<?= htmlspecialchars($ads['lot_title']) ?>">
                        </div>
                        <div class="lot__info">
                            <span class="lot__category"><?= htmlspecialchars($ads['category_title']) ?></span>
                            <h3 class="lot__title">
                                <a class="text-link" href="lot.php?id=<?= $ads['lot_id'] ?>">
                                    <?= htmlspecialchars($ads['lot_title']) ?>
                                </a>
                            </h3>
                            <div class="lot__state">
                                <div class="lot__rate">
                                    <span class="lot__amount">Стартовая цена</span>
                                    <span class="lot__cost"><?= formatPrice($ads['start_price']) ?></span>
                                </div>
                                <?php $time = get_dt_range($ads['end_date']); ?>
                                <div class="lot__timer timer <?php if ($time[0] < 24): ?> timer--finishing<?php endif; ?>">
                                    <?= str_pad($time[0], 2, '0', STR_PAD_LEFT) . ':' . str_pad($time[1], 2, '0', STR_PAD_LEFT) ?>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <?php if (isset($pages_count) && $pages_count > 1): ?>
        <ul class="pagination-list">
            <li class="pagination-item pagination-item-prev">
                <?php if ($current_page > 1): ?>
                    <a href="search.php?search=<?= urlencode($search) ?>&page=<?= $current_page - 1 ?>">Назад</a>
                <?php else: ?>
                    <a>Назад</a>
                <?php endif; ?>
            </li>

            <?php for ($i = 1; $i <= $pages_count; $i++): ?>
                <li class="pagination-item <?= ($i === $current_page) ? 'pagination-item-active' : '' ?>">
                    <a href="search.php?search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <li class="pagination-item pagination-item-next">
                <?php if ($current_page < $pages_count): ?>
                    <a href="search.php?search=<?= urlencode($search) ?>&page=<?= $current_page + 1 ?>">Вперед</a>
                <?php else: ?>
                    <a>Вперед</a>
                <?php endif; ?>
            </li>
        </ul>
    <?php endif; ?>
</div>
