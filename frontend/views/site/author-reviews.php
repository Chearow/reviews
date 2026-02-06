<?php
/** @var User $user */

/** @var Review[] $reviews */

use common\models\Review;
use common\models\User;

$this->title = 'Отзывы автора: ' . $user->fio;
?>

<h2 class="mb-4"><?= htmlspecialchars($this->title) ?></h2>

<?php
if (empty($reviews)) : ?>
    <p>У этого автора пока нет отзывов.</p>
<?php
endif; ?>

<?php
foreach ($reviews as $review) : ?>
    <div class="card mb-4 shadow-sm">
        <div class="card-header d-flex justify-content-between">
            <strong><?= htmlspecialchars($review->title) ?></strong>
            <span class="text-muted">Рейтинг: <?= $review->rating ?></span>
        </div>

        <div class="card-body">
            <p><?= nl2br(htmlspecialchars($review->text)) ?></p>

            <?php
            if (!empty($review->img)) : ?>
                <img src="<?= $review->img ?>" alt="<?= htmlspecialchars($review->title) ?>"
                     class="img-fluid rounded mb-3" style="max-width: 200px;">
            <?php
            endif; ?>

            <p><strong>Города:</strong>
                <?php
                foreach ($review->cities as $city) : ?>
                    <span class="badge bg-info text-dark me-1">
                        <?= htmlspecialchars($city->name) ?>
                    </span>
                <?php
                endforeach; ?>
            </p>

            <p class="text-muted small mb-0">
                <?= date('d.m.Y H:i', $review->created_at) ?>
            </p>
        </div>
    </div>
<?php
endforeach; ?>
