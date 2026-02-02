<?php
/** @var \common\models\User $user */
/** @var \common\models\Review[] $reviews */
?>

<h2>Отзывы автора: <?= htmlspecialchars($user->fio) ?></h2>

<?php if (empty($reviews)) : ?>
    <p>У этого автора пока нет отзывов.</p>
<?php endif; ?>

<?php foreach ($reviews as $review) : ?>
    <div class="panel panel-default" style="margin-bottom: 20px;">
        <div class="panel-heading">
            <strong><?= htmlspecialchars($review->title) ?></strong>
            <span class="pull-right">Рейтинг: <?= $review->rating ?></span>
        </div>

        <div class="panel-body">
            <p><?= nl2br(htmlspecialchars($review->text)) ?></p>

            <?php if (!empty($review->img)) : ?>
                <p><img src="<?= $review->img ?>" alt="<?= htmlspecialchars($review->title) ?>"
                    style="max-width: 200px;"></p>
            <?php endif; ?>

            <p><strong>Города:</strong>
                <?php foreach ($review->cities as $city) : ?>
                    <span class="label label-info" style="margin-right: 5px;">
                        <?= htmlspecialchars($city->name) ?>
                    </span>
                <?php endforeach; ?>
            </p>

            <p><small><?= date('d.m.Y H:i', $review->created_at) ?></small></p>
        </div>
    </div>
<?php endforeach; ?>