<?php
/** @var \common\models\Review[] $reviews */
?>

<h2>Отзывы вашего города</h2>

<?php if (empty($reviews)) : ?>
    <p>Пока нет отзывов.</p>
<?php endif; ?>

<?php foreach ($reviews as $review) : ?>
    <div class ="panel panel-default" style="margin-bottom: 20px;">
        <div class="panel-heading">
            <strong><?= $review->title ?></strong>
            <span class="pull-right">Рейтинг: <?= $review->rating ?></span>
        </div>

        <div class ="panel-body">
            <p><?= $review->text ?></p>

            <?php if ($review->img): ?>
                <p><img src="<?= $review->img ?>" alt"<?= htmlspecialchars($review->title) ?>" style="max-width: 200px;"></p>
            <?php endif; ?>

            <p>
                Автор:
                <a href="#" class="author-link" data-id="<?= $review->author_id ?>"><?= $review->user->fio ?> </a>
            </p>

            <p><small><?= date('d.m.Y H:i', $review->created_at) ?></small></p>
        </div>
    </div>
<?php endforeach; ?>