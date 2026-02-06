<?php
/** @var Review[] $reviews */

use common\models\Review;
use yii\helpers\Url;

$this->title = 'Отзывы вашего города';
?>

<h2 class="mb-4"><?= $this->title ?></h2>

<?php if (empty($reviews)) : ?>
    <p>Пока нет отзывов.</p>
<?php endif; ?>

<?php foreach ($reviews as $review) : ?>
    <div class="card mb-4 shadow-sm">
        <div class="card-header d-flex justify-content-between">
            <strong><?= htmlspecialchars($review->title) ?></strong>
            <span class="text-muted">Рейтинг: <?= $review->rating ?></span>
        </div>

        <div class="card-body">
            <p><?= nl2br(htmlspecialchars($review->text)) ?></p>

            <?php if ($review->img): ?>
                <img src="<?= $review->img ?>" alt="<?= htmlspecialchars($review->title) ?>"
                     class="img-fluid rounded mb-3" style="max-width: 200px;">
            <?php endif; ?>

            <p>
                Автор:
                <a href="#" class="author-link" data-id="<?= $review->author_id ?>">
                    <?= htmlspecialchars($review->user->fio) ?>
                </a>
            </p>

            <?php if (!Yii::$app->user->isGuest && $review->author_id === Yii::$app->user->id): ?>
                <div class="mb-3">
                    <a href="<?= Url::to(['review/update', 'id' => $review->id]) ?>"
                       class="btn btn-warning btn-sm">Редактировать</a>

                    <a href="<?= Url::to(['review/delete', 'id' => $review->id]) ?>"
                       class="btn btn-danger btn-sm"
                       data-confirm="Удалить отзыв?"
                       data-method="post">
                       Удалить
                    </a>
                </div>
            <?php endif; ?>

            <p class="text-muted small mb-0">
                <?= date('d.m.Y H:i', $review->created_at) ?>
            </p>
        </div>
    </div>
<?php endforeach; ?>

<?php
$authorInfoUrl = Url::to(['/site/author-info']);
$js = <<<JS
$('.author-link').on('click', function(e) {
    e.preventDefault();
    var authorId = $(this).data('id');

    $.ajax({
        url: '$authorInfoUrl',
        method: 'GET',
        data: { id: authorId },
        success: function(response) {
            if (response.success) {
                $('#authorModalLabel').text(response.data.fio);
                $('#authorEmail').text(response.data.email);
                $('#authorPhone').text(response.data.phone);
                $('#authorReviewsLink').attr('href', response.data.reviewsUrl);
                var modal = new bootstrap.Modal(document.getElementById('authorModal'));
                modal.show();
            } else {
                alert(response.message);
            }
        },
        error: function() {
            alert('Ошибка при загрузке данных автора');
        }
    });
});
JS;

$this->registerJs($js);
?>

<div class="modal fade" id="authorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="authorModalLabel">Автор</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p><strong>Email:</strong> <span id="authorEmail"></span></p>
                <p><strong>Телефон:</strong> <span id="authorPhone"></span></p>
            </div>

            <div class="modal-footer">
                <a id="authorReviewsLink" href="#" class="btn btn-primary">Все отзывы автора</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
