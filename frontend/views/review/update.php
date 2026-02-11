<?php

use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\web\JsExpression;
use frontend\assets\ReviewAsset;

/** @var $model common\models\Review */

ReviewAsset::register($this);

$this->title = 'Редактировать отзыв';
?>

    <h2><?= Html::encode($this->title) ?></h2>

<?php
$form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data']
]); ?>

<?= $form->field($model, 'title') ?>
<?= $form->field($model, 'text')->textarea() ?>
<?= $form->field($model, 'rating')->dropDownList([
    1 => 1,
    2 => 2,
    3 => 3,
    4 => 4,
    5 => 5,
]) ?>

<?= $form->field($model, 'city_ids')->widget(Select2::class, [
    'options' => [
        'placeholder' => 'Выберите города...',
        'multiple' => true,
        'id' => 'city-select'
    ],
    'pluginOptions' => [
        'allowClear' => true,
        'minimumInputLength' => 2,
        'language' => [
            'noResults' => new JsExpression(
                "
                function() {
                    return '<button type=\"button\" class=\"btn btn-link p-0\" id=\"add-city-btn\">Добавить город</button>';
                }
            "
            ),
        ],
        'escapeMarkup' => new JsExpression('function (markup) {return markup; }'),
        'ajax' => [
            'url' => '/city/search',
            'dataType' => 'json',
            'delay' => 250,
            'data' => new JsExpression('function(params) { return {q:params.term}; }'),
            'processResults' => new JsExpression('function(data) {return {results: data};}')
        ],
    ],
]) ?>


<?php
if ($model->review && $model->review->img): ?>
    <div class="mb-3">
        <p>Текущее изображение:</p>
        <img src="<?= $model->review->img ?>" style="max-width:200px" class="img-thumbnail mb-2">
    </div>
<?php
endif; ?>

<?= $form->field($model, 'imageFile')->fileInput() ?>

    <div class="form-group mt-3">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

<?php
ActiveForm::end(); ?>