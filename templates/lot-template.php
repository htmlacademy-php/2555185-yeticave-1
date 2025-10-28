<section class="lot-item container">
    <h2><?= htmlspecialchars($lot['lots_title']) ?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="<?= htmlspecialchars($lot['image']) ?>" width="730" height="548"
                    alt="<?= htmlspecialchars($lot['lot_title']) ?>">
            </div>
            <p class="lot-item__category">Категория: <span><?= htmlspecialchars($lot['category_title']) ?></span></p>
            <p class="lot-item__description"><?= htmlspecialchars($lot['description']) ?></p>
        </div>
        <div class="lot-item__right">
            <div class="lot-item__state">

                <?php $time = get_dt_range($lot['end_date']); ?>
                <div class="lot-item__timer timer <?php if ($time[0] < 24): ?> timer--finishing<?php endif; ?>">
                    <?= str_pad($time[0], 2, '0', STR_PAD_LEFT) . ': ' . str_pad($time[1], 2, '0', STR_PAD_LEFT) ?>
                </div>


                <div class="lot-item__cost-state">
                    <div class="lot-item__rate">
                        <span class="lot-item__amount">Текущая цена</span>
                        <span class="lot-item__cost"><?= formatPrice($currentPrice) ?></span>
                    </div>
                    <div class="lot-item__min-cost">
                        Мин. ставка
                        <span><?= formatPrice($minBid) ?></span>
                    </div>
                </div>
                <?php if (isset($_SESSION['user'])): ?>

                    <form class="lot-item__form <?= !empty($_SESSION['bid_errors']) ? 'form--invalid' : '' ?>"
                        action="add-bet.php" method="post" autocomplete="off">
                        <input type="hidden" name="lot_id" value="<?= $lot['id'] ?>">
                        <p
                            class="lot-item__form-item form__item <?= isset($_SESSION['bid_errors']['cost']) ? 'form__item--invalid' : '' ?>">
                            <label for="cost">Ваша ставка</label>
                            <input id="cost" type="text" name="cost"
                                placeholder="<?= formatPrice($minBid) ?>"
                                value="<?= htmlspecialchars($_POST['cost'] ?? '') ?>">
                            <span class="form__error"><?= $_SESSION['bid_errors']['cost'] ?? '' ?></span>
                        </p>
                        <button type="submit" class="button">Сделать ставку</button>
                    </form>
                <?php endif; ?>
            </div>
<div class="history">
    <h3>История ставок (<span><?= count($bids) ?></span>)</h3>
    <?php if (!empty($bids)): ?>
        <table class="history__list">
            <?php foreach ($bids as $bid): ?>
                <tr class="history__item">
                    <td class="history__name"><?= htmlspecialchars($bid['user_name']) ?></td>
                    <td class="history__price"><?= formatPrice($bid['amount']) ?></td>
                    <td class="history__time"><?= formatTimeAgo($bid['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Ставок пока нет</p>
    <?php endif; ?>
</div>
        </div>
    </div>
</section>
