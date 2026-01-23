<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\ReviewCity $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="review-city-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'review_id')->textInput() ?>

    <?= $form->field($model, 'city_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
