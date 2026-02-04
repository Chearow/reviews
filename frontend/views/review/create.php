<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\City;

/** @var $model \frontend\models\ReviewForm */

$this->title = 'Создать отзыв';
?>

<h2><?= $this->title ?></h2>

<?php $form = ActiveForm::begin([
        'id' => 'review-form',
        'options' => ['enctype' => 'multipart/form-data'],
]); ?>

<?= $form->field($model, 'title') ?>
<?= $form->field($model, 'text')->textarea() ?>
<?= $form->field($model, 'rating')->dropDownList([1,2,3,4,5]) ?>
<?= $form->field($model, 'city_ids')->widget(Select2::class, [
    'data' => ArrayHelper::map(City::find()->all(), 'id', 'name'),
    'options' => [
        'placeholder' => 'Выберите города...',
        'multiple' => true,
    ],
    'pluginOptions' => [
        'allowClear' => true,
        'minimumInputLength' => 2,
        'ajax' => [
            'url' =>'/city/search',
            'dataType' => 'json',
            'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }')
        ],
    ],
]) ?>
<?= $form->field($model, 'imageFile')->fileInput(); ?>

<div class="form-group">
    <?= Html::button('Сохранить', ['class' => 'btn btn-primary', 'id' => 'save-review']) ?>
</div>

<?php ActiveForm::end(); ?>

<div id="loader" style="display:none;">Сохраняем...</div>

<script>
$('#save-review').on('click', function () {
    let formData = new FormData($('#review-form')[0]);

    $('#loader').show();

    $.ajax({
        url: '/review/create-ajax',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            $('#loader').hide();

            if (response.success) {
                alert('Отзыв создан!');
                window.location.href = '/'
            } else {
                console.log(response.errors);
                alert('Ошибка. Проверьте форму.');
            }
        }
    });
});
</script>