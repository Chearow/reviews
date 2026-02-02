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
                <p><img src="<?= $review->img ?>" alt="<?= htmlspecialchars($review->title) ?>" style="max-width: 200px;"></p>
            <?php endif; ?>

            <p>
                Автор:
                <a href="#" class="author-link" data-id="<?= $review->author_id ?>"><?= $review->user->fio ?> </a>
            </p>

            <p><small><?= date('d.m.Y H:i', $review->created_at) ?></small></p>
        </div>
    </div>
<?php endforeach; ?>

<?php
$authorInfoUrl = \yii\helpers\Url::to(['/site/author-info']);
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
                $('#authorModal').modal('show');
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

<div class="modal fade" id="authorModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="authorModalLabel">Автор</h4>
            </div>

            <div class="modal-body">
                <p><strong>Email:</strong> <span id="authorEmail"></span></p>
                <p><strong>Телефон:</strong> <span id="authorPhone"></span></p>
            </div>

            <div class="modal-footer">
                <a id="authorReviewsLink" href="#" class="btn btn-primary">Все отзывы автора</a>
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
