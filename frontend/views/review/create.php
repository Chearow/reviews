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
        'enableClientValidation' => false,
        'enableAjaxValidation' => false,
        'options' => ['enctype' => 'multipart/form-data'],
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
            'noResults' => new \yii\web\JsExpression("
                function() {
                    return '<button type=\"button\" class=\"btn btn-link p-0\" id=\"add-city-btn\">Добавить город</button>';
                }
            "),
        ],
        'escapeMarkup' => new \yii\web\JsExpression('function (markup) {return markup; }'),
        'ajax' => [
            'url' =>'/city/search',
            'dataType' => 'json',
            'delay' => 250,
            'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }'),
            'processResults' => new \yii\web\JsExpression('function(data) {return {results: data};}')
        ],
    ],
]) ?>
<?= $form->field($model, 'imageFile')->fileInput(); ?>

<div class="form-group">
    <?= Html::button('Сохранить', ['class' => 'btn btn-primary', 'id' => 'save-review']) ?>
</div>

<?php ActiveForm::end(); ?>

<div id="loader" style="display:none;">Сохраняем...</div>

<?php
$js = <<<JS
$(document).on('click', '#add-city-btn', function() {
    let query = $('.select2-search__field').val();
    
    if(!query) {
        alert('Введите название города');
        return;
    }
    
    $.ajax({
        url: '/city/create-ajax',
        type: 'POST',
        data: {query: query},
        success: function(response) {
            if (response.success) {
                let newOption = new Option(response.text, response.id, true, true);
                $('.select2-search__field').val('');
                $('#city-select').append(newOption).trigger('change');
            } else {
                alert(response.message || 'Ошибка при создании города');
            }
        }
    });
});

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
                window.location.href = '/';
            } else {
                alert('Ошибка. Проверьте форму.');
            }
        }
    });
});
JS;

$this->registerJs($js);
?>